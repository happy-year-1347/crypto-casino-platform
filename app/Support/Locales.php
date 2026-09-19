<?php

namespace App\Support;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

/**
 * Languages the player site is translated into. Keys match the files in lang/*.json
 * and resources/js/Services/Locale.js.
 */
class Locales
{
    public const SUPPORTED = [
        'en'    => 'English',
        'pt_BR' => 'Português',
        'es'    => 'Español',
        'fr'    => 'Français',
        'de'    => 'Deutsch',
    ];

    public const FALLBACK = 'en';

    /**
     * Maps anything a browser or client sends (pt-BR, pt, es-MX, en_US, EN...) to a
     * supported code, or null.
     */
    public static function normalize(?string $code): ?string
    {
        if ($code === null || trim($code) === '') {
            return null;
        }

        $code = str_replace('-', '_', trim($code));
        foreach (array_keys(self::SUPPORTED) as $supported) {
            if (strcasecmp($code, $supported) === 0) {
                return $supported;
            }
        }

        $base = strtolower(explode('_', $code)[0]);

        return match ($base) {
            'pt'    => 'pt_BR',
            'en', 'es', 'fr', 'de' => $base,
            default => null,
        };
    }

    public static function isSupported(?string $code): bool
    {
        return $code !== null && array_key_exists($code, self::SUPPORTED);
    }

    /**
     * Site default chosen by the admin (Settings > Default > Default language).
     */
    public static function siteDefault(): string
    {
        $code = Cache::remember('setting:default_language', 600, function () {
            try {
                if (!Schema::hasColumn('settings', 'default_language')) {
                    return null;
                }
                return Setting::query()->value('default_language');
            } catch (\Throwable $e) {
                return null;
            }
        });

        return self::normalize($code) ?? self::FALLBACK;
    }

    public static function forgetSiteDefault(): void
    {
        Cache::forget('setting:default_language');
    }

    /**
     * Language for a request: explicit X-Locale header (the SPA sends the language
     * on screen) > the player's saved language > browser language > site default.
     */
    public static function resolve(Request $request, $user = null): string
    {
        if ($code = self::normalize($request->header('X-Locale'))) {
            return $code;
        }

        if ($user && ($code = self::normalize($user->language))) {
            return $code;
        }

        foreach ($request->getLanguages() as $browser) {
            if ($code = self::normalize($browser)) {
                return $code;
            }
        }

        return self::siteDefault();
    }

    public static function options(): array
    {
        return self::SUPPORTED;
    }
}
