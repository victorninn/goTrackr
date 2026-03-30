<?php

namespace App\Http\Controllers;

use App\Models\TimeLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class TimeLogController extends Controller
{
    // Employee: clock in
    public function clockIn(Request $request)
    {
        $user = Auth::user();

        if ($user->hasActiveClock()) {
            return back()->with('error', 'You already have an active clock-in. Please clock out first.');
        }

        $request->validate([
            'description' => 'nullable|string|max:500',
        ]);

        TimeLog::create([
            'user_id'     => $user->id,
            'date'        => Carbon::today()->toDateString(),
            'clock_in'    => Carbon::now()->toTimeString(),
            'description' => $request->input('description'),
        ]);

        return back()->with('success', 'Clocked in successfully!');
    }

    // Employee: clock out
    public function clockOut(Request $request)
    {
        $user      = Auth::user();
        $activeLog = $user->activeLog();

        if (!$activeLog) {
            return back()->with('error', 'No active clock-in found.');
        }

        $activeLog->clock_out   = Carbon::now()->toTimeString();
        $activeLog->total_hours = $activeLog->computeTotalHours();
        $activeLog->save();

        return back()->with('success', 'Clocked out successfully! Total: ' . $activeLog->total_hours . ' hours.');
    }

    // Admin/Superadmin: view all logs (with filters)
    public function index(Request $request)
    {
        $user  = Auth::user();
        $query = TimeLog::with('user.company');

        // Scope to company for admins
        if ($user->isAdmin()) {
            $query->whereHas('user', fn($q) => $q->where('company_id', $user->company_id));
        }

        // Filters
        if ($request->filled('employee_id')) {
            $query->where('user_id', $request->employee_id);
        }

        if ($request->filled('date_from')) {
            $query->where('date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('date', '<=', $request->date_to);
        }

        $logs = $query->orderBy('date', 'desc')->paginate(20)->withQueryString();

        // Employee list for filter
        $employeesQuery = User::whereHas('role', fn($q) => $q->where('name', 'employee'));
        if ($user->isAdmin()) {
            $employeesQuery->where('company_id', $user->company_id);
        }
        $employees = $employeesQuery->get();

        return view('logs.index', compact('logs', 'employees'));
    }

    // Employee: view own logs
    public function myLogs(Request $request)
    {
        $user  = Auth::user();
        $query = TimeLog::where('user_id', $user->id);

        if ($request->filled('date_from')) {
            $query->where('date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('date', '<=', $request->date_to);
        }

        $logs = $query->orderBy('date', 'desc')->paginate(20)->withQueryString();

        return view('logs.my', compact('logs'));
    }



// Replace the entire export() method with this:
public function export(Request $request)
{
    $user   = Auth::user();
    $type   = $request->get('type', 'monthly');
    $userId = $request->get('user_id');

    $query = TimeLog::with('user');

    if ($user->isEmployee()) {
        $query->where('user_id', $user->id);
    } elseif ($user->isAdmin()) {
        $query->whereHas('user', fn($q) => $q->where('company_id', $user->company_id));
        if ($userId) $query->where('user_id', $userId);
    } else {
        if ($userId) $query->where('user_id', $userId);
    }

    if ($type === 'weekly') {
        $start = Carbon::now()->startOfWeek();
        $end   = Carbon::now()->endOfWeek();
        $label = 'Week of ' . $start->format('M d') . ' – ' . $end->format('M d, Y');
        $filename = 'weekly_logs_' . $start->format('Ymd') . '.pdf';
    } else {
        $start = Carbon::now()->startOfMonth();
        $end   = Carbon::now()->endOfMonth();
        $label = $start->format('F Y');
        $filename = 'monthly_logs_' . $start->format('Ym') . '.pdf';
    }

    $logs = $query->whereBetween('date', [$start, $end])
        ->orderBy('date')
        ->get();

    $totalHours = $logs->sum('total_hours');

    $pdf = Pdf::loadView('logs.export-pdf', compact('logs', 'label', 'type', 'totalHours'))
        ->setPaper('a4', 'landscape');

    return $pdf->download($filename);
}
}