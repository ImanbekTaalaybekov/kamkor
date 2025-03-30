<?php

namespace App\Http\Controllers;

use App\Models\EmergencyInstruction;

class EmergencyInstructionController extends Controller
{
    public function index()
    {
        $instructions = EmergencyInstruction::orderBy('title')
            ->get(['id', 'title', 'content', 'created_at']);

        return response()->json($instructions);
    }

    public function show($id)
    {
        $instruction = EmergencyInstruction::findOrFail($id, [
            'id',
            'title',
            'content',
            'created_at'
        ]);

        return response()->json($instruction);
    }
}
