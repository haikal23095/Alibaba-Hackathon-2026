<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Zakaria MP',
                'email' => 'zakariamp@rebound.ai',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'Haikal Firmansyah',
                'email' => 'haikal.firmansyah@rebound.ai',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'Tiara Fatimah Azzahra',
                'email' => 'tiara.azzahra@rebound.ai',
                'password' => Hash::make('password'),
            ],
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }

        // id: Isi GDS tiruan dengan booking demo untuk verifikasi PNR
        // en: Populate the mock GDS with demo bookings for PNR verification
        $this->call(MockGdsBookingSeeder::class);

        // id: Isi sesi chat & riwayat pesan AI agent
        // en: Populate AI agent chat sessions & message history
        $this->call(AgentChatSeeder::class);
    }
}

