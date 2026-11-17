<?php

namespace Database\Seeders;

use App\Models\Wedding;
use App\Support\InvitationTheme;
use Illuminate\Database\Seeder;

class WeddingSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment('local')) {
            $this->command?->warn('Data contoh majlis hanya disemai dalam persekitaran tempatan.');

            return;
        }

        $this->seed(
            [
                'slug' => 'ali-dan-fatimah',
                'groom_short_name' => 'Ali',
                'bride_short_name' => 'Fatimah',
                'groom_full_name' => 'Ali bin Ahmad',
                'bride_full_name' => 'Fatimah binti Hassan',
                'parents_names' => "Ahmad bin Abdullah & Aminah binti Abu\nHassan bin Osman & Rokiah binti Salleh",
                'date' => '2026-11-21',
                'time_start' => '11:00:00',
                'time_end' => '16:00:00',
                'location_name' => 'Dewan Seri Melati',
                'location_address' => "Jalan Ampang\n50450 Kuala Lumpur",
                'maps_url' => 'https://maps.google.com/?q=Dewan+Seri+Melati+Kuala+Lumpur',
                'waze_url' => 'https://www.waze.com/ul?q=Dewan%20Seri%20Melati%20Kuala%20Lumpur&navigate=yes',
                'family_contacts' => [
                    ['name' => 'Ahmad bin Abdullah', 'phone' => '0123456789'],
                    ['name' => 'Aminah binti Abu', 'phone' => '0139876543'],
                ],
                'decoration_settings' => $this->theme('elegant_cream'),
                'is_active' => true,
            ],
            [
                ['guest_name' => 'Siti Aminah', 'phone_number' => '0192233445', 'status' => 'hadir', 'total_pax' => 2],
                ['guest_name' => 'Hakim Rahman', 'phone_number' => '0171122334', 'status' => 'tidak_hadir', 'total_pax' => 0],
            ],
            [
                ['guest_name' => 'Nur Aina', 'message' => 'Semoga berbahagia hingga ke jannah. Tahniah Ali dan Fatimah!'],
                ['guest_name' => 'Farid', 'message' => 'Semoga majlis berjalan lancar dan diberkati Allah.'],
            ],
        );

        $this->seed(
            [
                'slug' => 'amir-aisyah',
                'groom_short_name' => 'Amir',
                'bride_short_name' => 'Aisyah',
                'groom_full_name' => 'Amir bin Ismail',
                'bride_full_name' => 'Aisyah binti Rahman',
                'parents_names' => "Ismail bin Musa & Zainab binti Karim\nRahman bin Yusof & Mariam binti Latif",
                'date' => '2026-12-12',
                'time_start' => '10:30:00',
                'time_end' => '15:30:00',
                'location_name' => 'Dewan Perdana',
                'location_address' => "Persiaran Tasik\n40000 Shah Alam, Selangor",
                'maps_url' => 'https://maps.google.com/?q=Dewan+Perdana+Shah+Alam',
                'waze_url' => 'https://www.waze.com/ul?q=Dewan%20Perdana%20Shah%20Alam&navigate=yes',
                'family_contacts' => [
                    ['name' => 'Ismail bin Musa', 'phone' => '0126677889'],
                ],
                'decoration_settings' => $this->theme('dusty_pink'),
                'is_active' => true,
            ],
            [
                ['guest_name' => 'Hana Sofea', 'phone_number' => '0183344556', 'status' => 'hadir', 'total_pax' => 4],
            ],
            [
                ['guest_name' => 'Irfan', 'message' => 'Tahniah Amir dan Aisyah. Semoga menjadi keluarga yang sakinah.'],
            ],
        );

        $this->seed(
            [
                'slug' => 'hafiz-dan-sarah',
                'groom_short_name' => 'Hafiz',
                'bride_short_name' => 'Sarah',
                'groom_full_name' => 'Hafiz bin Zulkifli',
                'bride_full_name' => 'Sarah binti Kamal',
                'parents_names' => "Zulkifli bin Harun & Salmah binti Omar\nKamal bin Aziz & Noraini binti Rashid",
                'date' => '2027-02-14',
                'time_start' => '12:00:00',
                'time_end' => '17:00:00',
                'location_name' => 'Dewan Warisan',
                'location_address' => "Jalan Sultan Ahmad Shah\n10050 George Town, Pulau Pinang",
                'maps_url' => 'https://maps.google.com/?q=Dewan+Warisan+George+Town',
                'waze_url' => 'https://www.waze.com/ul?q=Dewan%20Warisan%20George%20Town&navigate=yes',
                'family_contacts' => [
                    ['name' => 'Salmah binti Omar', 'phone' => '0145566778'],
                    ['name' => 'Kamal bin Aziz', 'phone' => '0167788990'],
                ],
                'decoration_settings' => $this->theme('sage_green'),
                'is_active' => true,
            ],
            [
                ['guest_name' => 'Aiman', 'phone_number' => '0129900112', 'status' => 'hadir', 'total_pax' => 1],
                ['guest_name' => 'Liyana', 'phone_number' => '0132200334', 'status' => 'hadir', 'total_pax' => 3],
            ],
            [
                ['guest_name' => 'Nadia', 'message' => 'Semoga cinta kalian kekal hingga akhir hayat. Tahniah Hafiz dan Sarah.'],
            ],
        );

        $this->seed(
            [
                'slug' => 'danial-dan-nurul',
                'groom_short_name' => 'Danial',
                'bride_short_name' => 'Nurul',
                'groom_full_name' => 'Danial bin Razak',
                'bride_full_name' => 'Nurul binti Idris',
                'parents_names' => 'Razak bin Hamid & Idris bin Wahab',
                'date' => '2026-08-02',
                'time_start' => '11:00:00',
                'time_end' => '16:00:00',
                'location_name' => 'Dewan Melati',
                'location_address' => "Jalan Dato Onn\n80000 Johor Bahru, Johor",
                'maps_url' => 'https://maps.google.com/?q=Dewan+Melati+Johor+Bahru',
                'waze_url' => 'https://www.waze.com/ul?q=Dewan%20Melati%20Johor%20Bahru&navigate=yes',
                'family_contacts' => [
                    ['name' => 'Razak bin Hamid', 'phone' => '0194455667'],
                ],
                'decoration_settings' => $this->theme('traditional_malay'),
                'is_active' => false,
            ],
            [],
            [
                ['guest_name' => 'Aina', 'message' => 'Semoga sentiasa bahagia, Danial dan Nurul.'],
            ],
        );
    }

    /**
     * @param  array<string, mixed>  $attributes
     * @param  list<array<string, mixed>>  $rsvps
     * @param  list<array<string, mixed>>  $wishes
     */
    private function seed(array $attributes, array $rsvps, array $wishes): void
    {
        $wedding = Wedding::query()->firstOrCreate(
            ['slug' => $attributes['slug']],
            $attributes,
        );

        if (! $wedding->wasRecentlyCreated) {
            return;
        }

        foreach ($rsvps as $rsvp) {
            $wedding->rsvps()->create($rsvp);
        }

        foreach ($wishes as $wish) {
            $wedding->wishes()->create($wish);
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function theme(string $key): array
    {
        $preset = InvitationTheme::THEMES[$key];

        return [
            'theme' => $key,
            'primary_color' => $preset['primary_color'],
            'secondary_color' => $preset['secondary_color'],
            'background_color' => $preset['background_color'],
            'font' => $preset['font'],
            'floral_style' => $preset['floral_style'],
            'music_enabled' => false,
        ];
    }
}
