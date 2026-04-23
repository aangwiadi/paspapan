<?php

namespace App\Livewire\Admin;

use App\Models\User;
use App\Services\AppraisalService;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class AppraisalManager extends Component
{
    use WithPagination;

    public $month;
    public $year;

    public $search = '';

    // Modal state
    public $showModal = false;
    public ?User $evaluatingUser = null;
    public $activeAppraisalId = null;
    public $attendanceScore = 0;

    // Evaluation Mapping
    public $evaluations = [];
    public $managerScores = [];
    public $evalComments = [];
    public $evidenceDescriptions = [];

    // System Fields
    public $appraisalStatus = 'draft';
    public $meetingDate = null;
    public $meetingLink = null;
    public $generalNotes = '';
    public $employeeNotes = '';
    public $developmentRecommendations = '';

    /**
     * Controller-style entry point used by Route::get('/appraisals', [AppraisalManager::class, 'index']).
     * Returns the Livewire full-page component so Laravel can render it as a normal route.
     */
    public static function index()
    {
        return app('livewire')->new(static::class);
    }

    public function mount()
    {
        $this->month = Carbon::now()->month;
        $this->year = Carbon::now()->year;
    }

    public function updating($property)
    {
        if (in_array($property, ['search', 'month', 'year'])) {
            $this->resetPage();
        }
    }

    public function initOrEvaluate($userId)
    {
        // Check Period Lock
        $periodOpen = (bool) \App\Models\Setting::getValue('appraisal.period_open', false);
        $deadline = \App\Models\Setting::getValue('appraisal.period_deadline', '');
        if (!$periodOpen || ($deadline && now()->gt($deadline))) {
            session()->flash('error', __('The appraisal window is currently closed. Please open it from KPI Settings.'));
            return;
        }

        $this->evaluatingUser = User::findOrFail($userId);

        $service = app(AppraisalService::class);
        $appraisal = $service->initAppraisal($this->evaluatingUser, $this->month, $this->year);
        $appraisal->load('evaluations.kpiTemplate.kpiGroup');

        $this->activeAppraisalId = $appraisal->id;
        $this->appraisalStatus = $appraisal->status;
        $this->attendanceScore = $appraisal->attendance_score;
        $this->meetingDate = $appraisal->meeting_date ? Carbon::parse($appraisal->meeting_date)->format('Y-m-d') : null;
        $this->meetingLink = $appraisal->meeting_link;
        $this->generalNotes = $appraisal->notes;

        $this->evaluations = $appraisal->evaluations;

        // Populate form bindings
        foreach ($this->evaluations as $eval) {
            $this->managerScores[$eval->id] = $eval->manager_score ?? '';
            $this->evalComments[$eval->id] = $eval->comments ?? '';
            $this->evidenceDescriptions[$eval->id] = $eval->evidence_description ?? '';
        }

        $this->employeeNotes = $appraisal->employee_notes;
        $this->developmentRecommendations = $appraisal->development_recommendation;

        $this->showModal = true;
    }

    public function save()
    {
        $this->validate([
            'managerScores.*' => 'nullable|numeric|min:1|max:5',
            'evalComments.*' => 'nullable|string',
            'evidenceDescriptions.*' => 'nullable|string',
            'generalNotes' => 'nullable|string',
            'employeeNotes' => 'nullable|string',
            'developmentRecommendations' => 'nullable|string',
            'appraisalStatus' => 'required|in:self_assessment,manager_review,1on1_scheduled,completed',
            'meetingDate' => 'nullable|date',
            'meetingLink' => 'nullable|url',
        ]);

        $service = app(AppraisalService::class);
        $appraisal = \App\Models\Appraisal::findOrFail($this->activeAppraisalId);

        $oldStatus = $appraisal->status;

        $service->finalizeAppraisal(
            $appraisal,
            $this->managerScores,
            $this->evalComments,
            $this->evidenceDescriptions,
            $this->generalNotes,
            $this->employeeNotes,
            $this->developmentRecommendations,
            $this->appraisalStatus,
            $this->meetingDate,
            $this->meetingLink
        );

        // If completing, set calibration to pending for HR Director review
        if ($this->appraisalStatus === 'completed' && $oldStatus !== 'completed') {
            $appraisal->update(['calibration_status' => 'pending']);
        }

        if ($oldStatus !== $this->appraisalStatus) {
            $msg = '';
            if ($this->appraisalStatus === 'self_assessment') {
                $msg = 'Your manager has initialized an appraisal. Please login to submit your self-assessment score.';
            } elseif ($this->appraisalStatus === '1on1_scheduled') {
                $msg = 'Your manager has scheduled a 1-on-1 meeting to discuss your performance.';
            } elseif ($this->appraisalStatus === 'completed') {
                $msg = 'Your final performance score has been released. Please login to acknowledge the results.';
            }

            if ($msg) {
                $this->evaluatingUser->notify(new \App\Notifications\AppraisalActionNotification(
                    $appraisal,
                    $msg,
                    route('my-performance')
                ));
            }
        }

        $this->showModal = false;
        $this->evaluatingUser = null;

        session()->flash('success', __('Appraisal saved and status updated successfully.'));
    }

    /**
     * Calibration: HR Director / Superadmin approves or rejects a completed appraisal.
     */
    public function calibrate($appraisalId, $decision)
    {
        if (!auth()->user()->isSuperadmin) {
            session()->flash('error', __('Only Superadmin/HR Director can calibrate appraisals.'));
            return;
        }

        $appraisal = \App\Models\Appraisal::findOrFail($appraisalId);
        $appraisal->update([
            'calibrator_id' => auth()->id(),
            'calibration_status' => $decision,
        ]);

        // Notify the direct manager
        if ($appraisal->evaluator) {
            $statusText = $decision === 'approved' ? 'approved' : 'rejected and requires revision';
            $appraisal->evaluator->notify(new \App\Notifications\AppraisalActionNotification(
                $appraisal,
                "The appraisal for {$appraisal->user->name} has been {$statusText} by HR.",
                route('admin.appraisals')
            ));
        }

        session()->flash('success', __('Calibration decision recorded: :status', ['status' => ucfirst($decision)]));
    }

    public function render()
    {
        $admin = auth()->user();
        $query = User::where('group', 'user')->managedBy($admin);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('nip', 'like', '%' . $this->search . '%');
            });
        }

        $users = $query->orderBy('name')->paginate(10);

        $service = app(AppraisalService::class);
        $appraisals = $service->getAppraisalsForUsers(
            $users->pluck('id')->toArray(), $this->month, $this->year
        );

        $months = collect(range(1, 12))->map(fn ($i) => ['id' => (string) $i, 'name' => __(date('F', mktime(0, 0, 0, $i, 10)))])->values()->all();
        $years = collect(range(date('Y') - 2, date('Y') + 1))->map(fn ($i) => ['id' => (string) $i, 'name' => (string) $i])->values()->all();

        // Bell Curve Distribution Data
        $allScores = \App\Models\Appraisal::where('period_month', $this->month)
            ->where('period_year', $this->year)
            ->whereNotNull('final_score')
            ->pluck('final_score')
            ->toArray();

        $bellCurve = [
            'A' => count(array_filter($allScores, fn ($s) => $s >= 90)),
            'B' => count(array_filter($allScores, fn ($s) => $s >= 80 && $s < 90)),
            'C' => count(array_filter($allScores, fn ($s) => $s >= 70 && $s < 80)),
            'D' => count(array_filter($allScores, fn ($s) => $s >= 60 && $s < 70)),
            'E' => count(array_filter($allScores, fn ($s) => $s < 60)),
        ];

        $periodOpen = (bool) \App\Models\Setting::getValue('appraisal.period_open', false);
        $periodLabel = \App\Models\Setting::getValue('appraisal.period_label', '');

        return view('livewire.admin.appraisal-manager', compact('users', 'appraisals', 'months', 'years', 'bellCurve', 'periodOpen', 'periodLabel'));
    }
}
