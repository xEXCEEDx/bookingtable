<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reservation;
use App\Models\Table;
use Illuminate\Support\Facades\Auth;

class ReservationController extends Controller
{
    public function index()
    {
        $reservations = Reservation::with('table')->orderBy('created_at', 'desc')->get();
        return view('reservations', compact('reservations'));
    }

    public function updateTable(Request $request, $reservationId)
    {
        // Find the reservation
        $reservation = Reservation::findOrFail($reservationId);

        // Get associated table
        $table = $reservation->table;

        // Check if reservation and table exist
        if (!$reservation || !$table) {
            return back()->withErrors('Reservation or table not found.');
        }

        // Delete reservation
        $reservation->delete();

        // Update table status to 'available'
        if ($table) {
            $table->status = 'available';
            $table->save();
        }

        return redirect()->route('reservations')->with('success', 'Reservation deleted successfully.');
    }

    public function clearAllReservations()
    {
        // Get all reservations
        $reservations = Reservation::all();

        // Iterate over each reservation
        foreach ($reservations as $reservation) {
            // Get associated table
            $table = $reservation->table;

            // Update table status to 'available'
            if ($table) {
                $table->status = 'available';
                $table->save();
            }

            // Delete reservation
            $reservation->delete();
        }

        // Redirect back to reservations index
        return redirect()->route('reservations')->with('success', 'All reservations cleared successfully.');
    }

    public function update(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'table_number' => 'required|integer|exists:tables,table_number',
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

        // Get current user
        $user = Auth::user();

        // Find reservation by user_id and table_id with today's date
        $reservation = Reservation::where('user_id', $user->id)
            ->where('table_id', $table->id)
            ->whereDate('reservation_time', Carbon::today())
            ->first();

        if ($reservation) {
            // Update reservation status
            $reservation->status = $status;
            $reservation->save();
        }

        return redirect()->route('reservations')->with('success', 'Table status updated successfully.');
    }

    public function search(Request $request)
    {
        $query = $request->input('query');
        $reservations = Reservation::with('table')
            ->whereHas('table', function($q) use ($query) {
                $q->where('table_number', 'like', '%' . $query . '%');
            })
            ->orWhere('name', 'like', '%' . $query . '%')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('reservations', compact('reservations'));
    }
    public function userreservations()
    {
        $userId = Auth::id();

        // Fetch reservations for the logged-in user with related table data
        $reservations = Reservation::with('table')
                                    ->where('user_id', $userId)
                                    ->orderBy('created_at', 'desc')
                                    ->get();

        // Group reservations by reservation_time
        $groupedReservations = $reservations->groupBy('reservation_date');

        return view('userreservations', compact('groupedReservations'));
    }
}
