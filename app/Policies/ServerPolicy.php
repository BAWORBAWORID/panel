<?php

namespace Pterodactyl\Policies;

use Pterodactyl\Models\User;
use Pterodactyl\Models\Server;

class ServerPolicy
{
    /**
     * Checks if the user has the given permission on/for the server.
     */
    protected function checkPermission(User $user, Server $server, string $permission): bool
    {
        $subuser = $server->subusers->where('user_id', $user->id)->first();
        if (!$subuser || empty($permission)) {
            return false;
        }

        return in_array($permission, $subuser->permissions);
    }

    /**
     * Runs before any of the functions are called. Used to determine if user is root admin,
     * if so, ignore permissions — EXCEPT when the server has hide_from_admin enabled and
     * the requesting user is not the actual owner of the server.
     *
     * Logic:
     *   - Owner always gets access (regardless of hide_from_admin).
     *   - Root admin gets bypassed access ONLY IF hide_from_admin is false.
     *   - Root admin accessing a hidden server falls through to normal permission check,
     *     which will return false (admin is not a subuser), effectively blocking them
     *     from the Client Panel for that server.
     */
    public function before(User $user, string $ability, Server $server): bool
    {
        // Owner always has full access — no exceptions.
        if ($server->owner_id === $user->id) {
            return true;
        }

        // Root admin bypass is suppressed when hide_from_admin is active.
        // Admin can still see the server in the Admin Panel (that uses a different
        // auth path), but they cannot access it through the Client Panel.
        if ($user->root_admin && !$server->hide_from_admin) {
            return true;
        }

        // For all other cases (subusers, or admins blocked by hide_from_admin),
        // fall through to the standard permission check.
        return $this->checkPermission($user, $server, $ability);
    }

    /**
     * This is a horrendous hack to avoid Laravel's "smart" behavior that does
     * not call the before() function if there isn't a function matching the
     * policy permission.
     */
    public function __call(string $name, mixed $arguments)
    {
        // do nothing
    }
}
