<?php

declare(strict_types=1);

use App\Models\Master;
use App\Models\Payment;
use App\Models\Referral;
use App\Models\ReferralEarning;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Database\UniqueConstraintViolationException;

it('attaches a referral once and returns the existing attachment on repeat', function (): void {
    $referrer = Master::create(['name' => 'Referrer', 'referral_code' => 'REF123']);
    $referred = Master::create(['name' => 'Referred', 'referral_code' => 'NEW123']);

    $firstResponse = $this->withHeader('X-Master-Id', (string) $referred->id)
        ->postJson('/api/referrals/attach', ['code' => $referrer->referral_code]);

    $firstResponse
        ->assertCreated()
        ->assertJsonPath('data.referrer_master_id', $referrer->id)
        ->assertJsonPath('data.status', Referral::STATUS_PENDING);

    $this->withHeader('X-Master-Id', (string) $referred->id)
        ->postJson('/api/referrals/attach', ['code' => $referrer->referral_code])
        ->assertOk();

    expect(Referral::query()->where('referred_master_id', $referred->id)->count())->toBe(1);
});

it('enforces a single referral per referred master in the database', function (): void {
    $firstReferrer = Master::create(['name' => 'First referrer', 'referral_code' => 'REF123']);
    $secondReferrer = Master::create(['name' => 'Second referrer', 'referral_code' => 'REF456']);
    $referred = Master::create(['name' => 'Referred', 'referral_code' => 'NEW123']);

    Referral::create([
        'referrer_master_id' => $firstReferrer->id,
        'referred_master_id' => $referred->id,
        'status' => Referral::STATUS_PENDING,
    ]);

    expect(fn() => Referral::create([
        'referrer_master_id' => $secondReferrer->id,
        'referred_master_id' => $referred->id,
        'status' => Referral::STATUS_PENDING,
    ]))->toThrow(UniqueConstraintViolationException::class);
});

it('rejects an unknown referral code and self referral', function (): void {
    $master = Master::create(['name' => 'Master', 'referral_code' => 'OWN123']);

    $this->withHeader('X-Master-Id', (string) $master->id)
        ->postJson('/api/referrals/attach')
        ->assertUnprocessable();

    $this->withHeader('X-Master-Id', (string) $master->id)
        ->postJson('/api/referrals/attach', ['code' => 'MISSING'])
        ->assertUnprocessable();

    $this->withHeader('X-Master-Id', (string) $master->id)
        ->postJson('/api/referrals/attach', ['code' => $master->referral_code])
        ->assertUnprocessable();

    expect(Referral::query()->count())->toBe(0);
});

it('returns validation errors as JSON when the request accepts HTML', function (): void {
    $master = Master::create(['name' => 'Master', 'referral_code' => 'OWN123']);

    $this->call('POST', '/api/referrals/attach', [], [], [], [
        'HTTP_ACCEPT' => 'text/html',
        'HTTP_X_MASTER_ID' => (string) $master->id,
    ])
        ->assertUnprocessable()
        ->assertHeader('Content-Type', 'application/json')
        ->assertJsonValidationErrors(['code']);
});

it('requires a known current master for referral routes', function (): void {
    $this->getJson('/api/referrals/my')->assertUnauthorized();

    $this->withHeader('X-Master-Id', '999999')
        ->getJson('/api/referrals/earnings')
        ->assertUnauthorized();

    $this->withHeader('X-Master-Id', '999999')
        ->postJson('/api/referrals/attach', ['code' => 'ANY123'])
        ->assertUnauthorized();
});

