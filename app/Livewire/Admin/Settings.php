<?php

namespace App\Livewire\Admin;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

#[Layout('layouts.app')]
class Settings extends Component
{
    use WithFileUploads;

    public $logoUpload;

    public function mount()
    {
        if (!auth()->check() || !auth()->user()->isAdmin) {
            abort(403, 'Unauthorized action.');
        }
    }

    public function updateValue($id, $value)
    {
        if (!auth()->user()->isSuperadmin) {
            return; // Silently fail or abort
        }

        $setting = Setting::find($id);

        if ($setting) {
            // Handle boolean toggle where value might be sent as true/false string or 1/0
            if ($setting->type === 'boolean') {
                $value = filter_var($value, FILTER_VALIDATE_BOOLEAN) ? '1' : '0';
            }

            $setting->update(['value' => $value]);
            Cache::forget("setting.{$setting->key}");

            // Vital: Clear Enterprise License Cache if Company Name, Email, or Key changes
            if (in_array($setting->key, ['app.company_name', 'app.support_contact', 'enterprise_license_key'])) {
                \App\Services\Enterprise\LicenseGuard::clearLicenseCache();
            }

            $this->dispatch('saved'); // For sweetalert or notification
        }
    }

    public function updatedLogoUpload()
    {
        if (!auth()->user()->isSuperadmin) {
            $this->reset('logoUpload');
            return;
        }

        $this->validate([
            'logoUpload' => 'image|mimes:png,jpg,jpeg,webp|max:2048',
        ]);

        $setting = Setting::where('key', Setting::LOGO_KEY)->firstOrFail();
        $old = $setting->value;

        $path = $this->logoUpload->store('logos', 'public');
        $setting->update(['value' => $path]);
        Cache::forget("setting.{$setting->key}");

        if ($old && $old !== $path) {
            Storage::disk('public')->delete($old);
        }

        $this->reset('logoUpload');
        $this->dispatch('saved');
    }

    public function removeLogo()
    {
        if (!auth()->user()->isSuperadmin) {
            return;
        }

        $setting = Setting::where('key', Setting::LOGO_KEY)->first();

        if ($setting) {
            if ($setting->value) {
                Storage::disk('public')->delete($setting->value);
            }
            $setting->update(['value' => null]);
            Cache::forget("setting.{$setting->key}");
            $this->dispatch('saved');
        }
    }

    public function render()
    {
        // The app name comes from APP_NAME in .env, so it isn't editable here
        $groups = Setting::where('key', '!=', 'app.name')->get()->groupBy('group');
        $licenseInfo = \App\Services\Enterprise\LicenseGuard::getLicenseInfo();
        $hwid = \App\Console\Commands\EnterpriseHwId::generate();
        return view('livewire.admin.settings', ['groups' => $groups, 'licenseInfo' => $licenseInfo, 'hwid' => $hwid]);
    }
}
