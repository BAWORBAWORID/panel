<?php

namespace Pterodactyl\Http\Middleware\Admin\Servers;

use Closure;
use Illuminate\Http\Request;
use Pterodactyl\Models\Server;
use Illuminate\Http\RedirectResponse;

class RestrictHiddenServerAccess
{
    public function handle(Request $request, Closure $next): mixed
    {
        /** @var \Pterodactyl\Models\User $admin */
        $admin = $request->user();

        /** @var Server|null $server */
        $server = $request->route()->parameter('server');

        if (!$server instanceof Server) {
            return $next($request);
        }

        if (!$server->hide_from_admin) {
            return $next($request);
        }

        // Superadmin (id=1) tetap bisa masuk.
        if ($admin->id === 1) {
            return $next($request);
        }

        return redirect()
            ->route('admin.servers.view', $server->id)
            ->with('pterodactyl::flashes.info', [
                'Server <strong>' . e($server->name) . '</strong> is protected by owner privacy mode. Access to modify server details and ownership has been restricted.',
            ]);
    }
}
