<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class EmployeeSettingsController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        return view('employees.settings', compact('user'));
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required'],
            'password'         => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'The current password is incorrect.'])->withInput();
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return back()->with('success_password', 'Password updated successfully.');
    }

    public function updatePayment(Request $request)
    {
        $request->validate([
            'payment_method' => ['nullable', 'in:paypal,wise'],
            'paypal_id'      => ['nullable', 'string', 'max:255'],
            'wise_id'        => ['nullable', 'string', 'max:255'],
        ]);

        $user = Auth::user();
        $user->payment_method = $request->payment_method;
        $user->paypal_id      = $request->paypal_id;
        $user->wise_id        = $request->wise_id;
        $user->save();

        return back()->with('success_payment', 'Payment settings updated successfully.');
    }
}
