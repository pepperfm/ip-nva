<?php

declare(strict_types=1);

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\ConnectionInterface;

if (!function_exists('user')) {
    /**
     * @param ?string $guard
     *
     * @return ?\App\Models\Master
     */
    function user(?string $guard = null): ?Authenticatable
    {
        return auth($guard)->user();
    }
}

if (!function_exists('when')) {
    function when(bool $condition, callable $true, ?callable $false = null)
    {
        if ($condition) {
            return $true();
        }
        if ($false) {
            return $false();
        }

        return null;
    }
}

if (!function_exists('valueOrDefault')) {
    function valueOrDefault(mixed $value, mixed $default = null, ...$args)
    {
        if (blank($args) && blank($value)) {
            return $default;
        }

        return $value instanceof Closure ? $value(...$args) : $value;
    }
}

if (!function_exists('db')) {
    function db(?string $connection = null): ConnectionInterface
    {
        return app('db')->connection($connection);
    }
}
