<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;

    protected $table = 'inventory';

    protected $fillable = [
        'part_code',
        'name',
        'quantity',
        'min_quantity',
        'unit',
    ];

    // Helper methods
    public function isLowStock()
    {
        return $this->quantity <= $this->min_quantity;
    }

    // Scopes
    public function scopeLowStock($query)
    {
        return $query->whereColumn('quantity', '<=', 'min_quantity');
    }
}
