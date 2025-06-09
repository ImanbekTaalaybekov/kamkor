<?php

namespace App\Http\Controllers;

use App\Models\UsagePrivacyPolicy;
use Illuminate\Http\Request;

class UsagePrivacyPolicyController extends Controller
{
    public function index()
    {
        $instructions = UsagePrivacyPolicy::all();

        return response()->json($instructions);
    }
}
