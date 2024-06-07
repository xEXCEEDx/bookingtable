<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reservation;
use App\Models\Table;
use Illuminate\Support\Facades\Auth;
use App\Models\TableStatus;

class ReservationController extends Controller
{
    public function index()
    {
        $reservation = Reservation::with('table', 'user')->where('user_id', Auth::id())->get();

        $reservations = $reservations->groupBy(function ($reservation) {
            return Carbon::parse($reservations->reservation_date)->format('Y-m-d');
        });

        return view('reservations', compact('groupedReservations'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'table_id' => 'required|exists:tables,id',
        ]);

        $tableId = $request->input('table_id');
        $customerName = $request->input('customer_name');

        // Example logic to update table status to reserved
        $table = Table::find($tableId);

        if (!$table) {
            return back()->withErrors('Table not found.');
        }

        // Update table status to reserved
        $todayStatus = TableStatus::updateOrCreate(
            ['table_id' => $tableId, 'date' => now()->format('Y-m-d')],
            ['status' => 'reserved']
        );

        // Create or update reservation entry
        $reservation = Reservation::updateOrCreate(
            ['table_id' => $tableId, 'reservation_date' => now()->format('Y-m-d'), 'user_id' => Auth::id()],
            ['name' => $customerName, 'status' => 'reserved', 'staff_name' => Auth::user()->name] // Adjust staff_name as needed
        );

        return redirect()->route('reservations')->with('success', 'Table reserved successfully.');
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

        // Get the table status
        $tableStatus = TableStatus::where('table_id', $table->id)
                                   ->where('date', $reservation->reservation_date)
                                   ->first();

        if ($tableStatus && $tableStatus->status == 'available') {
            // Delete the reservation
            $reservation->delete();

            // Optionally, delete the table status entry if no longer needed
            $tableStatus->delete();

            return redirect()->route('reservations')->with('success', 'Reservation with available table status deleted successfully.');
        } else {
            // Get current user
            $user = Auth::user();

            // Update reservation status to 'cancelled'
            $reservation->status = 'cancelled';
            $reservation->staff_name = $user->name;
            $reservation->save();

            // Update table status to 'available'
            $tableStatus->status = 'available';
            $tableStatus->save();

            return redirect()->route('reservations')->with('success', 'Reservation cancelled successfully.');
        }
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
                $table->staff_name = null; // Reset staff_name for the table
                $table->save();
            }

            // Delete reservation
            $reservation->delete();
        }

        // Redirect back to reservations index
        return redirect()->route('reservations')->with('success', 'All reservations cleared successfully.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:available,reserved',
            'date' => 'required|date',
        ]);

        $status = $request->status;
        $date = $request->date;

        // Find the reservation
        $reservation = Reservation::findOrFail($id);

        // Update reservation status
        $reservation->status = $status;
        $reservation->save();

        // Update table status
        $table = $reservation->table;
        if ($table) {
            $table->status = $status;
            $table->save();
        }

        return redirect()->route('reservations')->with('success', 'Reservation status updated successfully.');
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

        // Group reservations by reservation_date
        $groupedReservations = $reservations->groupBy('reservation_date');

        return view('userreservations', compact('groupedReservations'));
    }
}
