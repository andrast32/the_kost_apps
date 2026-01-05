<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\User;

class UserFormRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {

        $model = $this->route('user') ?? $this->route('petugas');
        $id = $model ? $model->id : null;

        $rules = [
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique(User::class)->ignore($id)],
        ];

        if ($this->isMethod('post')) {
            $rules['password'] = ['required', 'confirmed', 'min:8'];
        } else {
            $rules['password'] = ['nullable', 'confirmed', 'min:8'];
        }

        return $rules;
    }

}
