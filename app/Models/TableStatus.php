<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TableStatus extends Model
{
    protected $fillable = [
        'table_id',
        'status',
        'date',
    ];

    public function table()
    {
        return $this->belongsTo(Table::class);
    }

    public function reservation()
    {
        return $this->belongsTo(Reservation::class, 'table_id', 'table_id')->where('reservation_date', $this->date);
    }
}
