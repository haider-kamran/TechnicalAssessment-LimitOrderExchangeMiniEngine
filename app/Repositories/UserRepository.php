<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository extends BaseRepository
{
    protected static $model;

    public function model()
    {
        return User::class;
    }

    public static function fetchGetData()
    {
        return User::select('*');
    }

    public static function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    public static function findByRefCode(string $refCode): ?User
    {
        return User::select('id', 'name', 'email')
            ->where('ref_code', $refCode)
            ->first();
    }
}
