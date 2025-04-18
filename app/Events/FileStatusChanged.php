<?php

namespace App\Events;

use App\Models\File;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class FileStatusChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $file;
    public $oldStatus;
    public $newStatus;
    public $userName;

    /**
     * Create a new event instance.
     */
    public function __construct(File $file, $oldStatus, $newStatus, $userName)
    {
        $this->file = $file;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
        $this->userName = $userName;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('order.' . $this->file->order_id),
        ];
    }
    
    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'file.status.changed';
    }
    
    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'file_id' => $this->file->id,
            'file_name' => $this->file->original_name,
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
            'user_name' => $this->userName,
            'order_id' => $this->file->order_id,
            'folder_id' => $this->file->folder_id,
            'timestamp' => now()->toIso8601String(),
        ];
    }
}