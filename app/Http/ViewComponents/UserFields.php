<?php

namespace App\Http\ViewComponents;

use Illuminate\Contracts\Support\Htmlable;
use App\{Profession, Skill, User};

class UserFields implements Htmlable
{
    private $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function toHtml()
    {
        return view('users._fields', [
            'professions' => Profession::orderBy('title', 'ASC')->get(),
            'skills' => Skill::orderBy('name', 'ASC')->get(),
            'roles' => trans('users.roles'),
            'user' => $this->user,
        ]);
    }
}