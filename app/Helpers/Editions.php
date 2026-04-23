<?php
/**
 * Enterprise Core Secured
 * (c) RiprLutuk
 * Unauthorized modification of this file is prohibited.
 */

namespace App\Helpers;

use App\Contracts\AttendanceServiceInterface;
use App\Contracts\PayrollServiceInterface;
use App\Contracts\ReportingServiceInterface;
use App\Contracts\AuditServiceInterface;
use App\Services\Attendance\CommunityService;
use App\Services\Payroll\CommunityPayrollService;
use App\Services\Reporting\CommunityReportingService;
use App\Services\Audit\CommunityAuditService;

class Editions
{
    /**
     * Check if a specific feature service is running in Community Mode (Locked).
     */
    public static function isLocked(string $contractClass): bool
    {
        // All features unlocked for development
        return false;
    }

    public static function payrollLocked(): bool
    {
        return false;
    }

    public static function reportingLocked(): bool
    {
        return false;
    }
    
    public static function auditLocked(): bool
    {
        return false;
    }
    
    public static function attendanceLocked(): bool
    {
        return false;
    }

    public static function assetLocked(): bool
    {
        return false;
    }

    public static function appraisalLocked(): bool
    {
        return false;
    }
}
