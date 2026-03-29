<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class EmployeeController extends Controller
{
    public function index()
    {
        $user  = Auth::user();
        $query = User::with(['role', 'company'])
            ->whereHas('role', fn($q) => $q->where('name', 'employee'));

        if ($user->isAdmin()) {
            $query->where('company_id', $user->company_id);
        }

        $employees = $query->latest()->paginate(20);

        return view('employees.index', compact('employees'));
    }

    public function create()
    {
        $user      = Auth::user();
        $companies = $user->isSuperAdmin() ? Company::all() : Company::where('id', $user->company_id)->get();

        return view('employees.create', compact('companies'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'email'       => 'required|email|unique:users',
            'password'    => 'required|min:8|confirmed',
            'company_id'  => 'required|exists:companies,id',
            'hourly_rate' => 'required|numeric|min:0',
        ]);

        // Admin can only add to their own company
        if ($user->isAdmin()) {
            $data['company_id'] = $user->company_id;
        }

        $employeeRole = Role::where('name', 'employee')->first();

        User::create([
            'name'        => $data['name'],
            'email'       => $data['email'],
            'password'    => Hash::make($data['password']),
            'role_id'     => $employeeRole->id,
            'company_id'  => $data['company_id'],
            'hourly_rate' => $data['hourly_rate'],
        ]);

        return redirect()->route('employees.index')->with('success', 'Employee created successfully.');
    }

    public function edit(User $employee)
    {
        $user      = Auth::user();
        $companies = $user->isSuperAdmin() ? Company::all() : Company::where('id', $user->company_id)->get();

        // Admin can only edit their company's employees
        if ($user->isAdmin() && $employee->company_id !== $user->company_id) {
            abort(403);
        }

        return view('employees.edit', compact('employee', 'companies'));
    }

    public function update(Request $request, User $employee)
    {
        $user = Auth::user();

        if ($user->isAdmin() && $employee->company_id !== $user->company_id) {
            abort(403);
        }

        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'email'       => 'required|email|unique:users,email,' . $employee->id,
            'password'    => 'nullable|min:8|confirmed',
            'company_id'  => 'required|exists:companies,id',
            'hourly_rate' => 'required|numeric|min:0',
        ]);

        if ($user->isAdmin()) {
            $data['company_id'] = $user->company_id;
        }

        $employee->name        = $data['name'];
        $employee->email       = $data['email'];
        $employee->company_id  = $data['company_id'];
        $employee->hourly_rate = $data['hourly_rate'];

        if (!empty($data['password'])) {
            $employee->password = Hash::make($data['password']);
        }

        $employee->save();

        return redirect()->route('employees.index')->with('success', 'Employee updated successfully.');
    }

    public function destroy(User $employee)
    {
        $user = Auth::user();

        if ($user->isAdmin() && $employee->company_id !== $user->company_id) {
            abort(403);
        }

        $employee->delete();

        return redirect()->route('employees.index')->with('success', 'Employee removed.');
    }
}
