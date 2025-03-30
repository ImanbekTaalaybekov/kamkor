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
                'required'
            ],
        ]);

        $sosHistory = SosHistory::create([
            'user_id' => $user->id,
            'geo' => $validated['geolocation'],
            'audio_file' => null
        ]);

        $template = TemplateMessage::where('user_id', $user->id)->first();
        $messageText = $template->message_text ?? 'Помогите, я в беде!';

        $geoSignature = $template->geo_signature
            ? str_replace('{geo}', $sosHistory->geo, $template->geo_signature)
            : "Моё местоположение: https://maps.google.com/?q={$sosHistory->geo}";

        $fullMessage = "$messageText\n$geoSignature";

        TrustedContacts::where('user_id', $user->id)
            ->each(function ($contact) use ($fullMessage) {
                $this->sendSMS($contact->phone_number, $fullMessage);
            });

        return response()->json([
            'success' => true,
            'sos_id' => $sosHistory->id,
            'message' => 'SOS-сигнал отправлен'
        ]);
    }

    protected function sendSMS(string $phoneNumber, string $message): void
    {
        //Добавить отправку СМС исходя от выбранного оператора (Никита Мобайл)
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

        $request->validate([
            'audio' => 'required|file|mimes:mp3,wav,aac,ogg' // 10MB
        ]);

        $path = $request->file('audio')->store(
            "sos_audio/{$user->id}",
            'public'
        );

        $sosHistory->update(['audio_file' => $path]);

        return response()->json([
            'success' => true,
            'audio_url' => asset("storage/$path"),
            'message' => 'Аудиофайл успешно сохранён'
        ]);
    }
}
