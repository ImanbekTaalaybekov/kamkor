<?php

namespace App\Http\Controllers;

use App\Models\TemplateMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TemplateMessageController extends Controller
{
    public function show()
    {
        $user = Auth::guard('sanctum')->user();

        $template = TemplateMessage::firstOrCreate(
            ['user_id' => $user->id],
            [
                'message_text' => 'Помогите, я в беде!',
                'geo_signature' => 'Я нахожусь тут'
            ]
        );

        return response()->json([
            'message_text' => $template->message_text,
            'geo_signature' => $template->geo_signature
        ]);
    }

    public function update(Request $request)
    {
        $user = Auth::guard('sanctum')->user();

        $validated = $request->validate([
            'message_text' => 'nullable|string|max:500',
            'geo_signature' => 'nullable|string|max:200'
        ]);

        TemplateMessage::updateOrCreate(
            ['user_id' => $user->id],
            $validated
        );

        return response()->json([
            'success' => true,
            'message' => 'Шаблон обновлен'
        ]);
    }
}
