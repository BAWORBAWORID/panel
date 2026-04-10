<?php

namespace Pterodactyl\Http\Controllers\Api\Client;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Pterodactyl\Models\UserBackgroundSetting;
use Pterodactyl\Http\Controllers\Controller;

class BackgroundController extends Controller
{
    /**
     * GET /api/client/account/background
     * Return current background settings for the authenticated user.
     */
    public function show(Request $request): JsonResponse
    {
        $user     = $request->user();
        $settings = UserBackgroundSetting::firstOrNew(['user_id' => $user->id]);

        if (!$settings->exists) {
            $data = UserBackgroundSetting::defaults();
        } else {
            $data = $settings->only([
                'mode', 'pattern', 'pattern_size', 'image_url',
                'filter', 'blur_amount', 'transparency',
                'pattern_color1', 'pattern_color2',
            ]);
        }

        return response()->json(['data' => $data]);
    }

    /**
     * PUT /api/client/account/background
     * Save background settings for the authenticated user.
     */
    public function update(Request $request): JsonResponse
    {
        $request->validate(UserBackgroundSetting::$validationRules);

        $user = $request->user();

        $settings = UserBackgroundSetting::updateOrCreate(
            ['user_id' => $user->id],
            $request->only([
                'mode', 'pattern', 'pattern_size', 'image_url',
                'filter', 'blur_amount', 'transparency',
                'pattern_color1', 'pattern_color2',
            ])
        );

        return response()->json([
            'data' => $settings->only([
                'mode', 'pattern', 'pattern_size', 'image_url',
                'filter', 'blur_amount', 'transparency',
                'pattern_color1', 'pattern_color2',
            ]),
        ]);
    }
}
