<?php

namespace App\Http\Controllers;

use App\Models\SosHistory;
use Illuminate\Support\Facades\Auth;

class SosHistoryController extends Controller
{
    public function index()
    {
        $user = Auth::guard('sanctum')->user();

        $histories = SosHistory::where('user_id', $user->id)->latest()->get();

        return response()->json([
            'success' => true,
            'data' => $histories->map(function ($item) {
                return [
                    'id' => $item->id,
                    'geo' => $item->geo,
                    'created_at' => $item->created_at->toDateTimeString()
                ];
            })
        ]);
    }

    public function show($id)
    {
        $sos = SosHistory::where('user_id', Auth::guard('sanctum')->id())
            ->findOrFail($id);

        return response()->json([
            'id' => $sos->id,
            'created_at' => $sos->created_at,
            'geo' => $sos->geo,
        ]);
    }
}
