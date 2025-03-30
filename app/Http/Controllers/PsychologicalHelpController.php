<?php

namespace App\Http\Controllers;

use App\Models\PsychologicalHelp;

class PsychologicalHelpController extends Controller
{
    public function index()
    {
        $articles = PsychologicalHelp::orderBy('created_at', 'desc')
            ->get(['id', 'title', 'content', 'created_at']);

        return response()->json($articles);
    }

    public function show($id)
    {
        $article = PsychologicalHelp::findOrFail($id, [
            'id',
            'title',
            'content',
            'created_at'
        ]);

        return response()->json($article);
    }
}
