<?php

namespace App\Livewire;

use App\Models\Overtime;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class OvertimeRequest extends Component
{
    use WithPagination;

    public $date;
    public $start_time;
    public $end_time;
    public $reason;
    public $showModal = false;

    protected $rules = [
        'date' => 'required|date',
        'start_time' => 'required|date_format:H:i',
        'end_time' => 'required|date_format:H:i', // Removed after:start_time to allow crossing midnight
        'reason' => 'required|string|min:5',
    ];

    public function render()
    {
        $overtimes = Overtime::where('user_id', Auth::id())
            ->orderBy('date', 'desc')
            ->orderBy('start_time', 'desc')
            ->paginate(10);

        return view('livewire.overtime-request', [
            'overtimes' => $overtimes
        ])->layout('layouts.app');
    }

    public function create()
    {
        $this->reset(['date', 'start_time', 'end_time', 'reason']);
        $this->showModal = true;
    }

    public function store()
    {
        $this->validate();

        // Calculate duration in minutes
        $start = \Carbon\Carbon::parse($this->date . ' ' . $this->start_time);
        $end = \Carbon\Carbon::parse($this->date . ' ' . $this->end_time);

        // Handle cross-day overtime (e.g. 23:00 to 02:00)
        if ($end->lt($start)) {
            $end->addDay();
        }

        $duration = $start->diffInMinutes($end);

        Overtime::create([
            'user_id' => Auth::id(),
            'date' => $this->date,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'duration' => $duration,
            'reason' => $this->reason,
            'status' => 'pending',
        ]);

        $this->showModal = false;
        $this->reset(['date', 'start_time', 'end_time', 'reason']);
        session()->flash('success', 'Overtime request submitted successfully.');
    }

    public function close()
    {
        $this->showModal = false;
    }
}
