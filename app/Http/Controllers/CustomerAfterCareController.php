<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CustomerAfterCareController extends Controller
{
    public function index()
    {
        $type   = request('type', 'all'); // all | Aftercare h+1 | Aftercare h+7 | Aftercare h+1bulan
        $status = request('status', 'pending');
        $search = request('search');
        $searchBy = request('search_by', 'customer_name'); // customer_name | invoice_number
        $referenceDate = request('date') ? Carbon::parse(request('date')) : Carbon::now();

        $query = Sale::query();

        if ($type === 'all') {
            $d1  = $referenceDate->copy()->subDays(1)->toDateString();
            $d7  = $referenceDate->copy()->subDays(7)->toDateString();
            $d30 = $referenceDate->copy()->subDays(30)->toDateString();

            $query->where(function ($q) use ($d1, $d7, $d30, $status) {
                $q->where(function ($q2) use ($d1, $status) {
                    $q2->whereDate('invoice_date', $d1)
                       ->where('followup_h1_status', $status);
                })->orWhere(function ($q2) use ($d7, $status) {
                    $q2->whereDate('invoice_date', $d7)
                       ->where('followup_h7_status', $status);
                })->orWhere(function ($q2) use ($d30, $status) {
                    $q2->whereDate('invoice_date', $d30)
                       ->where('followup_1month_status', $status);
                });
            });
        } else {
            $daysToSubtract = match($type) {
                'Aftercare h+1'     => 1,
                'Aftercare h+7'     => 7,
                'Aftercare h+1bulan'=> 30,
                default             => 1,
            };
            $statusColumn = match($type) {
                'Aftercare h+1'     => 'followup_h1_status',
                'Aftercare h+7'     => 'followup_h7_status',
                'Aftercare h+1bulan'=> 'followup_1month_status',
                default             => 'followup_h1_status',
            };

            $invoiceDate = $referenceDate->copy()->subDays($daysToSubtract)->toDateString();
            $query->whereDate('invoice_date', $invoiceDate)
                  ->where($statusColumn, $status);
        }

        // Search filter
        if ($search) {
            $column = in_array($searchBy, ['customer_name', 'invoice_number']) ? $searchBy : 'customer_name';
            $query->where($column, 'like', '%' . $search . '%');
        }

        $records = $query->with('items')->orderBy('invoice_date', 'asc')->paginate(20)->appends(request()->query());

        $types    = ['Aftercare h+1', 'Aftercare h+7', 'Aftercare h+1bulan'];
        $statuses = ['pending', 'completed', 'skipped'];

        return view('aftercare.index', compact('records', 'types', 'statuses', 'type', 'status', 'referenceDate', 'search', 'searchBy'));
    }

    public function markComplete(Sale $sale)
    {
        $type = request('type', 'Aftercare h+1');

        $statusColumn = match($type) {
            'Aftercare h+1'     => 'followup_h1_status',
            'Aftercare h+7'     => 'followup_h7_status',
            'Aftercare h+1bulan'=> 'followup_1month_status',
            default             => 'followup_h1_status',
        };

        $sale->update([$statusColumn => 'completed']);

        return back()->with('success', 'Aftercare berhasil ditandai selesai!');
    }

    public function markSkipped(Sale $sale)
    {
        $type = request('type', 'Aftercare h+1');

        $statusColumn = match($type) {
            'Aftercare h+1'     => 'followup_h1_status',
            'Aftercare h+7'     => 'followup_h7_status',
            'Aftercare h+1bulan'=> 'followup_1month_status',
            default             => 'followup_h1_status',
        };

        $sale->update([$statusColumn => 'skipped']);

        return back()->with('success', 'Aftercare ditandai skip!');
    }

    public function markPending(Sale $sale)
    {
        $type = request('type', 'Aftercare h+1');

        $statusColumn = match($type) {
            'Aftercare h+1'     => 'followup_h1_status',
            'Aftercare h+7'     => 'followup_h7_status',
            'Aftercare h+1bulan'=> 'followup_1month_status',
            default             => 'followup_h1_status',
        };

        $sale->update([$statusColumn => 'pending']);

        return back()->with('success', 'Aftercare dikembalikan ke Pending.');
    }
}
