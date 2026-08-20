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

        $validated = $request->validate([
            'phone_number' => 'required|string|max:20',
            'full_name' => 'required|string|max:100',
            'password' => 'nullable|string|min:8|max:20',
        ]);

        $updateData = [
            'phone_number' => $validated['phone_number'],
            'full_name' => $validated['full_name'],
        ];

        if (!empty($validated['password'])) {
            $updateData['password'] = \Illuminate\Support\Facades\Hash::make($validated['password']);
        }

        $user->update($updateData);

        return redirect()->route('pelapor.profile.edit')->with('success', 'Profil berhasil diperbarui.');
    }
}
