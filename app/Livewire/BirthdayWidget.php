<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class BirthdayWidget extends Component
{
    public function render()
    {
        $today = Carbon::today();
        $endDate = $today->copy()->addDays(7);
        $pgsql = DB::getDriverName() === 'pgsql';
        $doyColumn = $pgsql ? 'EXTRACT(DOY FROM birth_date)' : 'DAYOFYEAR(birth_date)';
        $doyParam = $pgsql ? 'EXTRACT(DOY FROM CAST(? AS date))' : 'DAYOFYEAR(?)';
        
        // Get users with birthdays in the next 7 days
        $upcomingBirthdays = User::whereNotNull('birth_date')
            ->whereRaw("{$doyColumn} BETWEEN {$doyParam} AND {$doyParam}", [
                $today->format('Y-m-d'),
                $endDate->format('Y-m-d')
            ])
            ->orWhereRaw("{$doyColumn} BETWEEN 1 AND {$doyParam} AND {$doyParam} > 358", [
                $endDate->format('Y-m-d'),
                $today->format('Y-m-d')
            ]) // Handle year wrap-around (late December)
            ->orderByRaw($doyColumn)
            ->limit(5)
            ->get()
            ->map(function ($user) use ($today) {
                $birthday = Carbon::parse($user->birth_date)->setYear($today->year);
                if ($birthday->isPast() && !$birthday->isToday()) {
                    $birthday->addYear();
                }
                $user->next_birthday = $birthday;
                $user->days_until = $today->diffInDays($birthday, false);
                return $user;
            })
            ->sortBy('days_until');

        return view('livewire.birthday-widget', [
            'birthdays' => $upcomingBirthdays,
        ]);
    }
}
