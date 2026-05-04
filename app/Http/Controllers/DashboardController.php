<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\TimeLog;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->isSuperAdmin()) {
            return $this->superAdminDashboard();
        }

        if ($user->isAdmin()) {
            return $this->adminDashboard();
        }

        return $this->employeeDashboard();
    }

    private function superAdminDashboard()
    {
        $totalUsers     = User::count();
        $totalAdmins    = User::whereHas('role', fn($q) => $q->where('name', 'admin'))->count();
        $totalEmployees = User::whereHas('role', fn($q) => $q->where('name', 'employee'))->count();
        $totalCompanies = Company::count();
        $recentLogs     = TimeLog::with('user.company')->orderBy('date', 'desc')->orderBy('id', 'desc')->take(10)->get();

        return view('dashboard.superadmin', compact(
            'totalUsers', 'totalAdmins', 'totalEmployees', 'totalCompanies', 'recentLogs'
        ));
    }

    private function adminDashboard()
    {
        $user           = Auth::user();
        $company        = $user->company;
        $totalEmployees = User::where('company_id', $user->company_id)
            ->whereHas('role', fn($q) => $q->where('name', 'employee'))
            ->count();

        $weekStart  = Carbon::now()->startOfWeek();
        $weekEnd    = Carbon::now()->endOfWeek();
        $monthStart = Carbon::now()->startOfMonth();
        $monthEnd   = Carbon::now()->endOfMonth();

        $weeklyHours  = TimeLog::whereHas('user', fn($q) => $q->where('company_id', $user->company_id))
            ->whereBetween('date', [$weekStart, $weekEnd])
            ->sum('total_hours');

        $monthlyHours = TimeLog::whereHas('user', fn($q) => $q->where('company_id', $user->company_id))
            ->whereBetween('date', [$monthStart, $monthEnd])
            ->sum('total_hours');

        $recentLogs = TimeLog::with('user')
            ->whereHas('user', fn($q) => $q->where('company_id', $user->company_id))
            ->orderBy('date', 'desc')->orderBy('id', 'desc')->take(10)->get();

        return view('dashboard.admin', compact(
            'company', 'totalEmployees', 'weeklyHours', 'monthlyHours', 'recentLogs'
        ));
    }

    private function employeeDashboard()
    {
        $user       = Auth::user();
        $activeLog  = $user->activeLog();

        $weekStart  = Carbon::now()->startOfWeek();
        $weekEnd    = Carbon::now()->endOfWeek();
        $monthStart = Carbon::now()->startOfMonth();
        $monthEnd   = Carbon::now()->endOfMonth();

        $weeklyHours  = TimeLog::where('user_id', $user->id)
            ->whereBetween('date', [$weekStart, $weekEnd])
            ->sum('total_hours');

        $monthlyHours = TimeLog::where('user_id', $user->id)
            ->whereBetween('date', [$monthStart, $monthEnd])
            ->sum('total_hours');

        $recentLogs = TimeLog::where('user_id', $user->id)
            ->orderBy('date', 'desc')->orderBy('id', 'desc')->take(10)->get();

        return view('dashboard.employee', compact(
            'activeLog', 'weeklyHours', 'monthlyHours', 'recentLogs'
        ));
    }
}