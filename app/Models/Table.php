<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Table extends Model
{
    protected $fillable = [
        'status',
    ];

    protected $attributes = [
        'status' => 'available',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($table) {
            // กำหนดค่า table_number เป็น auto-increment ของแถวใหม่
            $table->table_number = static::count() + 1;
        });
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
}
