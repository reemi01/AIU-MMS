<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'type',
        'equipment',
        'lift_id',
        'chiller_id',
        'equipment_id',
        'frequency',
        'priority',
        'worker_id',
        'status',
        'proof',
        'scheduled_date',
        'scheduled_time',
        'completed_at',
    ];

    protected $casts = [
        'scheduled_date' => 'date',
        'completed_at' => 'datetime',
    ];

    public function worker()
    {
        return $this->belongsTo(Worker::class);
    }

    public function lift()
    {
        return $this->belongsTo(Lift::class);
    }

    public function chiller()
    {
        return $this->belongsTo(Chiller::class);
    }

    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }

    public function reports()
    {
        return $this->hasMany(Report::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'inprogress');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }
}
