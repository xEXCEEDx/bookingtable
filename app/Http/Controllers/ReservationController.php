<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reservation;
use App\Models\Table;
use Illuminate\Support\Facades\Auth;
use App\Models\TableStatus;
use Carbon\Carbon;

class ReservationController extends Controller
{
    public function index()
    {
        $reservations = Reservation::with('table')->where('user_id', Auth::id())->get();

        $groupedReservations = $reservations->groupBy(function ($reservation) {
            return Carbon::parse($reservation->reservation_date)->format('Y-m-d');
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

        if (!$reservation || !$table) {
            return back()->withErrors('Reservation or table not found.');
        }


        $tableStatus = TableStatus::where('table_id', $table->id)
                                   ->where('date', $reservation->reservation_date)
                                   ->first();

        if ($tableStatus && $tableStatus->status == 'reserved') {

            $reservation->delete();


            $tableStatus->delete();

            return redirect()->route('reservations')->with('success', 'Reservation with available table status deleted successfully.');
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
                $tableStatus = TableStatus::where('table_id', $table->id)
                                          ->where('date', $reservation->reservation_date)
                                          ->first();
                if ($tableStatus) {
                    $tableStatus->status = 'available';
                    $tableStatus->save();
                }

                // Optionally, reset staff_name for the table
                $table->staff_name = null;
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
            'status' => 'required|in:available,reserved,cancelled',
        ]);

        // Find the reservation
        $reservation = Reservation::findOrFail($id);

        // Update reservation status
        $reservation->status = $request->status;
        $reservation->save();

        // Update table status if needed
        if ($request->status == 'cancelled') {
            $tableStatus = TableStatus::where('table_id', $reservation->table_id)
                                      ->where('date', $reservation->reservation_date)
                                      ->first();

            if ($tableStatus) {
                $tableStatus->status = 'available';
                $tableStatus->save();
            }
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
