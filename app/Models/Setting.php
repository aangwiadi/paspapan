<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'group',
        'type',
        'description',
    ];

    public static function getValue($key, $default = null)
    {
        return \Illuminate\Support\Facades\Cache::rememberForever("setting.{$key}", function () use ($key, $default) {
            return self::where('key', $key)->value('value') ?? $default;
        });
    }

    public const LOGO_KEY = 'app.logo';
    public const DEFAULT_LOGO = 'images/icons/logo.png';

    /**
     * Uploaded logo path on the public disk, or null when none was uploaded (or the file is gone).
     */
    protected static function uploadedLogo(): ?string
    {
        $path = self::getValue(self::LOGO_KEY);

        return $path && \Illuminate\Support\Facades\Storage::disk('public')->exists($path) ? $path : null;
    }

    /**
     * Public URL of the system logo (also used for favicon and app icons).
     */
    public static function logoUrl(): string
    {
        $path = self::uploadedLogo();

        return $path ? asset('storage/' . $path) : asset(self::DEFAULT_LOGO);
    }

    /**
     * Short hash that changes whenever the logo changes, for cache busting.
     */
    public static function logoVersion(): string
    {
        $path = self::logoPath();

        return substr(md5($path . (is_file($path) ? filemtime($path) : '')), 0, 10);
    }

    /**
     * Absolute filesystem path of the system logo, for PDFs.
     */
    public static function logoPath(): string
    {
        $path = self::uploadedLogo();

        return $path
            ? \Illuminate\Support\Facades\Storage::disk('public')->path($path)
            : public_path(self::DEFAULT_LOGO);
    }
}
