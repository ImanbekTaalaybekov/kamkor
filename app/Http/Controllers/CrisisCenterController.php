<?php

namespace App\Http\Controllers;

use App\Models\CrisisCenter;

class CrisisCenterController extends Controller
{
    public function index()
    {
        $centers = CrisisCenter::orderBy('name')->get([
            'id',
            'name',
            'address',
            'phone_number'
        ]);

        return response()->json($centers);
    }

    public function show($id)
    {
        $center = CrisisCenter::findOrFail($id, [
            'id',
            'name',
            'address',
            'phone_number'
        ]);

        return response()->json($center);
    }
}
