<?php

namespace Database\Seeders;

use App\Models\License;
use App\Models\LicenseKey;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class LicenseSeeder extends Seeder
{
    public function run(): void
    {
        // Create license tiers
        $free     = License::create(['name' => 'free',     'employee_limit' => 3]);
        $basic    = License::create(['name' => 'basic',    'employee_limit' => 15]);
        $business = License::create(['name' => 'business', 'employee_limit' => 30]);

        // Generate sample regular license keys (5 per tier)
        foreach ([$basic, $business] as $license) {
            for ($i = 0; $i < 5; $i++) {
                LicenseKey::create([
                    'license_id'  => $license->id,
                    'license_key' => strtoupper(Str::random(4) . '-' . Str::random(4) . '-' . Str::random(4) . '-' . Str::random(4)),
                    'is_used'     => false,
                    'is_master'   => false,
                ]);
            }
        }

        // ---------------------------------------------------------------
        // MASTER KEYS — super admin use only, never expire / never consumed
        // ---------------------------------------------------------------
        $masterKeys = [
            ['license' => $basic,    'key' => 'MASTER-BASIC-GOTRACKR-2024'],
            ['license' => $business, 'key' => 'MASTER-BUSINESS-GOTRACKR-2024'],
        ];

        foreach ($masterKeys as $mk) {
            LicenseKey::create([
                'license_id'  => $mk['license']->id,
                'license_key' => $mk['key'],
                'is_used'     => false,
                'is_master'   => true,
            ]);
        }

        $this->command->info('✅ Licenses seeded.');
        $this->command->info('');
        $this->command->info('Master Keys (super admin only):');
        $this->command->info('  Basic    → MASTER-BASIC-GOTRACKR-2024');
        $this->command->info('  Business → MASTER-BUSINESS-GOTRACKR-2024');
    }
}
