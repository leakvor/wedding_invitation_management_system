<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Guest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class GuestController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Guest::query()->latest();

        if ($status = $request->string('status')->toString()) {
            $query->where('status', $status);
        }

        if ($search = $request->string('search')->toString()) {
            $query->where('name', 'like', '%' . $search . '%');
        }

        $guests = $query->paginate(15);
        $guests->getCollection()->each->ensureInvitationAccess();

        return response()->json($guests);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'invitation_code' => ['nullable', 'string', 'max:50', 'unique:guests,invitation_code'],
            'status' => ['required', 'in:invited,confirmed,attended'],
            'rsvp_status' => ['nullable', 'in:yes,no,coming,pending'],
            'allowed_guests' => ['nullable', 'integer', 'min:1'],
            'money' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
        ]);

        $data['invitation_code'] = $data['invitation_code'] ?? strtoupper(Str::random(8));
        $data['qr_token'] = (string) Str::uuid();
        $data['invitation_slug'] = Str::uuid()->toString();

        $guest = Guest::create($data);

        return response()->json($guest, 201);
    }

    public function show(Guest $guest): JsonResponse
    {
        $guest->ensureInvitationAccess();

        return response()->json($guest->load(['moneyLogs.user', 'comments']));
    }

    public function update(Request $request, Guest $guest): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'invitation_code' => ['required', 'string', 'max:50', 'unique:guests,invitation_code,' . $guest->id],
            'status' => ['required', 'in:invited,confirmed,attended'],
            'rsvp_status' => ['nullable', 'in:yes,no,coming,pending'],
            'allowed_guests' => ['nullable', 'integer', 'min:1'],
            'money' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
        ]);

        $guest->update($data);

        return response()->json($guest->fresh());
    }

    public function destroy(Guest $guest): JsonResponse
    {
        $guest->delete();

        return response()->json([
            'message' => 'Guest deleted.',
        ]);
    }

    public function search(Request $request): JsonResponse
    {
        $name = $request->string('name')->toString();

        $guests = Guest::query()
            ->when($name, fn ($query) => $query->where('name', 'like', '%' . $name . '%'))
            ->limit(20)
            ->get();

        return response()->json($guests);
    }

    public function share(Request $request, Guest $guest): JsonResponse
    {
        $guest->ensureInvitationAccess();

        $frontendBase = rtrim((string) config('app.frontend_url', env('FRONTEND_URL', 'http://localhost:5173')), '/');
        $invitationUrl = $frontendBase . '/invite/' . $guest->invitation_slug;

        return response()->json([
            'url' => $invitationUrl,
            'qr_url' => 'https://api.qrserver.com/v1/create-qr-code/?size=240x240&data=' . urlencode($invitationUrl),
        ]);
    }

    public function export(): StreamedResponse
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=guests.csv',
        ];

        return response()->streamDownload(function (): void {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Name', 'Phone', 'Code', 'Status', 'RSVP', 'Money']);

            Guest::chunk(200, function ($guests) use ($handle): void {
                foreach ($guests as $guest) {
                    fputcsv($handle, [
                        $guest->name,
                        $guest->phone,
                        $guest->invitation_code,
                        $guest->status,
                        $guest->rsvp_status,
                        $guest->money,
                    ]);
                }
            });

            fclose($handle);
        }, 'guests.csv', $headers);
    }
}
