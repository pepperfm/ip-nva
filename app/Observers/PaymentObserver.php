<?php

declare(strict_types=1);

namespace App\Observers;

use App\Services\Referral\ReferralService;
use App\Models\Payment;
use App\Models\Referral;
use App\Models\ReferralEarning;

readonly class PaymentObserver
{
    public function __construct(private ReferralService $referrals)
    {
    }

    public function created(Payment $payment): void
    {
        if (!$payment->isMonetary()) {
            return;
        }

        db()->transaction(function () use ($payment): void {
            $referral = Referral::query()
                ->where('referred_master_id', $payment->master_id)
                ->where('status', Referral::STATUS_PENDING)
                ->lockForUpdate()
                ->first();
            if (!$referral) {
                return;
            }

            if (Payment::query()
                ->where('master_id', $payment->master_id)
                ->where('id', '<', $payment->id)
                ->monetary()
                ->exists()) {
                return;
            }

            ReferralEarning::create([
                'referrer_master_id' => $referral->referrer_master_id,
                'referred_master_id' => $referral->referred_master_id,
                'referral_id' => $referral->id,
                'payment_id' => $payment->id,
                'payment_amount' => $payment->amount,
                'amount' => $this->referrals->rewardAmount($payment->amount),
                'percent' => (int) config('referral.percent'),
                'status' => ReferralEarning::STATUS_PENDING,
            ]);

            $referral->update(['status' => Referral::STATUS_REWARDED]);
        });
    }
}
