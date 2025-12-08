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
        $id = $this->route('fasilitas') ?? $this->route('id');

        return [
            'kode'      =>  [
                                'required',
                                'max:50',
                                Rule::unique('fasilitas', 'kode')->ignore($id)
                            ],
            'nama'      =>  'required|string',
            'harga'     =>  'required|string|numeric|min:0',
            'stok'      =>  'required|numeric|min:0',
            'foto'      =>  'nullable|image|max:10240',
            'deskripsi' =>  'nullable|string'
        ];
    }
}
