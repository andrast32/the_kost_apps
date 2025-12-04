<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class KamarFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        if ($this->harga) {
            $this->merge([
                'harga' => str_replace('.', '', $this->harga)
            ]);
        }
    }

    public function rules(): array
    {

        $kamarId = $this->route('id') ?? $this->route('kamar');

        return [
            'kode'          =>  [
                                    'required',
                                    'max:50',
                                    Rule::unique('kamars', 'kode')->ignore($kamarId)
                                ],
            'deskripsi'     => 'nullable|string',
            'harga'         => 'required|numeric|min:0',
            'status'        => 'required|in:Kosong,Terisi,Dalam Perbaikan',
            'khusus'        => 'required|in:Laki-Laki,Perempuan,Keluarga',
            'foto'          => 'nullable|image|max:10240', // Maksimal 10MB
        ];
    }
}
