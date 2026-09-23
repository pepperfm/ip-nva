<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Observers\PaymentObserver;

#[ObservedBy(PaymentObserver::class)]
#[Fillable(['master_id', 'amount', 'type'])]
class Payment extends Model
{
    public const string TYPE_CARD = 'card';

    public const string TYPE_SBP = 'sbp';

    public const string TYPE_PROMO = 'promo';

    public const string TYPE_TRIAL = 'trial';

    protected function casts(): array
    {
        return [
            'amount' => 'integer',
        ];
    }

    public function isMonetary(): bool
    {
        return in_array($this->type, [self::TYPE_CARD, self::TYPE_SBP], true) && $this->amount > 0;
    }

    #[Scope]
    protected function monetary(Builder $query): Builder
    {
        return $query
            ->whereIn('type', [self::TYPE_CARD, self::TYPE_SBP])
            ->where('amount', '>', 0);
    }

    public function master(): BelongsTo
    {
        return $this->belongsTo(Master::class);
    }
}
