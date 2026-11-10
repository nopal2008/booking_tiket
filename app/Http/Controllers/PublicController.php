<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BookingItem;
use App\Models\Package;
use App\Models\Slot;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PublicController extends Controller
{
    public function home()
    {
        return view('public.home');
    }

    public function packages(Request $request)
    {
        $date = $request->input('date');

        $packages = Package::query()
            ->where('is_active', true)
            ->paginate(15)
            ->withQueryString();

        if ($date) {
            foreach ($packages as $pkg) {
                $slot = Slot::where('package_id', $pkg->id)
                    ->whereDate('date', $date)
                    ->first();

                if ($slot) {
                    $pkg->remaining = max(0, $slot->capacity - $slot->used);
                } else {
                    $pkg->remaining = $pkg->capacity_per_slot;
                }
            }
        }

        return view('public.packages', compact('packages', 'date'));
    }

    public function booking(Request $request, string $slug)
    {
        $package = Package::where('slug', $slug)->where('is_active', true)->firstOrFail();
        $selectedDate = $request->input('date');

        $slots = Slot::where('package_id', $package->id)
            ->whereDate('date', '>=', now()->toDateString())
            ->whereDate('date', '<=', now()->addDays(60)->toDateString())
            ->orderBy('date')
            ->get();

        return view('public.booking', compact('package', 'slots', 'selectedDate'));
    }

    public function storeBooking(Request $request)
    {
        $validated = $request->validate([
            'slot_id' => 'required|exists:slots,id',
            'items' => 'required|array|min:1',
            'items.*.name' => 'required|string|max:255',
            'items.*.id_number' => 'nullable|string|max:32',
            'items.*.phone' => 'nullable|string|max:20',
        ]);

        $booking = DB::transaction(function () use ($validated, $request) {
            $slot = Slot::where('id', $validated['slot_id'])
                ->lockForUpdate()
                ->firstOrFail();

            $participantCount = count($validated['items']);
            $remaining = $slot->capacity - $slot->used;

            if ($remaining < $participantCount) {
                throw new \RuntimeException('Kuota slot tidak mencukupi.');
            }

            $package = $slot->package;
            $totalAmount = (float) $package->price * $participantCount;

            $booking = Booking::create([
                'booking_code' => 'TS-' . strtoupper(Str::random(8)),
                'user_id' => auth()->id(),
                'slot_id' => $slot->id,
                'status' => 'pending', // Status pending (slot ditahan)
                'total_amount' => $totalAmount,
                'paid_at' => null,
            ]);

            foreach ($validated['items'] as $item) {
                BookingItem::create([
                    'booking_id' => $booking->id,
                    'name' => $item['name'],
                    'id_number' => $item['id_number'] ?? null,
                    'phone' => $item['phone'] ?? null,
                ]);
            }

            $slot->increment('used', $participantCount);

            ActivityLogger::log('booking.created', $booking, [
                'booking_code' => $booking->booking_code,
                'participants' => $participantCount,
                'total_amount' => $totalAmount,
            ]);

            return $booking;
        });

        return redirect()->to(url('/payment/' . $booking->booking_code))
            ->with('success', 'Slot Anda berhasil dikunci (Hold 10 menit)! Silakan pilih metode pembayaran sandbox.');
    }

    public function status(Request $request)
    {
        $code = $request->input('code');
        $booking = null;

        if ($code) {
            $booking = Booking::with(['slot.package', 'items'])
                ->where('booking_code', trim($code))
                ->first();
        }

        return view('public.status', compact('code', 'booking'));
    }
}

