<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\GalleryImage;
use App\Models\Guest;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class WeddingSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
            ]
        );

        Setting::query()->upsert([
            ['key' => 'bride_name', 'value' => 'Sophia'],
            ['key' => 'groom_name', 'value' => 'Daniel'],
            ['key' => 'hero_message', 'value' => 'Together with our families, we invite you to celebrate our wedding day.'],
            ['key' => 'hero_message_km', 'value' => 'សូមអញ្ជើញលោកអ្នកចូលរួមក្នុងពិធីមង្គលការរបស់យើងខ្ញុំដោយក្តីគោរព។'],
            ['key' => 'venue_name', 'value' => 'Rosewood Garden Hall'],
            ['key' => 'venue_name_km', 'value' => 'សាលារ៉ូសវូដ ហ្គាឌិន'],
            ['key' => 'venue_address', 'value' => '123 Celebration Ave, Bangkok'],
            ['key' => 'venue_address_km', 'value' => '123 Celebration Ave, Bangkok'],
            ['key' => 'google_map_url', 'value' => 'https://maps.google.com'],
            ['key' => 'wedding_datetime', 'value' => '2026-12-20 16:00:00'],
            ['key' => 'cover_title_en', 'value' => 'Dear Sir/Madam,'],
            ['key' => 'cover_title_km', 'value' => 'ជូនចំពោះភ្ញៀវកិត្តិយស'],
            ['key' => 'cover_subtitle_en', 'value' => 'We cordially invite you to attend our wedding ceremony.'],
            ['key' => 'cover_subtitle_km', 'value' => 'យើងខ្ញុំសូមគោរពអញ្ជើញលោកអ្នកចូលរួមពិធីមង្គលការរបស់យើង។'],
            ['key' => 'background_mode', 'value' => 'dark'],
            ['key' => 'primary_image_url', 'value' => 'https://images.unsplash.com/photo-1522673607200-164d1b6ce486'],
            ['key' => 'cover_image_url', 'value' => 'https://images.unsplash.com/photo-1519741497674-611481863552?auto=format&fit=crop&w=1400&q=80'],
            ['key' => 'wishes_title_en', 'value' => 'Guest Wishes'],
            ['key' => 'wishes_title_km', 'value' => 'សារជូនពរ'],
            ['key' => 'music_title', 'value' => 'Wedding Theme'],
            ['key' => 'music_url', 'value' => ''],
        ], ['key'], ['value']);

        Event::query()->delete();
        Event::insert([
            [
                'title' => 'Guest Arrival',
                'title_km' => 'ការមកដល់របស់ភ្ញៀវ',
                'event_date' => '2026-12-20 15:00:00',
                'description' => 'Registration and welcome drinks.',
                'description_km' => 'ចុះឈ្មោះ និងទទួលភេសជ្ជៈស្វាគមន៍។',
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Ceremony',
                'title_km' => 'ពិធីមង្គលការ',
                'event_date' => '2026-12-20 16:00:00',
                'description' => 'Wedding ceremony begins.',
                'description_km' => 'ចាប់ផ្តើមពិធីមង្គលការ។',
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Dinner & Celebration',
                'title_km' => 'អាហារពេលល្ងាច និងការអបអរ',
                'event_date' => '2026-12-20 18:00:00',
                'description' => 'Dinner, speeches, and party.',
                'description_km' => 'អាហារពេលល្ងាច សុន្ទរកថា និងការអបអរ។',
                'sort_order' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        GalleryImage::query()->delete();
        GalleryImage::insert([
            [
                'url' => 'https://images.unsplash.com/photo-1522673607200-164d1b6ce486',
                'caption' => 'Engagement portrait',
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'url' => 'https://images.unsplash.com/photo-1511285560929-80b456fea0bc',
                'caption' => 'Celebration',
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        if (!Guest::query()->exists()) {
            foreach (['Olivia Chen', 'Noah Patel', 'Ava Lim'] as $name) {
                Guest::create([
                    'name' => $name,
                    'phone' => null,
                    'invitation_code' => strtoupper(Str::random(8)),
                    'status' => 'invited',
                    'rsvp_status' => 'pending',
                    'allowed_guests' => 2,
                    'money' => 0,
                    'qr_token' => (string) Str::uuid(),
                    'invitation_slug' => Str::uuid()->toString(),
                    'notes' => 'VIP table',
                ]);
            }
        }
    }
}