it('returns referred masters with individual earnings and an earnings summary', function (): void {
    $referrer = Master::create(['name' => 'Referrer', 'referral_code' => 'REF123']);
    $pendingMaster = Master::create(['name' => 'Pending referral', 'referral_code' => 'PEN123']);
    $paidMaster = Master::create(['name' => 'Paid referral', 'referral_code' => 'PAI123']);
    $unqualifiedMaster = Master::create(['name' => 'Unqualified referral', 'referral_code' => 'UNQ123']);

    $pendingReferral = Referral::create([
        'referrer_master_id' => $referrer->id,
        'referred_master_id' => $pendingMaster->id,
        'status' => Referral::STATUS_REWARDED,
    ]);
    $paidReferral = Referral::create([
        'referrer_master_id' => $referrer->id,
        'referred_master_id' => $paidMaster->id,
        'status' => Referral::STATUS_REWARDED,
    ]);
    Referral::create([
        'referrer_master_id' => $referrer->id,
        'referred_master_id' => $unqualifiedMaster->id,
        'status' => Referral::STATUS_PENDING,
    ]);

    $pendingPayment = Payment::create([
        'master_id' => $pendingMaster->id,
        'amount' => 3000,
        'type' => Payment::TYPE_PROMO,
    ]);
    $paidPayment = Payment::create([
        'master_id' => $paidMaster->id,
        'amount' => 2000,
        'type' => Payment::TYPE_PROMO,
    ]);

    ReferralEarning::create([
        'referrer_master_id' => $referrer->id,
        'referred_master_id' => $pendingMaster->id,
        'referral_id' => $pendingReferral->id,
        'payment_id' => $pendingPayment->id,
        'payment_amount' => 3000,
        'amount' => 300,
        'percent' => 10,
        'status' => ReferralEarning::STATUS_PENDING,
    ]);
    ReferralEarning::create([
        'referrer_master_id' => $referrer->id,
        'referred_master_id' => $paidMaster->id,
        'referral_id' => $paidReferral->id,
        'payment_id' => $paidPayment->id,
        'payment_amount' => 2000,
        'amount' => 200,
        'percent' => 10,
        'status' => ReferralEarning::STATUS_PAID,
    ]);

    $headers = ['X-Master-Id' => (string) $referrer->id];

    $this->withHeaders($headers)
        ->getJson('/api/referrals/my')
        ->assertOk()
        ->assertJsonCount(3, 'data')
        ->assertJsonFragment([
            'master_id' => $pendingMaster->id,
            'name' => 'Pending referral',
            'qualified' => true,
            'earned_amount' => 300,
        ])
        ->assertJsonFragment([
            'master_id' => $unqualifiedMaster->id,
            'name' => 'Unqualified referral',
            'qualified' => false,
            'earned_amount' => 0,
        ]);

    $this->withHeaders($headers)
        ->getJson('/api/referrals/earnings')
        ->assertOk()
        ->assertExactJson([
            'data' => [
                'total_earned' => 500,
                'pending' => 300,
                'paid' => 200,
                'qualified_referrals' => 2,
            ],
        ]);
});

it('ignores a zero value card payment when finding the first successful payment', function (): void {
    config(['referral.percent' => 10]);

    $referrer = Master::create(['name' => 'Referrer', 'referral_code' => 'REF123']);
    $referred = Master::create(['name' => 'Referred', 'referral_code' => 'NEW123']);
    $referral = Referral::create([
        'referrer_master_id' => $referrer->id,
        'referred_master_id' => $referred->id,
        'status' => Referral::STATUS_PENDING,
    ]);

    Payment::create([
        'master_id' => $referred->id,
        'amount' => 0,
        'type' => Payment::TYPE_CARD,
    ]);

    expect($referral->fresh()->status)->toBe(Referral::STATUS_PENDING)
        ->and(ReferralEarning::query()->count())->toBe(0);

    Payment::create([
        'master_id' => $referred->id,
        'amount' => 3000,
        'type' => Payment::TYPE_CARD,
    ]);

    expect($referral->fresh()->status)->toBe(Referral::STATUS_REWARDED)
        ->and(ReferralEarning::query()->value('amount'))->toBe(300);
});

it('seeds two qualified referrals and 500 pending earnings for Masha', function (): void {
    $this->seed(DatabaseSeeder::class);

    $this->withHeader('X-Master-Id', '1')
        ->getJson('/api/referrals/earnings')
        ->assertOk()
        ->assertExactJson([
            'data' => [
                'total_earned' => 500,
                'pending' => 500,
                'paid' => 0,
                'qualified_referrals' => 2,
            ],
        ]);

    $this->withHeader('X-Master-Id', '1')
        ->getJson('/api/referrals/my')
        ->assertOk()
        ->assertJsonFragment([
            'name' => 'Даша',
            'qualified' => true,
            'earned_amount' => 200,
        ])
        ->assertJsonFragment([
            'name' => 'Катя',
            'qualified' => false,
            'earned_amount' => 0,
        ]);
});
