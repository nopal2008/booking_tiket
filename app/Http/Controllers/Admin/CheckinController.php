<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Checkin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckinController extends Controller
{
    public function index()
    {
        return view('admin.checkin');
    }

    public function scan(Request $request)
    {
        $token = trim($request->input('token', ''));

        if (!$token) {
            return response()->json([
                'ok' => false,
                'message' => 'Token atau kode booking tidak valid.',
            ], 422);
        }

        $booking = Booking::with(['slot.package', 'items'])
            ->where('booking_code', $token)
            ->first();

        if (!$booking) {
            return response()->json([
                'ok' => false,
                'message' => 'Booking tidak ditemukan.',
            ], 404);
        }

        if ($booking->status === 'checked_in') {
            return response()->json([
                'ok' => false,
                'message' => 'Booking sudah pernah di-check in sebelumnya.',
            ], 400);
        }

        if ($booking->status === 'cancelled') {
            return response()->json([
                'ok' => false,
                'message' => 'Booking telah dibatalkan.',
            ], 400);
        }

        if ($booking->status === 'pending') {
            return response()->json([
                'ok' => false,
                'message' => 'Booking belum dibayar.',
            ], 400);
        }

        DB::transaction(function () use ($booking) {
            $booking->update(['status' => 'checked_in']);

            Checkin::create([
                'booking_id' => $booking->id,
                'checked_by' => auth()->id(),
                'checked_at' => now(),
            ]);
        });

        return response()->json([
            'ok' => true,
            'message' => 'Check-in berhasil dikonfirmasi.',
            'booking' => [
                'code' => $booking->booking_code,
                'package' => $booking->slot?->package?->name ?? '-',
                'people' => $booking->items->count(),
            ],
        ]);
    }
}

