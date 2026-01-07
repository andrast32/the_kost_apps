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
            'name'  => ['required', 'string', 'max:255']
        ];

        return $rules;
    }

}
