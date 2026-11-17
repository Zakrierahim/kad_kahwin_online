<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreWishRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'wish_name' => trim(strip_tags((string) $this->input('wish_name'))),
            'message' => trim(strip_tags((string) $this->input('message'))),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'wish_name' => ['required', 'string', 'max:100'],
            'message' => ['required', 'string', 'min:2', 'max:500'],
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
            'wish_name.required' => 'Sila masukkan nama.',
            'message.required' => 'Sila tulis ucapan.',
            'message.min' => 'Ucapan terlalu pendek.',
            'message.max' => 'Ucapan tidak boleh melebihi 500 aksara.',
            'website_url.max' => 'Permintaan tidak sah.',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'wish_name' => 'nama',
            'message' => 'ucapan',
        ];
    }
}
