<?php

namespace App\Http\Requests;

use App\Models\Rsvp;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRsvpRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $merge = [
            'rsvp_name' => trim(strip_tags((string) $this->input('rsvp_name'))),
            'phone_number' => trim(strip_tags((string) $this->input('phone_number'))),
        ];

        if ($this->input('status') === Rsvp::TIDAK_HADIR) {
            $merge['total_pax'] = 0;
        }

        $this->merge($merge);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'rsvp_name' => ['required', 'string', 'max:100'],
            'phone_number' => ['required', 'string', 'max:20', 'regex:/^[0-9+\-\s()]{8,20}$/'],
            'status' => ['required', Rule::in([Rsvp::HADIR, Rsvp::TIDAK_HADIR])],
            'total_pax' => [
                'required',
                'integer',
                $this->input('status') === Rsvp::HADIR ? 'between:1,5' : 'in:0',
            ],
            'website_url' => ['nullable', 'string', 'max:0'],
            'form' => ['nullable', 'string', 'max:20'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'rsvp_name.required' => 'Sila masukkan nama tetamu.',
            'phone_number.required' => 'Sila masukkan nombor telefon.',
            'phone_number.regex' => 'Nombor telefon tidak sah.',
            'status.required' => 'Sila pilih status kehadiran.',
            'status.in' => 'Status kehadiran tidak sah.',
            'total_pax.required' => 'Sila pilih jumlah pax.',
            'total_pax.between' => 'Jumlah pax mestilah antara 1 hingga 5.',
            'total_pax.in' => 'Jumlah pax untuk tidak hadir mestilah 0.',
            'website_url.max' => 'Permintaan tidak sah.',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'rsvp_name' => 'nama tetamu',
            'phone_number' => 'nombor telefon',
            'status' => 'status kehadiran',
            'total_pax' => 'jumlah pax',
        ];
    }
}
