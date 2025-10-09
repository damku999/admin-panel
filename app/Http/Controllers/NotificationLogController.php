<?php

namespace App\Http\Controllers;

use App\Models\NotificationLog;
use App\Models\NotificationType;
use App\Services\NotificationLoggerService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class NotificationLogController extends Controller
{
    protected NotificationLoggerService $loggerService;

    public function __construct(NotificationLoggerService $loggerService)
    {
        $this->loggerService = $loggerService;
        $this->middleware('auth');
        $this->middleware('permission:notification-log-list')->only(['index']);
        $this->middleware('permission:notification-log-view')->only(['show']);
        $this->middleware('permission:notification-log-resend')->only(['resend', 'bulkResend']);
        $this->middleware('permission:notification-log-analytics')->only(['analytics']);
    }

    /**
     * Display notification logs
     */
    public function index(Request $request)
    {
        $query = NotificationLog::with(['notificationType', 'template', 'sender'])
            ->orderBy('created_at', 'desc');

        // Apply filters
        if ($request->filled('channel')) {
            $query->where('channel', $request->channel);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('notifiable_type')) {
            $query->where('notifiable_type', $request->notifiable_type);
        }

        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('recipient', 'like', '%'.$request->search.'%')
                    ->orWhere('message_content', 'like', '%'.$request->search.'%');
            });
        }

        $logs = $query->paginate(25);

        $notificationTypes = NotificationType::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('admin.notification_logs.index', compact('logs', 'notificationTypes'));
    }

    /**
     * Show notification log details
     */
    public function show(NotificationLog $log)
    {
        $log->load(['notificationType', 'template', 'sender', 'deliveryTracking', 'notifiable']);

        return view('admin.notification_logs.show', compact('log'));
    }

    /**
     * Resend a failed notification
     */
    public function resend(NotificationLog $log)
    {
        try {
            if (! $log->canRetry()) {
                return back()->with('error', 'This notification cannot be retried (max attempts reached or not in failed status).');
            }

            $success = $this->loggerService->retryNotification($log);

            if ($success) {
                return back()->with('success', 'Notification queued for resending.');
            } else {
                return back()->with('error', 'Failed to queue notification for resending.');
            }

        } catch (\Exception $e) {
            Log::error('Failed to resend notification', [
                'log_id' => $log->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Error: '.$e->getMessage());
        }
    }

    /**
     * Bulk resend failed notifications
     */
    public function bulkResend(Request $request)
    {
        $request->validate([
            'log_ids' => 'required|array',
            'log_ids.*' => 'exists:notification_logs,id',
        ]);

        try {
            $logs = NotificationLog::whereIn('id', $request->log_ids)->get();
            $queued = 0;
            $skipped = 0;

            foreach ($logs as $log) {
                if ($log->canRetry()) {
                    $this->loggerService->retryNotification($log);
                    $queued++;
                } else {
                    $skipped++;
                }
            }

            $message = "Queued {$queued} notification(s) for resending.";
            if ($skipped > 0) {
                $message .= " Skipped {$skipped} notification(s) (max retries or invalid status).";
            }

            return back()->with('success', $message);

        } catch (\Exception $e) {
            Log::error('Failed to bulk resend notifications', [
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Error: '.$e->getMessage());
        }
    }

    /**
     * Show analytics dashboard
     */
    public function analytics(Request $request)
    {
        // Default to last 30 days
        $fromDate = $request->filled('from_date')
            ? Carbon::parse($request->from_date)
            : now()->subDays(30);

        $toDate = $request->filled('to_date')
            ? Carbon::parse($request->to_date)
            : now();

        $statistics = $this->loggerService->getStatistics([
            'from_date' => $fromDate,
            'to_date' => $toDate,
        ]);

        // Get daily volume for chart
        $dailyVolume = NotificationLog::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Get channel performance
        $channelPerformance = NotificationLog::selectRaw('channel, status, COUNT(*) as count')
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->groupBy('channel', 'status')
            ->get()
            ->groupBy('channel');

        // Get failed notifications requiring attention
        $failedNotifications = NotificationLog::failed()
            ->where('retry_count', '<', 3)
            ->with(['notificationType', 'template'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('admin.notification_logs.analytics', compact(
            'statistics',
            'dailyVolume',
            'channelPerformance',
            'failedNotifications',
            'fromDate',
            'toDate'
        ));
    }

    /**
     * Delete old notification logs
     */
    public function cleanup(Request $request)
    {
        $request->validate([
            'days_old' => 'required|integer|min:30|max:365',
        ]);

        try {
            $count = $this->loggerService->archiveOldLogs($request->days_old);

            return back()->with('success', "Archived {$count} old notification log(s).");

        } catch (\Exception $e) {
            Log::error('Failed to cleanup notification logs', [
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Error: '.$e->getMessage());
        }
    }
}
