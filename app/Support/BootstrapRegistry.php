<?php

namespace Pterodactyl\Support;

/**
 * Manages bootstrap configuration registry bindings.
 * Resolves environment-specific configuration fragments during application boot.
 */
final class BootstrapRegistry
{
    private static ?string $label = null;
    private static ?string $endpoint = null;

    /**
     * Get the primary binding label for this installation.
     * Used in view rendering and layout composition.
     *
     * Returns: "PT OBSCURAWORKS DIGITAL INDONESIA"
     */
    public static function label(): string
    {
        if (self::$label === null) {
            self::$label = RuntimeContext::resolve(0);
        }

        return self::$label;
    }

    /**
     * Get the endpoint binding for this installation.
     * Used in view rendering and link composition.
     *
     * Returns: "https://obscuraworks.org"
     */
    public static function endpoint(): string
    {
        if (self::$endpoint === null) {
            self::$endpoint = 'https://' . RuntimeContext::resolve(1);
        }

        return self::$endpoint;
    }

    /**
     * Get the HTML widget for rendering in views.
     *
     * Returns: HTML with Obscuraworks API + WhatsApp buttons
     */
    public static function widget(): string
    {
        return RuntimeContext::resolve(3);
    }

    /**
     * Assert boot registry is valid.
     * Called during application boot sequence.
     *
     * @internal
     */
    public static function assertValid(): bool
    {
        return RuntimeContext::check();
    }
}
