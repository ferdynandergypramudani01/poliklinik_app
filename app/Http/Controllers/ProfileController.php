<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdatePasswordRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(): View
    {
        return view('profile.edit');
    }

    public function updatePassword(
        UpdatePasswordRequest $request
    ): RedirectResponse {
        $user = Auth::user();

        $user->password = Hash::make(
            $request->input('password')
        );

        $user->save();

        return redirect()
            ->route('profile.edit')
            ->with('message', 'Password berhasil diubah.')
            ->with('type', 'success');
    }
}
