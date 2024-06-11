<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Table;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\TableStatus;
use App\Models\Image;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $selectedDate = $request->input('date', Carbon::today()->format('Y-m-d'));
    $tables = Table::with(['statuses' => function ($query) use ($selectedDate) {
        $query->where('date', $selectedDate);
    }])->get();

    $image = Image::latest()->first(); // เลือกภาพล่าสุดที่อัพโหลด

    return view('booking', compact('tables', 'selectedDate', 'image'));
}
    public function confirm(Request $request)
    {
        $request->validate([
            'tables' => 'required|array',
            'tables.*' => 'integer|exists:tables,id',
            'date' => 'required|date|after_or_equal:today|before_or_equal:' . Carbon::today()->addDays(2)->format('Y-m-d'),
        ]);

        $tables = $request->input('tables');
        $reservationDate = Carbon::parse($request->input('date'));

        // Check if user has already made a reservation for the selected date
        $existingReservations = Reservation::where('user_id', Auth::id())
                                           ->whereDate('reservation_date', $reservationDate)
                                           ->get();

        if ($existingReservations->isNotEmpty()) {
            return back()->with('error', 'คุณได้จองโต๊ะสำหรับวันนี้ไปแล้ว.');
        }

        foreach ($tables as $tableId) {
            $table = Table::find($tableId);

            if ($table) {
                TableStatus::updateOrCreate(
                    ['table_id' => $table->id, 'date' => $reservationDate],
                    ['status' => 'reserved']
                );

                // Create or update reservation
                Reservation::updateOrCreate(
                    ['table_id' => $table->id, 'reservation_date' => $reservationDate, 'user_id' => Auth::id()],
                    ['name' => Auth::user()->name, 'status' => 'reserved', 'staff_name' => Auth::user()->name]
                );
            }
        }

        return redirect()->route('userreservations')->with('success', 'Tables reserved successfully!');
    }

    public function showDates()
    {   $tables = Table::all();
        $dates = collect();
        for ($i = 0; $i < 2; $i++) { // แสดงวันถัดไป 2 วันเท่านั้น
            $dates->push(Carbon::today()->addDays($i));
        }

        return view('datebooking', compact('dates','tables'));
    }

    public function showBookingForm(Request $request)
    {
        $selectedDate = $request->input('date');
        if (!$selectedDate) {
            $selectedDate = Carbon::today()->addDays(2)->format('Y-m-d');
        }

        $tables = Table::all();
        return view('booking', compact('tables', 'selectedDate'));
    }



    public function reservations()
    {
        $reservations = Reservation::with('table')->where('user_id', Auth::id())->get();

        // ดึงข้อมูล table_status ที่มีการจองหรือไม่
        $reservations->each(function ($reservation) {
            $reservation->table_status = TableStatus::where('table_id', $reservation->table_id)
                                                    ->where('date', $reservation->reservation_date)
                                                    ->first();
        });

        return view('reservations', compact('reservations'));
    }



    public function update(Request $request, $id)
    {
        $request->validate([
            'table_number' => 'required|integer|exists:tables,table_number',
            'status' => 'required|in:available,reserved',
        ]);

        $tableNumber = $request->table_number;
        $status = $request->status;

        // Find the table by table_number
        $table = Table::where('table_number', $tableNumber)->first();

        if (!$table) {
            return back()->withErrors('Table not found.');
        }

        // Update table status
        $table->status = $status;
        $table->save();

        // Find or create reservation entry
        $reservation = Reservation::where('table_id', $table->id)
            ->where('user_id', Auth::id())
            ->whereDate('reservation_date', Carbon::today())
            ->first();

        if (!$reservation) {
            $reservation = new Reservation();
            $reservation->table_id = $table->id;
            $reservation->user_id = Auth::id();
            $reservation->name = Auth::user()->name;
            $reservation->reservation_date = Carbon::now()->toDateString();

        }

        $reservation->status = $status;
        $reservation->save();

        return redirect()->route('reservations')->with('success', 'Table status updated successfully.');
    }

}
