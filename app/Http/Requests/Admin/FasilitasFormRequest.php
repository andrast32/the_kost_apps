<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FasilitasFormRequest extends FormRequest
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
        $fasilitasId = $this->route('id') ?? $this->route('fasilitas');

        return [
            'kode'      =>  [
                                'required',
                                'max:50',
                                Rule::unique('fasilitas', 'kode')->ignore($fasilitasId)
                            ],
            'nama'      =>  'required|string',
            'harga'     =>  'required|numeric|min:0',
            'stok'      =>  'required|numeric|min:0',
            'foto'      =>  'nullable|image|max:10240',
            'deskripsi' =>  'nullable|string'
        ];
    }
}
