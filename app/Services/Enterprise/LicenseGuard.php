<?php
/**
 * Enterprise Core — License Bypassed
 */

namespace App\Services\Enterprise;

use Illuminate\Support\Facades\Cache;
use App\Models\Setting;

final class LicenseGuard
{
    public static function check()
    {
        return true;
    }

    public static function hasValidLicense()
    {
        return true;
    }

    public static function clearLicenseCache()
    {
        Cache::forget('ent_lic_status');
        Cache::forget('ent_lic_hash');
    }

    public static function getLicenseInfo()
    {
        return [
            'client' => Setting::getValue('app.company_name', 'Bypassed'),
            'domain' => '*',
            'hwid' => '*',
            'max_users' => 999999,
            'author' => 'Bypassed',
        ];
    }
}
