<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CustomerAfterCareController extends Controller
{
    public function index()
    {
        $type = request('type', 'Aftercare h+1');
        $status = request('status', 'pending');
        $referenceDate = request('date') ? Carbon::parse(request('date')) : Carbon::now();

        // Hitung invoice_date yang seharusnya punya aftercare pada reference_date ini
        $daysToSubtract = match($type) {
            'Aftercare h+1' => 1,
            'Followup h+7' => 7,
            'Followup h+1bulan' => 30,
            default => 0,
        };
        
        $referenceInvoiceDate = $referenceDate->copy()->subDays($daysToSubtract);

        $query = Sale::query();
        
        // Filter berdasarkan invoice_date (tanggal penjualan)
        $query->whereDate('invoice_date', $referenceInvoiceDate->toDateString());

        // Filter berdasarkan status followup di sale table
        if ($status) {
            $statusColumn = match($type) {
                'Aftercare h+1' => 'followup_h1_status',
                'Followup h+7' => 'followup_h7_status',
                'Followup h+1bulan' => 'followup_1month_status',
                default => 'followup_h1_status',
            };
            
            $query->where($statusColumn, $status);
        }

        $records = $query->with('items')->orderBy('invoice_date', 'asc')->paginate(20);

        $types = ['Aftercare h+1', 'Followup h+7', 'Followup h+1bulan'];
        $statuses = ['pending', 'completed', 'skipped'];

        return view('aftercare.index', compact('records', 'types', 'statuses', 'type', 'status', 'referenceDate'));
    }

    public function markComplete(Sale $sale)
    {
        $type = request('type', 'Aftercare h+1');
        
        $statusColumn = match($type) {
            'Aftercare h+1' => 'followup_h1_status',
            'Followup h+7' => 'followup_h7_status',
            'Followup h+1bulan' => 'followup_1month_status',
            default => 'followup_h1_status',
        };
        
        $sale->update([$statusColumn => 'completed']);

        return back()->with('success', 'Follow-up berhasil ditandai selesai!');
    }

    public function markSkipped(Sale $sale)
    {
        $type = request('type', 'Aftercare h+1');
        
        $statusColumn = match($type) {
            'Aftercare h+1' => 'followup_h1_status',
            'Followup h+7' => 'followup_h7_status',
            'Followup h+1bulan' => 'followup_1month_status',
            default => 'followup_h1_status',
        };
        
        $sale->update([$statusColumn => 'skipped']);

        return back()->with('success', 'Follow-up ditandai skip!');
    }
}
