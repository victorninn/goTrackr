<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function index()
    {
        $admins = User::with(['company'])
            ->whereHas('role', fn($q) => $q->where('name', 'admin'))
            ->latest()->paginate(20);

        return view('admins.index', compact('admins'));
    }

    public function create()
    {
        $companies = Company::all();
        return view('admins.create', compact('companies'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'       => 'required|string|max:255',
            'email'      => 'required|email|unique:users',
            'password'   => 'required|min:8|confirmed',
            'company_id' => 'required|exists:companies,id',
        ]);

        $adminRole = Role::where('name', 'admin')->first();

        User::create([
            'name'       => $data['name'],
            'email'      => $data['email'],
            'password'   => Hash::make($data['password']),
            'role_id'    => $adminRole->id,
            'company_id' => $data['company_id'],
            'hourly_rate'=> 0,
        ]);

        return redirect()->route('admins.index')->with('success', 'Admin created successfully.');
    }

    public function destroy(User $admin)
    {
        $admin->delete();
        return redirect()->route('admins.index')->with('success', 'Admin removed.');
    }
}
