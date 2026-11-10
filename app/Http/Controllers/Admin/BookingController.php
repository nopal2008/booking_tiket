<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $perPage = min(15, max(5, (int) $request->input('per_page', 15)));
        $status  = $request->input('status');

        $cacheKey = 'bookings:list:' . md5(json_encode([
            'status' => $status,
            'page'   => $request->input('page', 1),
            'per'    => $perPage,
        ]));

        $bookings = Cache::remember($cacheKey, 60, function () use ($status, $perPage) {
            return Booking::query()
                ->select(['id','booking_code','slot_id','user_id','status','total_amount','paid_at','created_at'])
                ->with(['slot:id,package_id,date,capacity,used','slot.package:id,name,price'])
                ->withCount('items')
                ->when($status, fn($q) => $q->where('status', $status))
                ->orderByDesc('created_at')
                ->paginate($perPage)
                ->withQueryString();
        });

        return view('admin.bookings.index', compact('bookings', 'status', 'perPage'));
    }
}
