<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function show(string $code)
    {
        $booking = Booking::with(['slot.package', 'items'])
            ->where('booking_code', $code)
            ->firstOrFail();

        if (in_array($booking->status, ['confirmed', 'paid', 'checked_in'])) {
            return redirect()->to(url('/status?code=' . $booking->booking_code))
                ->with('success', 'Reservasi ini telah terbayar dan aktif.');
        }

        return view('public.payment', compact('booking'));
    }

    public function simulate(Request $request, string $code)
    {
        $booking = Booking::with(['slot.package', 'items'])
            ->where('booking_code', $code)
            ->firstOrFail();

        $method = $request->input('method', 'QRIS');

        $booking->update([
            'status' => 'confirmed',
            'paid_at' => now(),
        ]);

        ActivityLogger::log('payment.simulated', $booking, [
            'method' => $method,
            'amount' => $booking->total_amount,
            'booking_code' => $booking->booking_code,
        ]);

        return redirect()->to(url('/status?code=' . $booking->booking_code))
            ->with('success', 'Pembayaran via ' . strtoupper($method) . ' berhasil disimulasikan! E-Voucher resmi Anda telah aktif.');
    }
}

