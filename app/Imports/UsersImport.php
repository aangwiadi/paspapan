<?php

namespace App\Imports;

use App\Models\Division;
use App\Models\Education;
use App\Models\JobTitle;
use App\Models\User;
use App\Models\Wilayah;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Validators\Failure;

class UsersImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use SkipsFailures;

    public function __construct(public bool $save = true)
    {
    }

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // ... existing logic ...
        $division_id = Division::where('name', $row['division'])->first()?->id
            ?? Division::create(['name' => $row['division']])?->id;
        $job_title_id = JobTitle::where('name', $row['job_title'])->first()?->id
            ?? JobTitle::create(['name' => $row['job_title']])?->id;
        $education_id = Education::where('name', $row['education'])->first()?->id
            ?? Education::create(['name' => $row['education']])?->id;
        // Wilayah columns hold names (as produced by the export) or codes; unmatched values become null
        $provinsi = $this->findWilayah($row['provinsi'] ?? null, 2);
        $kabupaten = $this->findWilayah($row['kabupaten'] ?? null, 5, $provinsi);
        $kecamatan = $this->findWilayah($row['kecamatan'] ?? null, 8, $kabupaten);
        $kelurahan = $this->findWilayah($row['kelurahan'] ?? null, 13, $kecamatan);

        $user = (new User)->forceFill([
            'id' => isset($row['id']) ? $row['id'] : null,
            'nip' => (string) $row['nip'],
            'name' => $row['name'],
            'email' => $row['email'],
            'group' => $row['group'] ?? 'user',
            'phone' => (string) $row['phone'],
            'gender' => $row['gender'],
            'basic_salary' => $row['basic_salary'] ?? 0,
            'hourly_rate' => $row['hourly_rate'] ?? 0,
            'birth_date' => $row['birth_date'],
            'birth_place' => $row['birth_place'],
            'address' => $row['address'],
            'provinsi_kode' => $provinsi,
            'kabupaten_kode' => $kabupaten,
            'kecamatan_kode' => $kecamatan,
            'kelurahan_kode' => $kelurahan,
            'education_id' => $education_id,
            'division_id' => $division_id,
            'job_title_id' => $job_title_id,
            'password' => Hash::make($row['password']),
            'created_at' => isset($row['created_at']) ? $row['created_at'] : \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now(),
        ]);
        
        if ($this->save) {
            $user->save();
            \Illuminate\Support\Facades\Log::info('User imported successfully: ' . $user->email);
        }
        return $user;
    }

    /**
     * Resolve a wilayah name or code to its code at the given level (kode length), below $parent when given.
     */
    private function findWilayah(mixed $value, int $length, ?string $parent = null): ?string
    {
        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        $query = Wilayah::whereRaw('LENGTH(kode) = ' . $length);

        if ($parent) {
            $query->where('kode', 'like', $parent . '.%');
        }

        return $query
            ->where(fn ($q) => $q->where('kode', $value)->orWhereRaw('LOWER(nama) = ?', [mb_strtolower($value)]))
            ->value('kode');
    }

    public function rules(): array
    {
        return [
            'nip' => ['required', Rule::unique('users', 'nip')],
            'name' => ['required', 'string'],
            'email' => ['required', 'string', Rule::unique('users', 'email')],
            'phone' => ['nullable', Rule::unique('users', 'phone')],
            'gender' => ['required', 'string'],
            'password' => ['required', 'string'],
        ];
    }
}
