<?php

namespace App\Http\Requests;

use App\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required',
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore($this->user),
            ],
            'password' => '',
            'bio' => 'required',
            'twitter' => ['nullable', 'url'],
            'profession_id' => [
                'nullable',
                Rule::exists('professions', 'id')->whereNull('deleted_at')
            ],
            'skills' => [
                'array',
                Rule::exists('skills', 'id'),
            ],
            'role' => [
                'required',
            ],
        ];
    }

    public function updateUser(User $user)
    {
        $data = $this->validated();

        if ($data['password'] != null) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }

        $user->fill($data);
        $user->role = $data['role'];
        $user->save();

        $user->profile->update($data);

        $user->skills()->sync($data['skills'] ?? []);
    }
}
