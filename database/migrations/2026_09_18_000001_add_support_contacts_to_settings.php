<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The support page had no way to show players where to write. These two
 * optional fields are filled in Admin > Settings and appear on that page only
 * when they are set.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            if (!Schema::hasColumn('settings', 'support_email')) {
                $table->string('support_email', 191)->nullable()->after('software_description');
            }
            if (!Schema::hasColumn('settings', 'support_telegram')) {
                $table->string('support_telegram', 191)->nullable()->after('support_email');
            }
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            foreach (['support_email', 'support_telegram'] as $column) {
                if (Schema::hasColumn('settings', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
