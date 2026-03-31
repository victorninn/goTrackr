<?php

namespace App\Http\Controllers;

use App\Models\License;
use App\Models\LicenseKey;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class LicenseController extends Controller
{
    /**
     * Show the license page.
     * Superadmin → key management dashboard.
     * Admin       → activation form only.
     */
    public function show()
    {
        $user    = Auth::user();
        $company = $user->company()->with('license')->first();
        $licenses    = collect();
        $licenseKeys = collect();

        if ($user->isSuperAdmin()) {
            $licenses    = License::where('name', '!=', 'free')->get();
            $licenseKeys = LicenseKey::with('license')
                ->where('is_master', false)
                ->latest()
                ->get();
        }

        return view('license.show', compact('company', 'licenses', 'licenseKeys'));
    }

    /**
     * Generate one or more license keys (superadmin only).
     */
    public function generate(Request $request)
    {
        abort_unless(Auth::user()->isSuperAdmin(), 403);

        $request->validate([
            'license_id' => 'required|exists:licenses,id',
            'quantity'   => 'required|integer|min:1|max:50',
        ]);

        $quantity = (int) $request->input('quantity');

        for ($i = 0; $i < $quantity; $i++) {
            LicenseKey::create([
                'license_id'  => $request->input('license_id'),
                'license_key' => $this->generateKey(),
                'is_used'     => false,
                'is_master'   => false,
            ]);
        }

        $label = $quantity === 1 ? 'key' : 'keys';

        return redirect()->route('license.show')
            ->with('success', "{$quantity} license {$label} generated successfully.");
    }

    /**
     * Delete an unused license key (superadmin only).
     */
    public function destroyKey(LicenseKey $licenseKey)
    {
        abort_unless(Auth::user()->isSuperAdmin(), 403);

        if ($licenseKey->is_master) {
            return redirect()->route('license.show')
                ->withErrors(['delete' => 'Master keys cannot be deleted.']);
        }

        if ($licenseKey->is_used) {
            return redirect()->route('license.show')
                ->withErrors(['delete' => 'Cannot delete a key that has already been used.']);
        }

        $licenseKey->delete();

        return redirect()->route('license.show')
            ->with('success', 'License key deleted.');
    }

    /**
     * Activate a license key for the authenticated user's company (admin).
     */
    public function activate(Request $request)
    {
        $request->validate([
            'license_key' => 'required|string|max:255',
        ]);

        $key        = strtoupper(trim($request->input('license_key')));
        $licenseKey = LicenseKey::with('license')->where('license_key', $key)->first();

        if (! $licenseKey) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['license_key' => 'Invalid license key. Please check and try again.']);
        }

        // Master key: unlimited use, never consumed
        if ($licenseKey->is_master) {
            $company = Auth::user()->company;
            $company->license_id  = $licenseKey->license_id;
            $company->license_key = $key;
            $company->save();

            return redirect()->route('license.show')
                ->with('success', 'Master license activated! Plan: ' . ucfirst($licenseKey->license->name) . '.');
        }

        // Regular key: must not already be used
        if ($licenseKey->is_used) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['license_key' => 'This license key has already been used.']);
        }

        $company = Auth::user()->company;
        $company->license_id  = $licenseKey->license_id;
        $company->license_key = $key;
        $company->save();

        $licenseKey->is_used = true;
        $licenseKey->used_at = now();
        $licenseKey->save();

        return redirect()->route('license.show')
            ->with('success', 'License activated! Your new plan: ' . ucfirst($licenseKey->license->name) . '.');
    }

    /**
     * Generate a formatted key: XXXX-XXXX-XXXX-XXXX
     */
    private function generateKey(): string
    {
        do {
            $key = strtoupper(
                Str::random(4) . '-' .
                Str::random(4) . '-' .
                Str::random(4) . '-' .
                Str::random(4)
            );
        } while (LicenseKey::where('license_key', $key)->exists());

        return $key;
    }
}
