<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\GalleryImage;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'settings' => $this->normalizedSettings(),
            'events' => Event::orderBy('sort_order')->get(),
            'images' => GalleryImage::orderBy('sort_order')->get()->map(fn (GalleryImage $image) => [
                'id' => $image->id,
                'url' => $this->normalizeAssetUrl($image->url),
                'caption' => $image->caption,
                'sort_order' => $image->sort_order,
                'created_at' => $image->created_at,
                'updated_at' => $image->updated_at,
            ]),
        ]);
    }

    public function update(Request $request): JsonResponse
    {
        $data = $request->validate([
            'settings' => ['required', 'array'],
            'events' => ['nullable', 'array'],
            'images' => ['nullable', 'array'],
        ]);

        foreach ($data['settings'] as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        if (isset($data['events'])) {
            Event::query()->delete();

            foreach ($data['events'] as $index => $event) {
                Event::create([
                    'title' => $event['title'],
                    'title_km' => $event['title_km'] ?? null,
                    'event_date' => $event['event_date'],
                    'description' => $event['description'] ?? null,
                    'description_km' => $event['description_km'] ?? null,
                    'sort_order' => $index + 1,
                ]);
            }
        }

        if (isset($data['images'])) {
            GalleryImage::query()->delete();

            foreach ($data['images'] as $index => $image) {
                GalleryImage::create([
                    'url' => $image['url'],
                    'caption' => $image['caption'] ?? null,
                    'sort_order' => $index + 1,
                ]);
            }
        }

        return response()->json([
            'message' => 'Settings updated successfully.',
        ]);
    }

    public function upload(Request $request): JsonResponse
    {
        $data = $request->validate([
            'file' => ['required', 'file', 'max:10240'],
            'folder' => ['nullable', 'in:cover,profile,gallery,audio'],
        ]);

        $folder = $data['folder'] ?? 'gallery';
        $file = $request->file('file');

        if ($folder === 'audio') {
            $extension = strtolower((string) $file->getClientOriginalExtension());
            abort_unless(in_array($extension, ['mp3', 'wav', 'ogg', 'm4a'], true), 422, 'Unsupported audio format.');
        } else {
            abort_unless(str_starts_with((string) $file->getMimeType(), 'image/'), 422, 'Unsupported image format.');
        }

        $path = $file->store("wedding/{$folder}", 'public');

        return response()->json([
            'url' => url('/storage/' . $path),
            'path' => $path,
        ], 201);
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

    private function normalizeAssetUrl(?string $value): ?string
    {
        if (!$value) {
            return $value;
        }

        return preg_replace('#^http://localhost(?=/storage/)#', rtrim(url(''), '/'), $value) ?? $value;
    }
}
