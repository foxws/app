<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('telescope_entries_tags');
        Schema::dropIfExists('telescope_entries');
        Schema::dropIfExists('telescope_monitoring');

        $twoFactorColumns = array_values(array_filter(
            ['two_factor_secret', 'two_factor_recovery_codes', 'two_factor_confirmed_at'],
            fn (string $column): bool => Schema::hasColumn('users', $column),
        ));

        if ($twoFactorColumns === []) {
            return;
        }

        Schema::table('users', function (Blueprint $table) use ($twoFactorColumns) {
            $table->dropColumn($twoFactorColumns);
        });
    }
};
