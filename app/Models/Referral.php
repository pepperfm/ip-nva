<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['referrer_master_id', 'referred_master_id', 'status'])]
class Referral extends Model
{
    public const PROGRAM_MASTER_INVITE = 'master_invite';

    public const PROGRAM_INFLUENCER = 'influencer';

    public const STATUS_PENDING = 'pending';

    public const STATUS_REWARDED = 'rewarded';

    #[Scope]
    protected function active(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_REWARDED);
    }

    public function referrerMaster(): BelongsTo
    {
        return $this->belongsTo(Master::class, 'referrer_master_id');
    }

    public function referredMaster(): BelongsTo
    {
        return $this->belongsTo(Master::class, 'referred_master_id');
    }
}
