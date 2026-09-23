<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Services\Referral\ReferralService;
use App\Models\Master;
use App\Models\Referral;

final class ReferralController
{
    public function attach(Request $request, ReferralService $referrals): JsonResponse
    {
        $master = $this->currentMaster($request);
        $validated = $request->validate([
            'code' => ['required', 'string'],
        ]);

        $referral = $referrals->registerReferral($master, $validated['code']);
        if (!$referral) {
            return response()->json([
                'message' => 'The referral code is invalid or cannot be used by this master.',
            ], 422);
        }

        return response()->json([
            'data' => [
                'id' => $referral->id,
                'referrer_master_id' => $referral->referrer_master_id,
                'referred_master_id' => $referral->referred_master_id,
                'status' => $referral->status,
            ],
        ], $referral->wasRecentlyCreated ? 201 : 200);
    }

    public function my(Request $request): JsonResponse
    {
        $referrals = Referral::query()
            ->where('referrer_master_id', $this->currentMaster($request)->id)
            ->with('referredMaster')
            ->withSum('earnings', 'amount')
            ->get()
            ->map(static fn(Referral $referral): array => [
                'master_id' => $referral->referredMaster->id,
                'name' => $referral->referredMaster->name,
                'attached_at' => $referral->created_at->toISOString(),
                'qualified' => $referral->status === Referral::STATUS_REWARDED,
                'earned_amount' => (int) ($referral->earnings_sum_amount ?? 0),
            ]);

        return response()->json(['data' => $referrals]);
    }

    public function earnings(Request $request): JsonResponse
    {
        $master = $this->currentMaster($request);
        $earnings = $master->referralEarnings();

        return response()->json([
            'data' => [
                'total_earned' => (int) $earnings->clone()->sum('amount'),
                'pending' => (int) $earnings->clone()->where('status', 'pending')->sum('amount'),
                'paid' => (int) $earnings->clone()->where('status', 'paid')->sum('amount'),
                'qualified_referrals' => $master->referrals()
                    ->where('status', Referral::STATUS_REWARDED)
                    ->count(),
            ],
        ]);
    }

    private function currentMaster(Request $request): Master
    {
        $master = $request->attributes->get('current_master');

        abort_unless($master instanceof Master, 401);

        return $master;
    }
}
