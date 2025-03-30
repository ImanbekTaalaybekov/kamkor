<?php

namespace App\Http\Controllers;

use App\Models\TrustedContacts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TrustedContactController extends Controller
{
    public function getContacts()
    {
        $user = Auth::guard('sanctum')->user();
        $contacts = TrustedContacts::where('user_id', $user->id)
            ->orderBy('name')
            ->get(['id', 'name', 'phone_number']);

        return response()->json($contacts);
    }

    public function addContact(Request $request)
    {
        $user = Auth::guard('sanctum')->user();

        $request->validate([
            'name' => 'required|string|max:100',
            'phone_number' => 'required|string|max:20|unique:trusted_contacts,phone_number,NULL,id,user_id,'.$user->id
        ]);

        $contact = TrustedContacts::create([
            'user_id' => $user->id,
            'name' => $request->name,
            'phone_number' => $request->phone_number
        ]);

        return response()->json($contact, 201);
    }

    public function deleteContact($id)
    {
        $user = Auth::guard('sanctum')->user();
        $contact = TrustedContacts::where('user_id', $user->id)
            ->where('id', $id)
            ->firstOrFail();

        $contact->delete();

        return response()->json(['message' => 'Контакт удален']);
    }

    public function updateContact(Request $request, $id)
    {
        $user = Auth::guard('sanctum')->user();
        $contact = TrustedContacts::where('user_id', $user->id)
            ->where('id', $id)
            ->firstOrFail();

        $request->validate([
            'name' => 'sometimes|string|max:100',
            'phone_number' => 'sometimes|string|max:20'
        ]);

        $contact->update($request->all());

        return response()->json($contact);
    }
}
