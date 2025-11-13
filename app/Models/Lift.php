<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lift extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'location', 'model_number', 'serial_number', 'last_maintenance_date'];

    protected $casts = [
        'last_maintenance_date' => 'date',
    ];

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}
