<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Package extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'price',
        'capacity_per_slot',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'capacity_per_slot' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function slots(): HasMany
    {
        return $this->hasMany(Slot::class);
    }
}

