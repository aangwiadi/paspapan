<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class PwaController extends Controller
{
    private const SIZES = [32, 48, 72, 96, 128, 144, 152, 167, 180, 192, 256, 384, 512];

    /**
     * Logo share of the icon canvas per mode:
     * any      - transparent background, used for favicon / install icon
     * maskable - white background with Android safe zone padding
     * apple    - white background (iOS paints transparent areas black)
     */
    private const MODES = ['any' => 0.92, 'maskable' => 0.62, 'apple' => 0.88];

    public function manifest()
    {
        $name = config('app.name');
        $v = Setting::logoVersion();
        $icon = fn (string $mode, int $size) => [
            'src' => route('pwa.icon', ['mode' => $mode, 'size' => $size, 'v' => $v], false),
            'sizes' => "{$size}x{$size}",
            'type' => 'image/png',
            'purpose' => $mode,
        ];

        $shortcutIcons = [[
            'src' => route('pwa.icon', ['mode' => 'any', 'size' => 192, 'v' => $v], false),
            'sizes' => '192x192',
            'type' => 'image/png',
        ]];

        $manifest = [
            'id' => '/',
            'name' => $name,
            'short_name' => Str::limit($name, 12, ''),
            'description' => $name . ' - Absensi GPS & Barcode',
            'lang' => str_replace('_', '-', app()->getLocale()),
            'dir' => 'ltr',
            'start_url' => '/',
            'scope' => '/',
            'display' => 'standalone',
            'display_override' => ['standalone', 'minimal-ui'],
            'orientation' => 'portrait',
            'background_color' => '#ffffff',
            'theme_color' => '#ffffff',
            'categories' => ['business', 'productivity'],
            'icons' => [
                $icon('any', 192),
                $icon('any', 512),
                $icon('maskable', 192),
                $icon('maskable', 512),
            ],
            'shortcuts' => [
                ['name' => 'Scan Absensi', 'short_name' => 'Scan', 'url' => '/scan', 'icons' => $shortcutIcons],
                ['name' => 'Dashboard', 'short_name' => 'Home', 'url' => '/dashboard', 'icons' => $shortcutIcons],
            ],
        ];

        return response()->json($manifest, 200, [
            'Content-Type' => 'application/manifest+json',
            'Cache-Control' => 'public, max-age=3600',
        ], JSON_UNESCAPED_SLASHES);
    }

    public function icon(string $mode, int $size)
    {
        abort_unless(isset(self::MODES[$mode]) && in_array($size, self::SIZES, true), 404);

        $source = Setting::logoPath();
        abort_unless(is_file($source), 404);

        $dir = storage_path('app/pwa-icons');
        $file = "{$dir}/" . Setting::logoVersion() . "-{$mode}-{$size}.png";

        if (!is_file($file)) {
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
            file_put_contents($file, $this->render($source, $size, self::MODES[$mode], $mode !== 'any'));
        }

        return response()->file($file, [
            'Content-Type' => 'image/png',
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }

    private function render(string $source, int $size, float $ratio, bool $whiteBackground): string
    {
        $logo = imagecreatefromstring(file_get_contents($source));
        abort_unless($logo !== false, 404);

        [$lw, $lh] = [imagesx($logo), imagesy($logo)];

        $canvas = imagecreatetruecolor($size, $size);
        imagealphablending($canvas, false);
        imagesavealpha($canvas, true);
        imagefill($canvas, 0, 0, $whiteBackground
            ? imagecolorallocate($canvas, 255, 255, 255)
            : imagecolorallocatealpha($canvas, 0, 0, 0, 127));

        // Fit the logo inside the canvas, keeping its aspect ratio
        $scale = min($size * $ratio / $lw, $size * $ratio / $lh);
        [$w, $h] = [max(1, (int) round($lw * $scale)), max(1, (int) round($lh * $scale))];

        imagealphablending($canvas, true);
        imagecopyresampled($canvas, $logo, (int) (($size - $w) / 2), (int) (($size - $h) / 2), 0, 0, $w, $h, $lw, $lh);

        ob_start();
        imagepng($canvas);

        return ob_get_clean();
    }
}
