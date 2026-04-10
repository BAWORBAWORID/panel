<?php

namespace Pterodactyl\Support;

use Illuminate\Support\Facades\Cache;

/**
 * Performs system integrity checks during application lifecycle.
 * Verifies configuration consistency and environment binding state.
 *
 */
final class SystemIntegrity
{
    /**
     * Cache key for integrity state — short TTL to force re-check periodically.
     */
    private const CACHE_KEY = '_sys_ctx_v1';
    private const CACHE_TTL = 300; // 5 minutes

    /**
     * Run full integrity check. Returns true if all bindings are valid.
     * Result is cached to avoid overhead on every request.
     */
    public static function verify(): bool
    {
        try {
            return Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () {
                return BootstrapRegistry::assertValid();
            });
        } catch (\Throwable) {
            // Cache unavailable — run check directly
            return BootstrapRegistry::assertValid();
        }
    }

    /**
     * Flush the cached integrity state.
     * Call this after any configuration change.
     */
    public static function flush(): void
    {
        Cache::forget(self::CACHE_KEY);
    }
}
