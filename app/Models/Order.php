<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'status',
        'created_by',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function folders()
    {
        return $this->hasMany(Folder::class);
    }

    public function files()
    {
        return $this->hasMany(File::class);
    }

    public function rootFolders()
    {
        return $this->hasMany(Folder::class)->whereNull('parent_id');
    }
    public function updateStatusBasedOnFiles()
    {
        $total = $this->files()->count();
        $completed = $this->files()->where('status', 'completed')->count();
        $inProgress = $this->files()->where('status', 'in_progress')->count();
        $unclaimed = $this->files()->where('status', 'unclaimed')->count();

        if ($completed === $total) {
            $this->update(['status' => 'completed']);
        } elseif ($inProgress > 0) {
            $this->update(['status' => 'in_progress']);
        } elseif ($unclaimed === $total) {
            $this->update(['status' => 'pending']);
        }
    }
}
