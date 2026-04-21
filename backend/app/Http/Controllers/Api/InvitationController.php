<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\GalleryImage;
use App\Models\GuestComment;
use App\Models\Guest;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InvitationController extends Controller
{
    public function checkInvitation(Request $request): JsonResponse
    {
        $payload = $request->validate([
            'keyword' => ['required', 'string', 'max:255'],
        ]);

        $keyword = $payload['keyword'];

        $guest = Guest::query()
            ->where('name', 'like', '%' . $keyword . '%')
            ->orWhere('invitation_code', $keyword)
            ->first();

        $guest?->ensureInvitationAccess();

        return response()->json([
            'found' => (bool) $guest,
            'guest' => $guest,
        ]);
    }

    public function updateRsvp(Request $request, Guest $guest): JsonResponse
    {
        $data = $request->validate([
            'rsvp_status' => ['required', 'in:yes,no,coming'],
        ]);

        $guest->update($data);

        if ($data['rsvp_status'] !== 'no' && $guest->status === 'invited') {
            $guest->update(['status' => 'confirmed']);
        }

        return response()->json($guest->fresh());
    }

    public function content(): JsonResponse
    {
        return response()->json([
            'settings' => $this->normalizedSettings(),
            'events' => Event::orderBy('sort_order')->get(),
            'images' => $this->normalizedImages(),
        ]);
    }

    public function show(string $slug): JsonResponse
    {
        $guest = Guest::query()
            ->where('invitation_slug', $slug)
            ->firstOrFail();

        $guest->ensureInvitationAccess();

        return response()->json([
            'guest' => $guest,
            'comments' => GuestComment::query()->latest()->get(),
            'settings' => $this->normalizedSettings(),
            'events' => Event::orderBy('sort_order')->get(),
            'images' => $this->normalizedImages(),
        ]);
    }

    public function storeComment(Request $request, string $slug): JsonResponse
    {
        $guest = Guest::query()
            ->where('invitation_slug', $slug)
            ->firstOrFail();

        $data = $request->validate([
            'author_name' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:2000'],
            'language' => ['required', 'in:en,km'],
        ]);

        $comment = $guest->comments()->create($data);

        return response()->json($comment, 201);
    }

    private function normalizedSettings()
    {
        return Setting::all()
            ->mapWithKeys(function (Setting $setting) {
                $value = in_array($setting->key, ['cover_image_url', 'primary_image_url', 'music_url'], true)
                    ? $this->normalizeAssetUrl($setting->value)
                    : $setting->value;

                return [$setting->key => $value];
            });
    }

    private function normalizedImages()
    {
        return GalleryImage::orderBy('sort_order')->get()->map(fn (GalleryImage $image) => [
            'id' => $image->id,
            'url' => $this->normalizeAssetUrl($image->url),
            'caption' => $image->caption,
            'sort_order' => $image->sort_order,
            'created_at' => $image->created_at,
            'updated_at' => $image->updated_at,
        ]);
    }

    private function normalizeAssetUrl(?string $value): ?string
    {
        if (!$value) {
            return $value;
        }

        return preg_replace('#^http://localhost(?=/storage/)#', rtrim(url(''), '/'), $value) ?? $value;
    }
}
