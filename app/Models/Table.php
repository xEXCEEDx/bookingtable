<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Table extends Model
{
    protected $fillable = [
        'table_number',
        'status',
        'reservation_date',
    ];
    protected $attributes = [
        'status' => 'available',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($table) {
            $table->table_number = static::count() + 1;
        });
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function statuses()
    {
        return $this->hasMany(TableStatus::class);
    }
}
