<?php

namespace Database\Seeders;

use App\Models\District;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ActorSeeder extends Seeder
{
    public function run(): void
    {
        $operatorRole = Role::where('role_name', 'Operator Provinsi')->first();
        $subOperatorRole = Role::where('role_name', 'Sub Operator')->first();
        $pelaporRole = Role::where('role_name', 'Pelapor')->first();

        $districts = District::all();

        // Operator
        User::firstOrCreate(
            ['email' => 'operator@sipkp.local'],
            [
                'full_name' => 'Operator Provinsi',
                'username' => 'operator',
                'phone_number' => '081100000000',
                'password' => Hash::make('password'),
                'role_id' => $operatorRole->id,
                'district_id' => null,
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        // Sub Operators for 17 districts
        $phoneCounter = 1;
        foreach ($districts as $district) {
            $slug = Str::slug($district->name, '_');

            $emailPrefix = $slug;
            $username = 'subop.'.$slug;

            if ($district->name === 'Palembang') {
                $emailPrefix = 'plg';
            } elseif ($district->name === 'Prabumulih') {
                $emailPrefix = 'pbm';
            }

            User::firstOrCreate(
                ['email' => 'subop.'.$emailPrefix.'@sipkp.local'],
                [
                    'full_name' => 'Sub Operator '.$district->name,
                    'username' => $username,
                    'phone_number' => '0822000000'.str_pad($phoneCounter, 2, '0', STR_PAD_LEFT),
                    'password' => Hash::make('password'),
                    'role_id' => $subOperatorRole->id,
                    'district_id' => $district->id,
                    'is_active' => true,
                    'email_verified_at' => now(),
                ]
            );
            $phoneCounter++;
        }

        // Pelapor Utama
        User::firstOrCreate(
            ['email' => 'pelapor1@sipkp.local'],
            [
                'full_name' => 'Pelapor Aktif',
                'username' => 'pelapor1',
                'phone_number' => '083300000001',
                'password' => Hash::make('password'),
                'role_id' => $pelaporRole->id,
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        // Jika Pelapor kurang dari 5, tambahkan acak
        $pelaporCount = User::where('role_id', $pelaporRole->id)->count();
        if ($pelaporCount < 5) {
            User::factory()->count(5 - $pelaporCount)->create([
                'role_id' => $pelaporRole->id,
                'password' => Hash::make('password'),
                'is_active' => true,
            ]);
        }
    }
}
