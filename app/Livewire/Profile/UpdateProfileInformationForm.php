<?php

namespace App\Livewire\Profile;

use App\Models\Wilayah;
use Laravel\Jetstream\Http\Livewire\UpdateProfileInformationForm as JetstreamForm;

class UpdateProfileInformationForm extends JetstreamForm
{
    /**
     * Reset the lower wilayah levels when a higher one changes.
     */
    public function updatedState($value, $key)
    {
        if ($key === 'provinsi_kode') {
            $this->state['kabupaten_kode'] = null;
            $this->state['kecamatan_kode'] = null;
            $this->state['kelurahan_kode'] = null;
        }

        if ($key === 'kabupaten_kode') {
            $this->state['kecamatan_kode'] = null;
            $this->state['kelurahan_kode'] = null;
        }

        if ($key === 'kecamatan_kode') {
            $this->state['kelurahan_kode'] = null;
        }
    }

    public function render()
    {
        $children = fn (?string $parent, int $length) => $parent
            ? Wilayah::where('kode', 'like', $parent . '.%')->whereRaw('LENGTH(kode) = ' . $length)->orderBy('nama')->get()
            : collect();

        return view('profile.update-profile-information-form', [
            'provinces' => Wilayah::whereRaw('LENGTH(kode) = 2')->orderBy('nama')->get(),
            'regencies' => $children($this->state['provinsi_kode'] ?? null, 5),
            'districts' => $children($this->state['kabupaten_kode'] ?? null, 8),
            'villages' => $children($this->state['kecamatan_kode'] ?? null, 13),
        ]);
    }
}
