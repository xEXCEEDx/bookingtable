<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Table;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $selectedDate = $request->input('date', Carbon::today()->format('Y-m-d'));
        $tables = Table::all();
        return view('booking', compact('tables', 'selectedDate'));
    }

    public function showDates()
    {
        $dates = collect();
        for ($i = 0; $i < 2; $i++) { // แสดงวันถัดไป 2 วันเท่านั้น
            $dates->push(Carbon::today()->addDays($i));
        }

        return view('datebooking', compact('dates'));
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

    // ตรวจสอบการจองโต๊ะและจัดเก็บข้อมูล
    public function confirm(Request $request)
    {
        // Validate input
        $request->validate([
            'tables' => 'required|array',
            'tables.*' => 'integer|exists:tables,id',
            'date' => 'required|date|after_or_equal:today|before_or_equal:' . Carbon::today()->addDays(2)->format('Y-m-d'),
        ]);

        // Count existing reservations for the user on the selected date
        $reservationCount = Reservation::where('user_id', Auth::id())
            ->whereDate('reservation_date', $request->input('date'))
            ->count();

        // if ($reservationCount > 0) {
        //     return redirect()->back()->with('warning', 'You have already made a reservation for this date.');
        // }

        // Count tables selected for reservation
        $tables = $request->input('tables');
        if (count($tables) > 5) {
            return redirect()->back()->with('error', 'You cannot reserve more than 5 tables per day.');
        }

        // Proceed with reservation
        $reservationDate = Carbon::parse($request->input('date'));
        $reservationTime = Carbon::now();

        foreach ($tables as $tableId) {
            $table = Table::find($tableId);

            if ($table && $table->status == 'available') {
                // Update table status
                $table->status = 'reserved';
                $table->save();

                // Create reservation
                Reservation::create([
                    'table_id' => $table->id,
                    'user_id' => Auth::id(),
                    'name' => Auth::user()->name,
                    'reservation_date' => $reservationDate,
                    'reservation_time' => $reservationTime,
                    'status' => 'reserved',
                ]);
            }
        }

        return redirect()->route('userreservations')->with('success', 'Tables reserved successfully!');
    }





    public function reservations()
    {
        $reservations = Reservation::with('table')->where('user_id', Auth::id())->get();
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
            $reservation->reservation_time = Carbon::now()->toTimeString();
        }

        $reservation->status = $status;
        $reservation->save();

        return redirect()->route('reservations')->with('success', 'Table status updated successfully.');
    }
}
