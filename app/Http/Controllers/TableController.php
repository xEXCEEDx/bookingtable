<?php


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

        // ตรวจสอบว่าโต๊ะถูกจองไว้หรือไม่
        if ($request->status == 'reserved') {
            $user = Auth::user();

            // กำหนดจำนวนการจองสูงสุดต่อวัน
            $maxReservationsPerDay = 1;

            // นับจำนวนการจองในวันนี้
            $reservationsToday = Reservation::where('user_id', $user->id)
                ->whereDate('reservation_date', Carbon::today())
                ->count();

            // ตรวจสอบว่ามีการจองเกินจำนวนที่กำหนดหรือไม่
            if ($reservationsToday >= $maxReservationsPerDay) {
                return back()->withErrors('You have reached the maximum reservations per day.');
            }
        }

        // อัปเดตสถานะของโต๊ะ
        $table->status = $request->status;
        $table->save();

        // อัปเดตการจองที่เกี่ยวข้องกับโต๊ะนี้
        if ($request->status == 'available') {
            // ลบการจองทั้งหมดสำหรับโต๊ะนี้
            Reservation::where('table_id', $tableId)->delete();
        } elseif ($request->status == 'reserved') {
            // สร้างการจองใหม่
            $reservation = new Reservation();
            $reservation->table_id = $tableId;
            $reservation->user_id = Auth::id(); // ตั้งค่า user_id จากผู้ใช้ที่เข้าสู่ระบบ
            $reservation->name = Auth::user()->name; // ตั้งชื่อเป็นชื่อผู้ใช้ที่เข้าสู่ระบบ
            $reservation->reservation_time = Carbon::now(); // ใช้ Carbon เพื่อเวลาปัจจุบัน
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
    public function reserveTable(Request $request)
    {
        $request->validate([
            'reservation_date' => 'required|date|after_or_equal:tomorrow|before_or_equal:'.Carbon::tomorrow()->addDays(2)->format('Y-m-d'),
        ]);

        // ตรวจสอบและจองโต๊ะตามวันที่ที่เลือก
        $tableId = $request->input('table_id');

        $table = Table::find($tableId);

        if (!$table) {
            return redirect()->back()->with('error', 'ไม่พบโต๊ะที่ต้องการจอง');
        }

        // อัปเดตสถานะของโต๊ะเป็น 'reserved' หรือ 'available' ตามสถานะปัจจุบัน
        if ($table->status == 'available') {
            $table->status = 'reserved';
        } elseif ($table->status == 'reserved') {
            $table->status = 'available';
        }

        $table->save();

        return redirect()->back()->with('success', 'อัปเดตสถานะการจองโต๊ะเรียบร้อยแล้ว');
    }
}
