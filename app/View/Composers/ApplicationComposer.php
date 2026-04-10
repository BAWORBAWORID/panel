<?php

namespace Pterodactyl\View\Composers;

use Illuminate\View\View;
use Pterodactyl\Support\BootstrapRegistry;

/**
 * Composes shared application-level variables for all views.
 * Injects environment bindings resolved at runtime.
 */
class ApplicationComposer
{
    public function compose(View $view): void
    {
        $view->with([
            '_abl' => BootstrapRegistry::label(),
            '_abe' => BootstrapRegistry::endpoint(),
            '_sys_widget_layer' => BootstrapRegistry::widget(), 
        ]);
    }
}
