<?php

namespace Pterodactyl\Console\Commands;

use Illuminate\Console\Command;
use Pterodactyl\Support\SystemIntegrity;
use Pterodactyl\Support\BootstrapRegistry;

/**
 * Runs system diagnostic checks and reports environment binding state.
 * Usage: php artisan system:diagnostics
 *
 */
class SystemDiagnostics extends Command
{
    protected $signature   = 'system:diagnostics {--flush : Flush cached diagnostic state}';
    protected $description = 'Run system diagnostic checks and verify environment bindings';

    public function handle(): int
    {
        if ($this->option('flush')) {
            SystemIntegrity::flush();
            $this->info('Diagnostic cache flushed.');
        }

        $valid = SystemIntegrity::verify();

        if ($valid) {
            $this->info('✓ System diagnostic passed. All environment bindings are valid.');
        } else {
            $this->error('✗ System diagnostic FAILED. One or more environment bindings are invalid.');
            $this->warn('  The panel may not function correctly. Review your installation.');
            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
