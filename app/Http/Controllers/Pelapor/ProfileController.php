<?php

namespace App\Http\Controllers\Pelapor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        return view('pelapor.profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        // Validate the profile update
        $validated = $request->validate([
            'phone_number' => 'required|string|max:20',
            // Since we can't change migrations right now to add address columns to users table,
            // we will only update existing fields like full_name and phone_number.
            // Wait, email shouldn't be editable if it's tied to Google.
            'full_name' => 'required|string|max:100',
        ]);

        $user->update($validated);

        return redirect()->route('pelapor.profile.edit')->with('success', 'Profil berhasil diperbarui.');
    }
}
