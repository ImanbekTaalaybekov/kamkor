<?php

namespace App\Http\Controllers;

use App\Models\AppVersion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AppVersionController extends Controller
{
    public function index(): JsonResponse
    {
        $versions = AppVersion::query()
            ->get(['platform', 'version'])
            ->keyBy('platform');

        return response()->json([
            'android' => $versions->get('android'),
            'ios' => $versions->get('ios'),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'platform' => [
                'required',
                Rule::in(['android', 'ios']),
            ],
            'version' => [
                'required',
                'string',
                'max:50',
            ],
        ]);

        $appVersion = AppVersion::query()->updateOrCreate(
            [
                'platform' => $validated['platform'],
            ],
            [
                'version' => $validated['version'],
            ]
        );

        return response()->json([
            'message' => $appVersion->wasRecentlyCreated
                ? 'Версия создана'
                : 'Версия обновлена',
            'data' => $appVersion,
        ]);
    }
}
