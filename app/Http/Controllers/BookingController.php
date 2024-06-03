<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Table;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{

    public function index()
    {
        $tables = Table::all();
        return view('booking', compact('tables'));
    }

    public function confirm(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'tables' => 'required|array',
            'tables.*' => 'integer|exists:tables,table_number',
        ]);

        // Process the request
        $tables = $request->input('tables');

        foreach ($tables as $tableNumber) {
            $table = Table::where('table_number', $tableNumber)->first();

            // Check if table exists and is available
            if ($table && $table->status == 'available') {
                // Update table status to reserved
                $table->status = 'reserved';
                $table->save();

                // Create reservation entry
                Reservation::create([
                    'table_id' => $table->id,
                    'user_id' => Auth::id(), // Assuming you have authentication set up
                    'name' => Auth::user()->name, // Assuming you have a name field in your user model
                    'reservation_time' => Carbon::now(),
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
            ->whereDate('reservation_time', Carbon::today())
            ->first();

        if (!$reservation) {
            $reservation = new Reservation();
            $reservation->table_id = $table->id;
            $reservation->user_id = Auth::id();
            $reservation->name = Auth::user()->name;
            $reservation->reservation_time = Carbon::now();
        }

        $reservation->status = $status;
        $reservation->save();

        return redirect()->route('reservations')->with('success', 'Table status updated successfully.');
    }
}
