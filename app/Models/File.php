<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'original_name',
        'path',
        'mime_type',
        'size',
        'order_id',
        'folder_id',
        'claimed_by',
        'status',
        'claimed_at',
        'completed_at',
    ];

    protected $casts = [
        'claimed_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function folder()
    {
        return $this->belongsTo(Folder::class);
    }

    public function claimedByUser()
    {
        return $this->belongsTo(User::class, 'claimed_by');
    }

    public function activityLogs()
    {
        return $this->hasMany(FileActivityLog::class);
    }

    public function isClaimed()
    {
        return $this->claimed_by !== null;
    }

    public function isCompleted()
    {
        return $this->status === 'completed';
    }
}
