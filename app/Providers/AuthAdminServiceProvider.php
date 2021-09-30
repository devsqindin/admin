<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\EloquentUserProvider as UserProvider;
use Illuminate\Contracts\Auth\Authenticatable as UserContract;
use App\Operador;
use Illuminate\Support\Facades\Hash;

class AuthAdminServiceProvider extends UserProvider
{

    public function validateCredentials(UserContract $user, array $credentials) {
        return Hash::check($credentials['password'], $user->password);
    }

    public function retrieveByCredentials(array $credentials){
        return Operador::get()->where('email',$credentials['email'])->first();
    }
}
