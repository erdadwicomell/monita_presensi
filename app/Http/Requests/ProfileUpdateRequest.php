<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'            => ['required', 'string', 'max:255'],
            'email'           => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            'nomor_telepon'   => ['nullable', 'string', 'max:25'],
            'alamat'          => ['nullable', 'string'],
            'jabatan'         => ['nullable', 'string', 'max:100'],
            'nip'             => ['nullable', 'string', 'max:50'],
            'asal_sekolah_pt' => ['nullable', 'string', 'max:255'],
            'nim_nisn'        => ['nullable', 'string', 'max:50'],
            'foto_profil'     => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ];
    }
}
