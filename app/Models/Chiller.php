<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property mixed $id
 */
class Chiller extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'location', 'model_number', 'serial_number', 'last_maintenance_date'];

    protected $casts = [
        'last_maintenance_date' => 'date',
    ];

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }
}
