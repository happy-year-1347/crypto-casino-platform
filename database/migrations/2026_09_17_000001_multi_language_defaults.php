<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * The site used to force Portuguese on every account (users.language defaulted to
 * pt_BR). Now:
 *  - settings.default_language: the language visitors get when their browser
 *    language is not one of the supported ones (admin can change it)
 *  - users.language is NULL until the player picks a language, so new players
 *    follow their browser / the site default instead of Portuguese.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('settings', 'default_language')) {
            Schema::table('settings', function (Blueprint $table) {
                $table->string('default_language', 10)->default('en');
            });
        }

        Schema::table('users', function (Blueprint $table) {
            $table->string('language')->nullable()->default(null)->change();
        });

        // pt_BR was the forced default, not a choice
        DB::table('users')->where('language', 'pt_BR')->update(['language' => null]);

        Cache::forget('setting');
        Cache::forget('setting:default_language');
    }

    public function down(): void
    {
        DB::table('users')->whereNull('language')->update(['language' => 'pt_BR']);

        Schema::table('users', function (Blueprint $table) {
            $table->string('language')->nullable(false)->default('pt_BR')->change();
        });

        if (Schema::hasColumn('settings', 'default_language')) {
            Schema::table('settings', function (Blueprint $table) {
                $table->dropColumn('default_language');
            });
        }

        Cache::forget('setting');
        Cache::forget('setting:default_language');
    }
};
