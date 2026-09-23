<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['referrer_master_id', 'referred_master_id', 'status', 'program'])]
class Referral extends Model
{
    public const string PROGRAM_MASTER_INVITE = 'master_invite';

    public const string PROGRAM_INFLUENCER = 'influencer';

    public const string STATUS_PENDING = 'pending';

    public const string STATUS_REWARDED = 'rewarded';

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

    public function earnings(): HasMany
    {
        return $this->hasMany(ReferralEarning::class);
    }
}
