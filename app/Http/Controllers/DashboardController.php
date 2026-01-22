<?php

namespace App\Http\Controllers;

use App\Models\SmokingLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Check if user smoked today
        $smokedToday = SmokingLog::where('user_id', $user->id)
            ->whereDate('smoked_at', Carbon::today())
            ->where('type', 'smoked')
            ->exists();

        // Calculate Penalty (Total Cost of Smoked Cigarettes)
        $totalSmoked = SmokingLog::where('user_id', $user->id)->sum('quantity');
        $costPerCig = ($user->pack_price ?? 10) / 20;
        $totalPenalty = $totalSmoked * $costPerCig;

        // Calendar Logic
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();
        $today = Carbon::today();

        $monthlyLogs = SmokingLog::where('user_id', $user->id)
            ->whereBetween('smoked_at', [$startOfMonth, $endOfMonth])
            ->where('type', 'smoked') // Only count actual smoking, not resisted
            ->get()
            ->groupBy(function ($date) {
                return Carbon::parse($date->smoked_at)->format('Y-m-d');
            });

        $calendar = [];
        $currentDate = $startOfMonth->copy();

        while ($currentDate <= $endOfMonth) {
            $dateString = $currentDate->format('Y-m-d');
            $dayNumber = $currentDate->day;

            if ($currentDate->isFuture()) {
                $status = 'future';
            } elseif ($monthlyLogs->has($dateString)) {
                $status = 'smoked';
            } else {
                // Check if before user joined? Optional, but simpler to just say CLEAN if they didn't log.
                // Or maybe check quit_date if available. 
                // Let's assume if it's in the past and no log, it's clean.
                $status = 'clean';
            }

            $calendar[] = [
                'day' => $dayNumber,
                'date' => $dateString,
                'status' => $status,
                'is_today' => $currentDate->isToday(),
            ];

            $currentDate->addDay();
        }

        return view('dashboard', compact('smokedToday', 'totalPenalty', 'calendar'));
    }
}

