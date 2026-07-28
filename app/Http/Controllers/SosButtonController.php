<?php

namespace App\Http\Controllers;

use App\Models\SosHistory;
use App\Models\TrustedContacts;
use App\Models\TemplateMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SosButtonController extends Controller
{
    public function sendAlert(Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'geolocation' => [
                'required',
            ],
        ]);

        $sosHistory = SosHistory::create([
            'user_id' => $user->id,
            'geo' => $validated['geolocation'],
            'audio_file' => null,
            'status' => 'pending',
            'last_location_at' => now(),
        ]);

        $template = TemplateMessage::where('user_id', $user->id)->first();
        $messageText = $template->message_text ?? 'Помогите, я в беде!';

        if ($template && $template->geo_signature) {
            $geoSignature = str_replace('{geo}', $sosHistory->geo, $template->geo_signature);
        } else {
            $geoSignature = "Моё местоположение: https://maps.google.com/?q={$sosHistory->geo}";
        }

        $fullMessage = "$messageText\n$geoSignature";

        TrustedContacts::where('user_id', $user->id)
            ->each(function ($contact) use ($fullMessage) {
                $this->sendSMS($contact->phone_number, $fullMessage);
            });

        return response()->json([
            'success' => true,
            'sos_id' => $sosHistory->id,
            'status' => $sosHistory->status,
            'message' => 'SOS-сигнал отправлен',
        ]);
    }

    public function updateLocation(Request $request, $sosId)
    {
        $user = Auth::guard('sanctum')->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $sosHistory = SosHistory::where('id', $sosId)
            ->where('user_id', $user->id)
            ->firstOrFail();

        if (!in_array($sosHistory->status, ['pending', 'in_progress'], true)) {
            return response()->json([
                'message' => 'Заявка уже завершена или отменена. Обновление геолокации остановлено.',
                'status' => $sosHistory->status,
            ], 409);
        }

        $validated = $request->validate([
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'accuracy' => ['nullable', 'numeric', 'min:0', 'max:100000'],
            'captured_at' => ['nullable', 'date'],
        ]);

        $latitude = (float) $validated['latitude'];
        $longitude = (float) $validated['longitude'];

        $sosHistory->update([
            'geo' => sprintf('%.7F, %.7F', $latitude, $longitude),
            'location_accuracy' => $validated['accuracy'] ?? null,
            'last_location_at' => $validated['captured_at'] ?? now(),
        ]);

        return response()->json([
            'success' => true,
            'status' => $sosHistory->status,
            'geo' => $sosHistory->geo,
            'last_location_at' => optional($sosHistory->last_location_at)->toIso8601String(),
        ]);
    }

    protected function sendSMS(string $phoneNumber, string $message): void
    {
        // Добавить отправку СМС исходя от выбранного оператора (Никита Мобайл)
    }

    public function addAudio(Request $request, $sosId)
    {
        $user = Auth::guard('sanctum')->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $sosHistory = SosHistory::where('id', $sosId)
            ->where('user_id', $user->id)
            ->firstOrFail();

        if (!in_array($sosHistory->status, ['pending', 'in_progress'], true)) {
            return response()->json([
                'message' => 'Заявка уже завершена или отменена. Аудиозапись не принимается.',
                'status' => $sosHistory->status,
            ], 409);
        }

        $request->validate([
            'audio' => 'required|file|max:10240|mimes:webm,ogg,mp3,wav,aac,m4a,mp4',
        ]);

        $path = $request->file('audio')->store("sos_audio/{$user->id}", 'local');

        $sosHistory->update(['audio_file' => $path]);

        return response()->json([
            'success' => true,
            'message' => 'Аудиозапись сохранена и доступна только уполномоченным сотрудникам.',
        ]);
    }
}
