<?php

namespace App\Http\Requests;

use App\User;
use Illuminate\Foundation\Http\FormRequest;
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
        $user->fill([
            'name' => $this->name,
            'email' => $this->email,
        ]);

        if ($this->password != null) {
            $user->password = bcrypt($this->password);
        }

        $user->role = $this->role;
        $user->save();

        $user->profile->update([
            'twitter' => $this->twitter,
            'bio' => $this->bio,
            'profession_id' => $this->profession_id,
        ]);

        $user->skills()->sync($this->skills ?: []);
    }
}
