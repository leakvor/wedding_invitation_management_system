<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Guest;
use App\Models\MoneyGiftLog;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function stats(): JsonResponse
    {
        return response()->json([
            'total_invited' => Guest::count(),
            'total_confirmed' => Guest::where('status', 'confirmed')->count(),
            'total_attended' => Guest::where('status', 'attended')->count(),
            'total_money' => Guest::sum('money'),
            'recent_money_logs' => MoneyGiftLog::with(['guest', 'user'])
                ->latest()
                ->limit(10)
                ->get(),
        ]);
    }
}

