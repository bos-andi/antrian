<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@andidev.id'],
            [
                'name' => 'Admin',
                'password' => 'andidev.id',
                'role' => 'admin',
            ]
        );

        $this->call([
            PendaftaranSantriBaruSeeder::class,
        ]);
    }
}
