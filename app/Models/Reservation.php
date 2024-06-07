<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'table_id',
        'user_id',
        'name',
        'reservation_time',
        'reservation_date',
        'status',
    ];

    public function table()
    {
        return $this->belongsTo(Table::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tableStatus()
    {
        return $this->hasOne(TableStatus::class, 'table_id', 'table_id')->where('date', $this->reservation_date);
    }
}
