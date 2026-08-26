<?php

namespace App\Http\Controllers\Pelapor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        $districts = \App\Models\District::all();

        return view('pelapor.profile.edit', compact('user', 'districts'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'phone_number' => 'required|string|max:20',
            'full_name' => 'required|string|max:100',
            'district_id' => 'required|exists:districts,id',
            'password' => 'nullable|string|min:8|max:20|confirmed',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,webp|max:2048',
            'remove_photo' => 'nullable|boolean',
        ], [
            'district_id.required' => 'Silakan pilih Kabupaten/Kota.',
            'district_id.exists' => 'Wilayah Kabupaten/Kota yang dipilih tidak valid.'
        ]);

        $updateData = [
            'phone_number' => $validated['phone_number'],
            'full_name' => $validated['full_name'],
            'district_id' => $validated['district_id'],
        ];

        if (! empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        // Handle photo removal
        if ($request->boolean('remove_photo')) {
            if ($user->profile_picture) {
                Storage::disk('public')->delete($user->profile_picture);
            }
            $updateData['profile_picture'] = null;
        } 
        // Handle new photo upload
        elseif ($request->hasFile('profile_picture')) {
            if ($user->profile_picture) {
                Storage::disk('public')->delete($user->profile_picture);
            }
            $path = $request->file('profile_picture')->store('profile-photos', 'public');
            $updateData['profile_picture'] = $path;
        }

        $user->update($updateData);

        return redirect()->route('pelapor.profile.edit')->with('success', 'Profil berhasil diperbarui.');
    }
}
