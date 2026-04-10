<?php

namespace Pterodactyl\Http\Requests\Api\Client\Servers\Settings;

use Pterodactyl\Models\Server;
use Pterodactyl\Http\Requests\Api\Client\ClientApiRequest;

/**
 * INSTRUKSI:
 * Letakkan file ini di:
 * app/Http/Requests/Api/Client/Servers/Settings/ToggleVisibilityRequest.php
 */
class ToggleVisibilityRequest extends ClientApiRequest
{
    /**
     * Hanya owner server (bukan subuser) yang diizinkan mengubah hide_from_admin.
     * Root admin pun tidak bisa mengubah pengaturan ini atas nama orang lain.
     */
    public function authorize(): bool
    {
        /** @var Server $server */
        $server = $this->route()->parameter('server');

        return $this->user()->id === $server->owner_id;
    }

    public function rules(): array
    {
        return [
            'hide_from_admin' => ['required', 'boolean'],
        ];
    }
}
