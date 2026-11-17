<?php

namespace App\Http\Requests\Admin;

use App\Models\Wedding;
use App\Support\InvitationTheme;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class WeddingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    protected function prepareForValidation(): void
    {
        $groom = $this->plain('groom_short_name');
        $bride = $this->plain('bride_short_name');
        $slug = trim((string) $this->input('slug'));

        if ($slug === '') {
            $slug = $groom.'-dan-'.$bride;
        }

        $this->merge([
            'groom_short_name' => $groom,
            'bride_short_name' => $bride,
            'groom_full_name' => $this->plain('groom_full_name'),
            'bride_full_name' => $this->plain('bride_full_name'),
            'parents_names' => trim(strip_tags((string) $this->input('parents_names'))) ?: null,
            'hashtag' => $this->hashtag($this->input('hashtag')),
            'slug' => \Illuminate\Support\Str::slug($slug),
            'location_name' => $this->plain('location_name'),
            'location_address' => trim(strip_tags((string) $this->input('location_address'))),
            'maps_url' => trim((string) $this->input('maps_url')) ?: null,
            'waze_url' => trim((string) $this->input('waze_url')) ?: null,
            'bg_music_url' => trim((string) $this->input('bg_music_url')) ?: null,
            'primary_color' => strtoupper(trim((string) $this->input('primary_color'))),
            'secondary_color' => strtoupper(trim((string) $this->input('secondary_color'))),
            'background_color' => strtoupper(trim((string) $this->input('background_color'))),
            'is_active' => $this->boolean('is_active'),
            'music_enabled' => $this->boolean('music_enabled'),
            'remove_cover' => $this->boolean('remove_cover'),
            'remove_music' => $this->boolean('remove_music'),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $weddingId = $this->route('wedding')?->getKey();

        return [
            'groom_short_name' => ['required', 'string', 'max:80'],
            'bride_short_name' => ['required', 'string', 'max:80'],
            'groom_full_name' => ['required', 'string', 'max:160'],
            'bride_full_name' => ['required', 'string', 'max:160'],
            'parents_names' => ['nullable', 'string', 'max:2000'],
            'hashtag' => ['nullable', 'regex:/^#[A-Za-z0-9_]{2,39}$/'],
            'date' => ['required', 'date'],
            'time_start' => ['required', 'date_format:H:i'],
            'time_end' => ['required', 'date_format:H:i', 'after:time_start'],
            'location_name' => ['required', 'string', 'max:160'],
            'location_address' => ['required', 'string', 'max:2000'],
            'maps_url' => ['nullable', 'url', 'max:500', 'regex:/^https:\/\//i'],
            'waze_url' => ['nullable', 'url', 'max:500', 'regex:/^https:\/\//i'],
            'contacts' => ['nullable', 'array', 'max:12'],
            'contacts.*.name' => ['nullable', 'string', 'max:100'],
            'contacts.*.phone' => ['nullable', 'string', 'max:20'],
            'theme' => ['required', Rule::in(array_keys(InvitationTheme::THEMES))],
            'primary_color' => ['required', 'regex:/^#[0-9A-F]{6}$/'],
            'secondary_color' => ['required', 'regex:/^#[0-9A-F]{6}$/'],
            'background_color' => ['required', 'regex:/^#[0-9A-F]{6}$/'],
            'font' => ['required', Rule::in(array_keys(InvitationTheme::FONTS))],
            'floral_style' => ['required', Rule::in(array_keys(InvitationTheme::FLORALS))],
            'bg_music_url' => ['nullable', 'url', 'max:500', 'regex:/^https:\/\//i'],
            'music_file' => ['nullable', 'file', 'max:2048', function (string $attribute, mixed $value, \Closure $fail): void {
                if (! $value instanceof \Illuminate\Http\UploadedFile) {
                    return;
                }

                $extension = strtolower($value->getClientOriginalExtension());
                $detected = (string) $value->getMimeType();
                $client = (string) $value->getClientMimeType();
                $allowedExtensions = ['mp3', 'wav', 'ogg', 'm4a'];
                $allowedMimes = ['audio/mpeg', 'audio/mp3', 'audio/wav', 'audio/x-wav', 'audio/wave', 'audio/ogg', 'audio/mp4', 'audio/x-m4a', 'audio/aac', 'application/octet-stream'];

                if (! in_array($extension, $allowedExtensions, true)) {
                    $fail('Fail muzik mestilah MP3, WAV, OGG atau M4A yang sah.');

                    return;
                }

                if (
                    str_starts_with($detected, 'text/')
                    || str_contains($detected, 'php')
                    || str_contains($detected, 'javascript')
                    || str_contains($detected, 'html')
                    || $detected === 'image/svg+xml'
                ) {
                    $fail('Jenis fail muzik tidak dibenarkan.');

                    return;
                }

                if (! in_array($detected, $allowedMimes, true) && ! in_array($client, $allowedMimes, true)) {
                    $fail('Jenis fail muzik tidak dibenarkan.');
                }
            }],
            'music_enabled' => ['boolean'],
            'remove_music' => ['boolean'],
            'cover_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'remove_cover' => ['boolean'],
            'slug' => [
                'required',
                'string',
                'max:120',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('weddings', 'slug')->ignore($weddingId),
                Rule::notIn(Wedding::RESERVED_SLUGS),
            ],
            'is_active' => ['boolean'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            foreach ($this->input('contacts', []) as $index => $contact) {
                if (! is_array($contact)) {
                    continue;
                }

                $name = trim((string) ($contact['name'] ?? ''));
                $phone = trim((string) ($contact['phone'] ?? ''));

                if ($name === '' && $phone === '') {
                    continue;
                }

                if ($name === '' || $phone === '') {
                    $validator->errors()->add("contacts.$index.name", 'Nama dan nombor telefon perlu diisi bersama.');
                }

                if ($phone !== '' && preg_match('/^[0-9+\-\s()]{8,20}$/', $phone) !== 1) {
                    $validator->errors()->add("contacts.$index.phone", 'Nombor telefon tidak sah.');
                }
            }
        });
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'slug.unique' => 'Pautan ini sudah digunakan. Sila pilih slug yang lain.',
            'slug.not_in' => 'Slug ini tidak boleh digunakan.',
            'slug.regex' => 'Slug hanya boleh mengandungi huruf kecil, nombor dan tanda sempang.',
            'time_end.after' => 'Masa tamat mestilah selepas masa mula.',
            'maps_url.regex' => 'Pautan Google Maps mestilah URL https yang sah.',
            'waze_url.regex' => 'Pautan Waze mestilah URL https yang sah.',
            'bg_music_url.regex' => 'Pautan muzik mestilah URL https yang sah.',
            'cover_image.image' => 'Gambar muka depan mestilah fail imej yang sah.',
            'cover_image.mimes' => 'Gambar muka depan mestilah JPG, PNG atau WEBP.',
            'cover_image.max' => 'Gambar muka depan tidak boleh melebihi 2MB.',
            'music_file.max' => 'Fail muzik tidak boleh melebihi 2MB.',
            'primary_color.regex' => 'Warna utama mestilah kod heks yang sah.',
            'secondary_color.regex' => 'Warna kedua mestilah kod heks yang sah.',
            'background_color.regex' => 'Warna latar mestilah kod heks yang sah.',
            'hashtag.regex' => 'Hashtag hanya boleh mengandungi huruf, nombor dan garis bawah. Contoh: #imandalamsyifa.',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'groom_short_name' => 'nama ringkas pengantin lelaki',
            'bride_short_name' => 'nama ringkas pengantin perempuan',
            'groom_full_name' => 'nama penuh pengantin lelaki',
            'bride_full_name' => 'nama penuh pengantin perempuan',
            'parents_names' => 'nama ibu bapa',
            'hashtag' => 'hashtag',
            'date' => 'tarikh majlis',
            'time_start' => 'masa mula',
            'time_end' => 'masa tamat',
            'location_name' => 'nama lokasi',
            'location_address' => 'alamat',
            'maps_url' => 'pautan Google Maps',
            'waze_url' => 'pautan Waze',
            'theme' => 'tema',
            'font' => 'tipografi',
            'floral_style' => 'hiasan bunga',
            'slug' => 'slug',
            'cover_image' => 'gambar muka depan',
            'music_file' => 'fail muzik',
            'bg_music_url' => 'pautan muzik',
        ];
    }

    private function hashtag(mixed $value): ?string
    {
        $hashtag = trim(strip_tags((string) $value));
        $hashtag = preg_replace('/\s+/', '', $hashtag) ?? '';

        if ($hashtag === '' || $hashtag === '#') {
            return null;
        }

        if (! str_starts_with($hashtag, '#')) {
            $hashtag = '#'.$hashtag;
        }

        return $hashtag;
    }

    private function plain(string $key): string
    {
        return trim(preg_replace('/\s+/', ' ', strip_tags((string) $this->input($key))) ?? '');
    }
}
