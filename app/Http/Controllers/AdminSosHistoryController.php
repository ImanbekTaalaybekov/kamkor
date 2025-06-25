<?php

namespace App\Http\Controllers;

use App\Models\SosHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminSosHistoryController extends Controller
{
    public function getRegionSosHistories(Request $request)
    {
        $admin = Auth::user();

        $histories = SosHistory::whereHas('user', function ($query) use ($admin) {
            $query->where('region', $admin->region);
        })->with('user')->latest()->get();

        return response()->json([
            'data' => $histories
        ]);
    }

    public function markAsDone($id)
    {
        $sos = SosHistory::find($id);

        if (!$sos) {
            return response()->json(['message' => 'Заявка не найдена'], 404);
        }

        $sos->status = 'done';
        $sos->save();

        return response()->json([
            'message' => 'Статус изменён на done',
            'data' => $sos,
        ]);
    }
}
