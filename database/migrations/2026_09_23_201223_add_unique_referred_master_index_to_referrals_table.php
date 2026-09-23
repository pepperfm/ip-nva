<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('referrals', static function (Blueprint $table): void {
            $table->unique('referred_master_id');
        });
    }

    public function down(): void
    {
        Schema::table('referrals', static function (Blueprint $table): void {
            $table->dropUnique(['referred_master_id']);
        });
    }
};
