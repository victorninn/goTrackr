<?php

namespace App\Http\Controllers;

use App\Models\TimeLog;
use App\Models\User;
use App\Models\InvoiceShare;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class TimeLogController extends Controller
{


    public function clockIn(Request $request)
    {
        $user = Auth::user();
        if ($user->hasActiveClock()) {
            return back()->with('error', 'You already have an active clock-in. Please clock out first.');
        }
        $request->validate(['description' => 'nullable|string|max:500']);
        TimeLog::create([
            'user_id'     => $user->id,
            'date'        => Carbon::today()->toDateString(),
            'clock_in'    => Carbon::now()->toTimeString(),
            'description' => $request->input('description'),
        ]);
        return back()->with('success', 'Clocked in successfully!');
    }

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

    public function index(Request $request)
    {
        $user  = Auth::user();
        $query = TimeLog::with('user.company');
        if ($user->isAdmin()) {
            $query->whereHas('user', fn($q) => $q->where('company_id', $user->company_id));
        }
        if ($request->filled('employee_id')) $query->where('user_id', $request->employee_id);
        if ($request->filled('date_from'))   $query->where('date', '>=', $request->date_from);
        if ($request->filled('date_to'))     $query->where('date', '<=', $request->date_to);
        $logs = $query->orderBy('date', 'desc')->orderBy('id', 'desc')->paginate(20)->withQueryString();
        $employeesQuery = User::whereHas('role', fn($q) => $q->where('name', 'employee'));
        if ($user->isAdmin()) $employeesQuery->where('company_id', $user->company_id);
        $employees = $employeesQuery->get();
        return view('logs.index', compact('logs', 'employees'));
    }

    public function myLogs(Request $request)
    {
        $user  = Auth::user();
        $query = TimeLog::where('user_id', $user->id);
        if ($request->filled('date_from')) $query->where('date', '>=', $request->date_from);
        if ($request->filled('date_to'))   $query->where('date', '<=', $request->date_to);
        $logs = $query->orderBy('date', 'desc')->orderBy('id', 'desc')->paginate(20)->withQueryString();
        return view('logs.my', compact('logs'));
    }

    public function previewLogs(Request $request)
    {
        $user = Auth::user();
        $type = $request->get('type', 'weekly');
        [$logs, $label, $start, $end] = $this->buildLogQuery($user, $type);
        $totalHours = $logs->sum('total_hours');
        $totalPay   = $logs->sum(fn($l) => $l->total_hours * $user->hourly_rate);
        return view('logs.preview', compact('logs', 'label', 'type', 'totalHours', 'totalPay', 'user', 'start', 'end'));
    }

    public function shareInvoice(Request $request)
    {
        $user = Auth::user();
        $type = $request->get('type', 'monthly');
        [$logs, $label, $start, $end] = $this->buildLogQuery($user, $type);
        InvoiceShare::where('user_id', $user->id)
            ->where('type', $type)
            ->where('period_start', $start->toDateString())
            ->delete();
        $share = InvoiceShare::create([
            'user_id'      => $user->id,
            'token'        => Str::random(48),
            'type'         => $type,
            'period_start' => $start->toDateString(),
            'period_end'   => $end->toDateString(),
            'label'        => $label,
            'expires_at'   => Carbon::now()->addDays(30),
        ]);
        $link = route('invoice.public', $share->token);
        return response()->json(['link' => $link]);
    }

    public function publicInvoice(string $token)
    {
        $share = InvoiceShare::where('token', $token)->firstOrFail();
        if ($share->isExpired()) {
            abort(410, 'This invoice link has expired.');
        }
        $user = $share->user;
        $logs = TimeLog::where('user_id', $user->id)
            ->whereBetween('date', [$share->period_start, $share->period_end])
            ->orderBy('date', 'desc')->orderBy('id', 'desc')
            ->get();
        $totalHours = $logs->sum('total_hours');
        $totalPay   = $logs->sum(fn($l) => $l->total_hours * $user->hourly_rate);
        $label      = $share->label;
        $type       = $share->type;
        return view('logs.invoice-public', compact('logs', 'user', 'label', 'type', 'totalHours', 'totalPay', 'share'));
    }

    public function export(Request $request)
    {
        $user   = Auth::user();
        $type   = $request->get('type', 'monthly');
        $userId = $request->get('user_id');
        $query  = TimeLog::with('user');
        if ($user->isEmployee()) {
            $query->where('user_id', $user->id);
        } elseif ($user->isAdmin()) {
            $query->whereHas('user', fn($q) => $q->where('company_id', $user->company_id));
            if ($userId) $query->where('user_id', $userId);
        } else {
            if ($userId) $query->where('user_id', $userId);
        }
        if ($type === 'weekly') {
            $start    = Carbon::now()->startOfWeek();
            $end      = Carbon::now()->endOfWeek();
            $label    = 'Week of ' . $start->format('M d') . ' – ' . $end->format('M d, Y');
            $filename = 'invoice_weekly_' . $start->format('Ymd') . '.pdf';
        } else {
            $start    = Carbon::now()->startOfMonth();
            $end      = Carbon::now()->endOfMonth();
            $label    = $start->format('F Y');
            $filename = 'invoice_monthly_' . $start->format('Ym') . '.pdf';
        }
        $logs       = $query->whereBetween('date', [$start, $end])->orderBy('date', 'desc')->orderBy('id', 'desc')->get();
        $totalHours = $logs->sum('total_hours');
        $totalPay   = $logs->sum(fn($l) => $l->total_hours * ($l->user->hourly_rate ?? 0));
        $employee   = $user->isEmployee() ? $user : ($logs->first()?->user ?? $user);
        $pdf = Pdf::loadView('logs.export-pdf', compact('logs', 'label', 'type', 'totalHours', 'totalPay', 'employee'))
            ->setPaper('a4', 'portrait');
        return $pdf->download($filename);
    }

    private function buildLogQuery(User $user, string $type): array
    {
        if ($type === 'weekly') {
            $start = Carbon::now()->startOfWeek();
            $end   = Carbon::now()->endOfWeek();
            $label = 'Week of ' . $start->format('M d') . ' – ' . $end->format('M d, Y');
        } else {
            $start = Carbon::now()->startOfMonth();
            $end   = Carbon::now()->endOfMonth();
            $label = $start->format('F Y');
        }
        $logs = TimeLog::where('user_id', $user->id)
            ->whereBetween('date', [$start, $end])
            ->orderBy('date', 'desc')->orderBy('id', 'desc')
            ->get();
        return [$logs, $label, $start, $end];
    }

    public function preview(Request $request)
    {
        $type = $request->get('type', 'weekly');
        $user = auth()->user();

        if ($type === 'weekly') {
            if ($request->filled('week')) {
                $start = Carbon::now()->setISODate(
                    ...array_map('intval', explode('-W', $request->week))
                )->startOfDay();
            } else {
                $start = now()->startOfWeek();
            }
            $end   = $start->copy()->endOfWeek();
            $label = $start->format('M d') . ' – ' . $end->format('M d, Y');
        } else {
            $monthStr = $request->get('month', now()->format('Y-m'));
            $start    = Carbon::createFromFormat('Y-m', $monthStr)->startOfMonth();
            $end      = $start->copy()->endOfMonth();
            $label    = $start->format('F Y');
        }

        $logs = $user->timeLogs()
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->orderBy('date', 'desc')->orderBy('id', 'desc')
            ->get();

        $totalHours = $logs->sum('total_hours');
        $totalPay   = $totalHours * $user->hourly_rate;

        return view('logs.preview', compact('type', 'label', 'logs', 'totalHours', 'totalPay', 'user'));
    }

    public function create()
    {
        $employees = User::all();
        return view('logs.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id'     => 'required|exists:users,id',
            'date'        => 'required|date',
            'clock_in'    => 'required',
            'clock_out'   => 'nullable',
            'description' => 'nullable|string|max:500',
        ]);
        if (!empty($data['clock_out'])) {
            $in  = Carbon::parse($data['date'] . ' ' . $data['clock_in']);
            $out = Carbon::parse($data['date'] . ' ' . $data['clock_out']);
            $data['total_hours'] = round($in->diffInMinutes($out) / 60, 2);
        }
        TimeLog::create($data);
        return redirect()->route('logs.index')->with('success', 'Time log created.');
    }

    public function edit(TimeLog $log)
    {
        $employees = User::all();
        return view('logs.edit', compact('log', 'employees'));
    }

    

    public function update(Request $request, TimeLog $log)
    {
        $data = $request->validate([
            'user_id'     => 'required|exists:users,id',
            'date'        => 'required|date',
            'clock_in'    => 'required',
            'clock_out'   => 'nullable',
            'description' => 'nullable|string|max:500',
        ]);
        if (!empty($data['clock_out'])) {
            $in  = Carbon::parse($data['date'] . ' ' . $data['clock_in']);
            $out = Carbon::parse($data['date'] . ' ' . $data['clock_out']);
            $data['total_hours'] = round($in->diffInMinutes($out) / 60, 2);
        } else {
            $data['total_hours'] = null;
        }
        $log->update($data);
        return redirect()->route('logs.index')->with('success', 'Time log updated.');
    }

    

    public function destroy(TimeLog $log)
    {
        $log->delete();
        return redirect()->route('logs.index')->with('success', 'Time log deleted.');
    }


    public function updateActiveDescription(Request $request)
{
    $request->validate([
        'description' => 'nullable|string|max:500'
    ]);

    $user = Auth::user();
    $log  = $user->activeLog(); // you already use this 👍

    if (!$log) {
        return back()->with('error', 'No active session.');
    }

    $log->description = $request->description;
    $log->save();

    return back()->with('success', 'Description updated.');
}
    
}