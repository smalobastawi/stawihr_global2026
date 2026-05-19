<?php

namespace App\Events;

use App\Models\LeaveApplication;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class LeaveApplicationEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $leaveApplication;
    public $approverId;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(LeaveApplication $leaveApplication, $approverId)
    {
        //
        $this->leaveApplication = $leaveApplication;
        $this->approverId = $approverId;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        // return new PrivateChannel('channel-name');
        return new Channel('approver' . $this->approverId);
    }

    public function broadcastWith()
    {
        return [
            'message' => 'New leave application received',
            'application_id' => $this->leaveApplication->id,
            'employee_name' => $this->leaveApplication->employee->full_name,
            'dates' => $this->leaveApplication->application_from_date . ' to ' . $this->leaveApplication->application_to_date
        ];
    }
}
