<?php

namespace App\Services;

use App\Models\Appraisal;
use App\Models\Attendance;
use App\Models\KpiTemplate;
use App\Models\AppraisalEvaluation;
use App\Models\User;

class AppraisalService
{
    /**
     * Calculate attendance score for a user in a given period.
     */
    public function calculateAttendanceScore(User $user, int $month, int $year): int
    {
        $lates = Attendance::where('user_id', $user->id)
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->where('status', 'late')
            ->count();

        $absents = Attendance::where('user_id', $user->id)
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->whereIn('status', ['absent', 'alpha'])
            ->count();

        $excused = Attendance::where('user_id', $user->id)
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->whereIn('status', ['excused', 'sick'])
            ->count();

        $score = 100 - ($lates * 5) - ($absents * 10) - ($excused * 2);
        return max(0, min(100, $score));
    }

    /**
     * Initialize Draft Appraisal for Employee's self assessment.
     */
    public function initAppraisal(User $user, int $month, int $year): Appraisal
    {
        $appraisal = Appraisal::firstOrCreate([
            'user_id' => $user->id,
            'period_month' => $month,
            'period_year' => $year,
        ], [
            'status' => 'self_assessment',
            'attendance_score' => $this->calculateAttendanceScore($user, $month, $year),
        ]);

        $activeKpis = KpiTemplate::where('is_active', true)->get();
        foreach ($activeKpis as $kpi) {
            AppraisalEvaluation::firstOrCreate([
                'appraisal_id' => $appraisal->id,
                'kpi_template_id' => $kpi->id,
            ]);
        }

        return $appraisal;
    }

    /**
     * Finalize Appraisal after Manager Reviews and 1 on 1.
     */
    public function finalizeAppraisal(
        Appraisal $appraisal,
        array $managerScores,
        array $evalComments,
        array $evidenceDescriptions,
        ?string $managerNotes,
        ?string $employeeNotes,
        ?string $developmentRecommendations,
        string $status,
        ?string $meetingDate = null,
        ?string $meetingLink = null
    ): Appraisal {

        // Step 1: Persist all evaluation data
        foreach ($appraisal->evaluations as $eval) {
            $rawScore = $managerScores[$eval->id] ?? '';
            $score = $rawScore === '' ? null : (float) $rawScore;

            $comment = $evalComments[$eval->id] ?? null;
            $evidence = $evidenceDescriptions[$eval->id] ?? null;

            $eval->update([
                'manager_score' => $score,
                'comments' => $comment,
                'evidence_description' => $evidence,
            ]);
        }

        // Step 2: Group-aware weighted calculation
        $appraisal->load('evaluations.kpiTemplate.kpiGroup');

        // Build group buckets: group_id => [evals...]
        $groupBuckets = [];
        foreach ($appraisal->evaluations as $eval) {
            $groupId = $eval->kpiTemplate->kpi_group_id ?? 'ungrouped';
            $groupBuckets[$groupId][] = $eval;
        }

        $totalGroupWeight = 0;
        $weightedGroupScore = 0;

        foreach ($groupBuckets as $groupId => $evals) {
            $group = ($groupId !== 'ungrouped') ? \App\Models\KpiGroup::find($groupId) : null;
            $groupWeight = $group ? $group->weight : 100;

            $childTotalWeight = 0;
            $childWeightedScore = 0;

            foreach ($evals as $eval) {
                $score = $eval->manager_score;
                $templateWeight = $eval->kpiTemplate->weight;

                $childTotalWeight += $templateWeight;
                $childWeightedScore += (($score ?? 0) * ($templateWeight / 100));
            }

            if ($childTotalWeight > 0) {
                $normalizedGroupScore = $childWeightedScore / ($childTotalWeight / 100);
            } else {
                $normalizedGroupScore = 0;
            }

            $totalGroupWeight += $groupWeight;
            $weightedGroupScore += ($normalizedGroupScore * ($groupWeight / 100));
        }

        if ($totalGroupWeight > 0) {
            $overallKpiScore5 = $weightedGroupScore / ($totalGroupWeight / 100);
        } else {
            $overallKpiScore5 = 0;
        }

        // Convert 5-point scale to 100-point scale
        $overallKpiScore100 = $overallKpiScore5 * 20;

        // Step 3: Final score = Attendance portion + KPI portion
        $attendanceWeightPercent = (float) \App\Models\Setting::getValue('appraisal.attendance_weight', 30);
        $attendanceWeight = $attendanceWeightPercent / 100;
        $kpiWeight = 1.0 - $attendanceWeight;

        if (count($groupBuckets) == 0) {
            $finalScore = $appraisal->attendance_score;
        } else {
            $finalScore = ($appraisal->attendance_score * $attendanceWeight) + ($overallKpiScore100 * $kpiWeight);
        }

        $appraisal->update([
            'evaluator_id' => auth()->id(),
            'status' => $status,
            'subjective_score' => round($overallKpiScore100, 2),
            'final_score' => round($finalScore, 2),
            'notes' => $managerNotes,
            'employee_notes' => $employeeNotes,
            'development_recommendation' => $developmentRecommendations,
            'meeting_date' => $meetingDate,
            'meeting_link' => $meetingLink,
        ]);

        return $appraisal;
    }

    /**
     * Get appraisals keyed by user_id for a set of users.
     */
    public function getAppraisalsForUsers(array $userIds, int $month, int $year)
    {
        return Appraisal::whereIn('user_id', $userIds)
            ->where('period_month', $month)
            ->where('period_year', $year)
            ->get()
            ->keyBy('user_id');
    }
}
