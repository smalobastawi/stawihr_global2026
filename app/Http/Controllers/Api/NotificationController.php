<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthenticated.',
            ], 401);
        }

        $perPage = max(1, min(100, (int) $request->get('per_page', 50)));
        $notifications = $user->notifications()->paginate($perPage);

        $items = collect($notifications->items())
            ->map(fn ($notification) => $this->formatNotification($notification))
            ->values();

        return response()->json([
            'status' => 'success',
            'data' => $items,
            'unread_count' => $user->unreadNotifications()->count(),
            'meta' => [
                'current_page' => $notifications->currentPage(),
                'last_page' => $notifications->lastPage(),
                'total' => $notifications->total(),
            ],
        ]);
    }

    public function markAsRead(Request $request, string $id)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthenticated.',
            ], 401);
        }

        $notification = $user->notifications()->where('id', $id)->first();
        if (!$notification) {
            return response()->json([
                'status' => 'error',
                'message' => 'Notification not found.',
            ], 404);
        }

        $notification->markAsRead();

        return response()->json([
            'status' => 'success',
            'message' => 'Notification marked as read.',
        ]);
    }

    public function markAllAsRead(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthenticated.',
            ], 401);
        }

        $user->unreadNotifications->markAsRead();

        return response()->json([
            'status' => 'success',
            'message' => 'All notifications marked as read.',
        ]);
    }

    public function destroy(Request $request, string $id)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthenticated.',
            ], 401);
        }

        $notification = $user->notifications()->where('id', $id)->first();
        if (!$notification) {
            return response()->json([
                'status' => 'error',
                'message' => 'Notification not found.',
            ], 404);
        }

        $notification->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Notification deleted.',
        ]);
    }

    private function formatNotification($notification): array
    {
        $data = is_array($notification->data) ? $notification->data : [];
        $typeClass = class_basename($notification->type);

        return [
            'id' => $notification->id,
            'title' => $this->resolveTitle($typeClass, $data),
            'message' => $data['message'] ?? 'New notification',
            'type' => $this->mapType($typeClass),
            'date' => $notification->created_at?->format('Y-m-d H:i:s'),
            'is_read' => $notification->read_at !== null,
            'action_url' => $this->mapActionUrl($data['link'] ?? null),
        ];
    }

    private function resolveTitle(string $typeClass, array $data): string
    {
        if (!empty($data['title'])) {
            return (string) $data['title'];
        }

        return match ($typeClass) {
            'LeaveApplicationSubmitted' => 'Leave Application',
            'LeaveApplicationApproved' => 'Leave Approved',
            'LeaveApplicationRejected' => 'Leave Rejected',
            default => str($typeClass)->headline()->toString(),
        };
    }

    private function mapType(string $typeClass): string
    {
        $normalized = strtolower($typeClass);

        if (str_contains($normalized, 'leave')) {
            return 'leave';
        }
        if (str_contains($normalized, 'payslip') || str_contains($normalized, 'payroll')) {
            return 'payslip';
        }
        if (str_contains($normalized, 'announcement')) {
            return 'announcement';
        }
        if (str_contains($normalized, 'meeting') || str_contains($normalized, 'training')) {
            return 'meeting';
        }
        if (str_contains($normalized, 'policy') || str_contains($normalized, 'document')) {
            return 'policy';
        }
        if (str_contains($normalized, 'birthday') || str_contains($normalized, 'celebration')) {
            return 'celebration';
        }
        if (str_contains($normalized, 'reminder') || str_contains($normalized, 'timesheet')) {
            return 'reminder';
        }

        return 'general';
    }

    private function mapActionUrl(?string $link): ?string
    {
        if ($link === null || trim($link) === '' || $link === '#') {
            return null;
        }

        $path = parse_url($link, PHP_URL_PATH) ?? $link;
        $path = strtolower($path);

        if (str_contains($path, 'leave')) {
            return '/leave';
        }
        if (str_contains($path, 'payroll') || str_contains($path, 'payslip')) {
            return '/payslips';
        }
        if (str_contains($path, 'attendance')) {
            return '/attendance';
        }
        if (str_contains($path, 'approval')) {
            return '/leave';
        }
        if (str_contains($path, 'disciplinary')) {
            return '/disciplinary';
        }
        if (str_contains($path, 'pip')) {
            return '/pip-plans';
        }

        return null;
    }
}
