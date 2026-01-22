<?php

namespace App\Livewire;

use App\Models\Attendance;
use App\Models\Overtime;
use App\Models\Reimbursement;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class TeamApprovals extends Component
{
    use WithPagination;

    public $activeTab = 'leaves'; // leaves, reimbursements, overtimes
    public $search = '';

    // ... mount and switchTab

    public function approveOvertime($id)
    {
        $overtime = Overtime::find($id);

        if (!$this->isSubordinate($overtime->user_id)) {
            return;
        }

        $overtime->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
        ]);

        $this->dispatch('refresh');
        session()->flash('success', 'Overtime request approved.');
    }

    public function rejectOvertime($id)
    {
        $overtime = Overtime::find($id);

        if (!$this->isSubordinate($overtime->user_id)) {
            return;
        }

        $overtime->update([
            'status' => 'rejected',
            'approved_by' => Auth::id(),
        ]);

        $this->dispatch('refresh');
        session()->flash('success', 'Overtime request rejected.');
    }
    
    // ... existing approve/reject methods for leave/reimbursement

    public function render()
    {
        $user = Auth::user();
        $subordinateIds = $user->subordinates->pluck('id');

        $leaves = collect();
        $reimbursements = collect();
        $overtimes = collect();

        if ($this->activeTab === 'leaves') {
            $leaves = Attendance::whereIn('user_id', $subordinateIds)
                ->where('approval_status', 'pending')
                ->where('status', '!=', 'present') // Only leaves
                ->orderBy('created_at', 'desc')
                ->paginate(10);
        } elseif ($this->activeTab === 'reimbursements') {
            $reimbursements = Reimbursement::whereIn('user_id', $subordinateIds)
                ->where('status', 'pending')
                ->orderBy('created_at', 'desc')
                ->paginate(10);
        } else {
             $overtimes = Overtime::whereIn('user_id', $subordinateIds)
                ->where('status', 'pending')
                ->orderBy('created_at', 'desc')
                ->paginate(10);
        }

        return view('livewire.team-approvals', [
            'leaves' => $leaves,
            'reimbursements' => $reimbursements,
            'overtimes' => $overtimes,
        ])->layout('layouts.app');
    }
}
