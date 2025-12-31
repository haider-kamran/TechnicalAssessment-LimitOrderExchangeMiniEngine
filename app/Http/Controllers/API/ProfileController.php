<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfileController extends Controller
{

    public function balance(Request $request)
    {
        $user = $request->user()->load('assets');

        return $this->success(
            'Profile fetched successfully',
            [
                'balance' => $user->balance,
                'assets' => $user->assets->map(fn($asset) => [
                    'symbol' => $asset->symbol,
                    'amount' => $asset->amount,
                    'locked_amount' => $asset->locked_amount,
                ]),
            ]
        );
    }
}
