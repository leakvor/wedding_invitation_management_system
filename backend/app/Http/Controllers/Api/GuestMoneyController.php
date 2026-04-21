<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Guest;
use App\Models\MoneyGiftLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GuestMoneyController extends Controller
{
    public function store(Request $request, Guest $guest): JsonResponse
    {
        $data = $request->validate([
            'amount' => ['required', 'numeric', 'min:0'],
            'note' => ['nullable', 'string', 'max:500'],
        ]);

        $previous = $guest->money ?? 0;

        $guest->update([
            'money' => $data['amount'],
        ]);

        MoneyGiftLog::create([
            'guest_id' => $guest->id,
            'user_id' => $request->user()->id,
            'previous_amount' => $previous,
            'new_amount' => $data['amount'],
            'note' => $data['note'] ?? null,
        ]);

        return response()->json([
            'message' => 'Gift amount updated.',
            'guest' => $guest->fresh(),
        ]);
    }
}

