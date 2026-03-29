<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CompanyController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->isSuperAdmin()) {
            $companies = Company::withCount('users')->latest()->paginate(20);
            return view('companies.index', compact('companies'));
        }

        // Admin: edit their own company
        return redirect()->route('companies.edit', $user->company_id);
    }

    public function create()
    {
        return view('companies.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'logo' => 'nullable|image|max:2048',
        ]);

        $logoPath = null;
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('logos', 'public');
        }

        Company::create([
            'name' => $data['name'],
            'logo' => $logoPath,
        ]);

        return redirect()->route('companies.index')->with('success', 'Company created.');
    }

    public function edit(Company $company)
    {
        $user = Auth::user();

        if ($user->isAdmin() && $user->company_id !== $company->id) {
            abort(403);
        }

        return view('companies.edit', compact('company'));
    }

    public function update(Request $request, Company $company)
    {
        $user = Auth::user();

        if ($user->isAdmin() && $user->company_id !== $company->id) {
            abort(403);
        }

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'logo' => 'nullable|image|max:2048',
        ]);

        $company->name = $data['name'];

        if ($request->hasFile('logo')) {
            // Delete old logo
            if ($company->logo) {
                Storage::disk('public')->delete($company->logo);
            }
            $company->logo = $request->file('logo')->store('logos', 'public');
        }

        $company->save();

        return redirect()->back()->with('success', 'Company updated.');
    }

    public function destroy(Company $company)
    {
        if ($company->logo) {
            Storage::disk('public')->delete($company->logo);
        }
        $company->delete();

        return redirect()->route('companies.index')->with('success', 'Company deleted.');
    }
}
