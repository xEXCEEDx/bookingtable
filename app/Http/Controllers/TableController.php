<?php
namespace App\Http\Controllers;



namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Table;
use App\Models\Reservation;
use Auth;
use Carbon\Carbon;

class TableController extends Controller
{
    public function index()
    {
        $tables = Table::all();
        return view('tablestaff', compact('tables'));
    }

    public function setTotalTables(Request $request)
    {
        $request->validate([
            'total_tables' => 'required|integer|min:0',
        ]);

        $newTotalTables = $request->total_tables;
        $currentTotalTables = Table::count();
        $difference = $newTotalTables - $currentTotalTables;

        if ($difference > 0) {
            for ($i = 0; $i < $difference; $i++) {
                Table::create(['status' => 'available']);
            }
        } elseif ($difference < 0) {
            Table::orderBy('id', 'desc')->take(abs($difference))->delete();
        }

        return redirect()->route('tables.index')->with('success', 'Total tables updated successfully.');
    }

    public function updateStatus(Request $request, $tableId)
{
    $request->validate([
        'status' => 'required|in:available,reserved',
    ]);

    $table = Table::findOrFail($tableId);
    $table->status = $request->status;
    $table->save();

    // Update reservations associated with this table
    if ($request->status == 'available') {
        // Delete all reservations for this table
        Reservation::where('table_id', $tableId)->delete();
    } elseif ($request->status == 'reserved') {
        // Get current user
        $user = Auth::user();

        // Create new reservation
        $reservation = new Reservation();
        $reservation->table_id = $tableId;
        $reservation->user_id = $user->id; // Set user_id from logged-in user
        $reservation->name = 'STAFF'; // Set name to 'STAFF'
        $reservation->reservation_time = Carbon::now(); // Use Carbon for current time
        $reservation->status = 'reserved';
        $reservation->save();
    }

    return redirect()->route('tables.index')->with('success', 'Table status updated successfully.');
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
}
