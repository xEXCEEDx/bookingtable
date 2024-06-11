<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Table;
use App\Models\TableStatus;
use Carbon\Carbon;
use App\Models\Reservation;
use Illuminate\Support\Facades\Auth;


class TableController extends Controller
{
    public function index()
    {
        // Fetch all tables with their statuses
        $tables = Table::all();

        return view('tablestaff', compact('tables'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:available,reserved',
            'date' => 'required|date',
        ]);

        $status = $request->input('status');
        $date = $request->input('date');

        // Update the table status
        $tableStatus = TableStatus::updateOrCreate(
            ['table_id' => $id, 'date' => $date],
            ['status' => $status]
        );

        // If the status is 'reserved', create a reservation
        if ($status === 'reserved') {
            $reservation = Reservation::firstOrCreate(
                ['table_id' => $id, 'reservation_date' => $date],
                [
                    'status' => 'reserved',
                    'name' => 'Auto Reserved', // ปรับแต่งชื่อตามที่ต้องการ
                    'user_id' => Auth::id() // ระบุ user_id ของผู้ใช้ปัจจุบัน
                ]
            );
        } else {
            // If the status is 'available', delete any existing reservation for that date
            Reservation::where('table_id', $id)->where('reservation_date', $date)->delete();
        }

        return back()->with('success', 'Table status updated successfully.');
    }


    public function reserveTable(Request $request)
    {
        $request->validate([
            'reservation_date' => 'required|date|after_or_equal:tomorrow|before_or_equal:' . Carbon::tomorrow()->addDays(2)->format('Y-m-d'),
            'table_id' => 'required|exists:tables,id'
        ]);

        $tableId = $request->input('table_id');
        if (is_null($tableId)) {
            return redirect()->back()->with('error', 'Table ID is required.');
        }

        $date = Carbon::parse($request->input('reservation_date'));

        $tableStatus = TableStatus::updateOrCreate(
            ['table_id' => $tableId, 'date' => $date],
            ['status' => 'reserved']
        );

        return redirect()->back()->with('success', 'Table reserved successfully.');
    }

    public function setTotal(Request $request)
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
}
