<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\BookingItem;
use App\Models\Package;
use App\Models\Slot;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Admin User
        $admin = User::firstOrCreate(
            ['email' => 'admin@tumpaksewu.com'],
            [
                'name' => 'Admin Tumpak Sewu',
                'password' => bcrypt('password123'),
            ]
        );

        // Paket Layanan Pendamping
        $packagesData = [
            [
                'name' => 'Layanan Pemandu Standar (Guide Lokal)',
                'slug' => 'layanan-pemandu-standar',
                'price' => 150000,
                'capacity_per_slot' => 15,
                'is_active' => true,
            ],
            [
                'name' => 'Paket Jelajah Air Terjun + Goa Tetes',
                'slug' => 'paket-jelajah-goa-tetes',
                'price' => 250000,
                'capacity_per_slot' => 12,
                'is_active' => true,
            ],
            [
                'name' => 'Paket Sunrise Panorama & Trekking Rombongan',
                'slug' => 'paket-sunrise-trekking',
                'price' => 350000,
                'capacity_per_slot' => 20,
                'is_active' => true,
            ],
            [
                'name' => 'Paket Fotografi & Video Dokumentasi Drone',
                'slug' => 'paket-fotografi-dokumentasi',
                'price' => 500000,
                'capacity_per_slot' => 5,
                'is_active' => true,
            ],
        ];

        $packages = [];
        foreach ($packagesData as $pkgData) {
            $packages[] = Package::firstOrCreate(
                ['slug' => $pkgData['slug']],
                $pkgData
            );
        }

        // Slot untuk 14 hari ke depan
        $today = Carbon::today();
        foreach ($packages as $pkg) {
            for ($i = 0; $i < 14; $i++) {
                $date = $today->copy()->addDays($i)->toDateString();
                Slot::firstOrCreate(
                    [
                        'package_id' => $pkg->id,
                        'date' => $date,
                    ],
                    [
                        'capacity' => $pkg->capacity_per_slot,
                        'used' => 0,
                    ]
                );
            }
        }

        // Contoh Booking
        $slotSample = Slot::where('package_id', $packages[0]->id)->whereDate('date', $today)->first();
        if ($slotSample && Booking::count() === 0) {
            $booking = Booking::create([
                'booking_code' => 'TS-SAMPLE01',
                'user_id' => $admin->id,
                'slot_id' => $slotSample->id,
                'status' => 'confirmed',
                'total_amount' => 300000,
                'paid_at' => now(),
            ]);

            BookingItem::create([
                'booking_id' => $booking->id,
                'name' => 'Ahmad Fauzi',
                'id_number' => '3501234567890001',
                'phone' => '081234567890',
            ]);

            BookingItem::create([
                'booking_id' => $booking->id,
                'name' => 'Siti Nurhaliza',
                'id_number' => '3501234567890002',
                'phone' => '081234567891',
            ]);

            $slotSample->increment('used', 2);
        }
    }
}
