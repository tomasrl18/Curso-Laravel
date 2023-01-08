<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\{Http\Requests\CreateUserRequest, Profession, Skill, User, UserProfile};
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();

        $title = 'Listado de Usuarios';

        return view('users.index', compact('title', 'users'));
    }

    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    public function create()
    {
        $professions = Profession::orderBy('title', 'ASC')->get();

        $skills = Skill::orderBy('name', 'ASC')->get();

        $roles = trans('users.roles');

        $user = new User;

        return view('users.create', compact('professions', 'skills', 'roles', 'user'));
    }

    public function store(CreateUserRequest $request)
    {
        $request->createUser();

        return redirect()->route('users.index');
    }

    public function edit(User $user)
    {
        $professions = Profession::orderBy('title', 'ASC')->get();

        $skills = Skill::orderBy('name', 'ASC')->get();

        $roles = trans('users.roles');

        return view('users.edit', compact('professions', 'skills', 'roles', 'user'));
    }

    public function update(User $user)
    {
        $data = request()->validate([
            'name' => 'required',
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($user->id)
            ],
            'password' => '',
        ]);

        if ($data['password'] != null) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        return redirect()->route('users.show', ['user' => $user]);
    }

    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('users.index');
    }
}