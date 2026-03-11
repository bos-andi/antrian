<?php

namespace Database\Seeders;

use App\Models\Counter;
use App\Models\Queue;
use App\Models\Service;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;

class PendaftaranSantriBaruSeeder extends Seeder
{
    /**
     * Seed data untuk antrian pendaftaran santri baru.
     */
    public function run(): void
    {
        // 0. Setting (jika belum ada)
        if (!Setting::exists()) {
            Setting::create([
                'name' => 'Pondok Pesantren',
                'address' => 'Jl. Pesantren No. 1',
                'phone' => '08123456789',
                'image' => '',
            ]);
        }

        // 1. Satu layanan saja: Pendaftaran Santri Baru (satu jenis tiket)
        $service = Service::firstOrCreate(
            ['name' => 'Pendaftaran Santri Baru'],
            [
                'prefix' => 'A',
                'padding' => 3,
                'is_active' => true,
            ]
        );

        // 2. Lima loket pendaftaran PSB
        $counterNames = ['Loket 1', 'Loket 2', 'Loket 3', 'Loket 4', 'Loket 5'];
        foreach ($counterNames as $name) {
            Counter::firstOrCreate(
                [
                    'name' => $name,
                    'service_id' => $service->id,
                ],
                ['is_active' => true]
            );
        }

        // 3. Lima petugas operator (satu per loket)
        $operators = [
            ['name' => 'Petugas Loket 1', 'email' => 'petugas.loket1@ponpes.id', 'counter_name' => 'Loket 1'],
            ['name' => 'Petugas Loket 2', 'email' => 'petugas.loket2@ponpes.id', 'counter_name' => 'Loket 2'],
            ['name' => 'Petugas Loket 3', 'email' => 'petugas.loket3@ponpes.id', 'counter_name' => 'Loket 3'],
            ['name' => 'Petugas Loket 4', 'email' => 'petugas.loket4@ponpes.id', 'counter_name' => 'Loket 4'],
            ['name' => 'Petugas Loket 5', 'email' => 'petugas.loket5@ponpes.id', 'counter_name' => 'Loket 5'],
        ];

        foreach ($operators as $op) {
            $counter = Counter::where('service_id', $service->id)->where('name', $op['counter_name'])->first();
            if ($counter) {
                User::firstOrCreate(
                    ['email' => $op['email']],
                    [
                        'name' => $op['name'],
                        'password' => 'petugas123',
                        'role' => 'operator',
                        'counter_id' => $counter->id,
                    ]
                );
            }
        }

        // 4. Sample antrian hari ini (untuk demo, skip jika sudah ada)
        $hasQueueToday = Queue::where('service_id', $service->id)->whereDate('created_at', now()->toDateString())->exists();
        if (!$hasQueueToday) {
            for ($i = 1; $i <= 5; $i++) {
                Queue::create([
                    'service_id' => $service->id,
                    'number' => $service->prefix . str_pad($i, $service->padding, '0', STR_PAD_LEFT),
                    'status' => 'waiting',
                ]);
            }
        }
    }
}
