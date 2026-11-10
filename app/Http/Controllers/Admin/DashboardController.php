<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Checkin;
use App\Models\Slot;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $data = Cache::remember('admin:dashboard:summary', 60, function () {
            $today = Carbon::today();
            $startOfMonth = Carbon::now()->startOfMonth();

            $bookingsToday = Booking::whereDate('created_at', $today)->count();
            $revenueMonth = Booking::where('created_at', '>=', $startOfMonth)
                ->whereIn('status', ['paid', 'confirmed', 'checked_in'])
                ->sum('total_amount');
            $checkinsToday = Checkin::whereDate('checked_at', $today)->count();
            $pendingCount = Booking::where('status', 'pending')->count();

            $stats = [
                'bookings_today' => $bookingsToday,
                'revenue_month' => (float) $revenueMonth,
                'checkins_today' => $checkinsToday,
                'pending' => $pendingCount,
            ];

            // 14 hari terakhir
            $dailyLabels = [];
            $dailyData = [];
            for ($i = 13; $i >= 0; $i--) {
                $d = Carbon::today()->subDays($i);
                $dailyLabels[] = $d->translatedFormat('d M');
                $count = Booking::whereDate('created_at', $d->toDateString())->count();
                $dailyData[] = $count;
            }

            // Komposisi status
            $statuses = ['pending', 'paid', 'confirmed', 'checked_in', 'cancelled'];
            $dbStatusCounts = Booking::select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->pluck('count', 'status')
                ->all();

            $statusCounts = [];
            foreach ($statuses as $s) {
                $statusCounts[$s] = $dbStatusCounts[$s] ?? 0;
            }

            // Slot terdekat (maks 15)
            $upcoming = Slot::with('package:id,name')
                ->whereDate('date', '>=', Carbon::today())
                ->orderBy('date')
                ->limit(15)
                ->get();

            return compact('stats', 'dailyLabels', 'dailyData', 'statusCounts', 'upcoming');
        });

        return view('admin.dashboard', $data);
    }
}

