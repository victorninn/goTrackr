<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\TimeLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PayrollController extends Controller
{
    public function index(Request $request)
    {
        $user   = Auth::user();
        $period = $request->get('period', 'monthly');
        $month  = $request->get('month', Carbon::now()->format('Y-m'));
        $week   = $request->get('week', Carbon::now()->startOfWeek()->format('Y-m-d'));

        if ($period === 'weekly') {
            $start = Carbon::parse($week)->startOfWeek();
            $end   = Carbon::parse($week)->endOfWeek();
        } else {
            $start = Carbon::parse($month . '-01')->startOfMonth();
            $end   = Carbon::parse($month . '-01')->endOfMonth();
        }

        $employeeQuery = User::with(['timeLogs' => function ($q) use ($start, $end) {
            $q->whereBetween('date', [$start, $end]);
        }])->whereHas('role', fn($q) => $q->where('name', 'employee'));

        if ($user->isAdmin()) {
            $employeeQuery->where('company_id', $user->company_id);
        }

        $employees = $employeeQuery->get()->map(function ($emp) {
            $totalHours  = $emp->timeLogs->sum('total_hours');
            $totalSalary = round($totalHours * $emp->hourly_rate, 2);

            return [
                'id'           => $emp->id,
                'name'         => $emp->name,
                'hourly_rate'  => $emp->hourly_rate,
                'total_hours'  => $totalHours,
                'total_salary' => $totalSalary,
                'company'      => $emp->company?->name,
            ];
        });

        return view('payroll.index', compact('employees', 'period', 'month', 'week', 'start', 'end'));
    }

    public function export(Request $request)
    {
        $user   = Auth::user();
        $period = $request->get('period', 'monthly');
        $month  = $request->get('month', Carbon::now()->format('Y-m'));
        $week   = $request->get('week', Carbon::now()->startOfWeek()->format('Y-m-d'));

        if ($period === 'weekly') {
            $start = Carbon::parse($week)->startOfWeek();
            $end   = Carbon::parse($week)->endOfWeek();
            $label = 'payroll_weekly_' . $start->format('Ymd');
        } else {
            $start = Carbon::parse($month . '-01')->startOfMonth();
            $end   = Carbon::parse($month . '-01')->endOfMonth();
            $label = 'payroll_monthly_' . $start->format('Ym');
        }

        $employeeQuery = User::with(['timeLogs' => function ($q) use ($start, $end) {
            $q->whereBetween('date', [$start, $end]);
        }])->whereHas('role', fn($q) => $q->where('name', 'employee'));

        if ($user->isAdmin()) {
            $employeeQuery->where('company_id', $user->company_id);
        }

        $employees = $employeeQuery->get();

        $filename = "{$label}.csv";
        $headers  = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($employees) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Employee Name', 'Total Hours', 'Hourly Rate', 'Total Salary']);

            foreach ($employees as $emp) {
                $totalHours  = $emp->timeLogs->sum('total_hours');
                $totalSalary = round($totalHours * $emp->hourly_rate, 2);

                fputcsv($handle, [
                    $emp->name,
                    $totalHours,
                    $emp->hourly_rate,
                    $totalSalary,
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
