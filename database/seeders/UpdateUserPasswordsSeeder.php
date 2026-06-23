<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UpdateUserPasswordsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Update password untuk users yang ada
        $users = [
            ['username' => 'admin', 'password' => 'admin123'],
            ['username' => 'manager', 'password' => 'manager123'],
            ['username' => 'operator', 'password' => 'operator123'],
            ['username' => 'viewer', 'password' => 'viewer123'],
        ];

        foreach ($users as $userData) {
            $user = User::where('username', $userData['username'])->first();
            if ($user) {
                $user->update([
                    'password' => Hash::make($userData['password'])
                ]);
                echo "Updated password for user: {$userData['username']}\n";
            }
        }
    }
}
