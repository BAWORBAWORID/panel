<?php

namespace Pterodactyl\Http\Controllers\Api\Client;

use Illuminate\Http\JsonResponse;
use Pterodactyl\Models\AdminAlert;
use Pterodactyl\Http\Controllers\Controller;

class ActiveAlertController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $alert = AdminAlert::getActive();

        if (!$alert) {
            return response()->json(['data' => null]);
        }

        return response()->json([
            'data' => [
                'id'           => $alert->id,
                'title'        => $alert->title,
                'message'      => $alert->message,
                'type'         => $alert->type,
                'icon'         => $alert->icon ?? 'megaphone',
                'position'     => $alert->position ?? 'sticky',
                'bg_color'     => $alert->bg_color ?? '#1a1a2e',
                'border_color' => $alert->border_color ?? '#4a5568',
                'text_color'   => $alert->text_color ?? '#e2e8f0',
                'dismissable'  => $alert->dismissable,
                'created_by'   => $alert->creator
                    ? trim($alert->creator->name_first . ' ' . $alert->creator->name_last)
                    : 'Administrator',
            ],
        ]);
    }
}
