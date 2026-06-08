<?php

namespace App\Http\Controllers;

use App\Imports\SalesImport;
use App\Models\Sale;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Carbon\Carbon;

class SalesController extends Controller
{
    /**
     * Dashboard dengan list followup yang perlu dilakukan
     */
    public function dashboard(Request $request)
    {
        $today = Carbon::now()->toDateString();
        $filter = $request->get('filter', 'all'); // all | h1 | h7 | 1month

        // Query followup hari ini (dengan pengelompokan kondisi yang benar)
        $followupToday = Sale::where(function ($query) use ($today) {
            $query->where(function ($q) use ($today) {
                $q->where('followup_h1_date', $today)
                  ->where('followup_h1_status', 'pending');
            })->orWhere(function ($q) use ($today) {
                $q->where('followup_h7_date', $today)
                  ->where('followup_h7_status', 'pending');
            })->orWhere(function ($q) use ($today) {
                $q->where('followup_1month_date', $today)
                  ->where('followup_1month_status', 'pending');
            });
        })->get();

        // Query semua pending followup dengan filter
        $pendingQuery = Sale::query();
        if ($filter === 'h1') {
            $pendingQuery->where('followup_h1_status', 'pending');
        } elseif ($filter === 'h7') {
            $pendingQuery->where('followup_h7_status', 'pending');
        } elseif ($filter === '1month') {
            $pendingQuery->where('followup_1month_status', 'pending');
        } else {
            $pendingQuery->where(function ($q) {
                $q->where('followup_h1_status', 'pending')
                  ->orWhere('followup_h7_status', 'pending')
                  ->orWhere('followup_1month_status', 'pending');
            });
        }
        $pendingFollowups = $pendingQuery->orderBy('invoice_date', 'asc')->paginate(20)->appends($request->query());

        // Statistik
        $stats = [
            'total_sales'   => Sale::count(),
            'pending_h1'    => Sale::where('followup_h1_status', 'pending')->count(),
            'pending_h7'    => Sale::where('followup_h7_status', 'pending')->count(),
            'pending_1month'=> Sale::where('followup_1month_status', 'pending')->count(),
            'today_followups' => $followupToday->count(),
        ];

        return view('sales.dashboard', [
            'followupToday'    => $followupToday,
            'pendingFollowups' => $pendingFollowups,
            'stats'            => $stats,
            'filter'           => $filter,
        ]);
    }

    /**
     * Detail customer dengan purchase history
     */
    public function customerDetail($phoneNumber)
    {
        // Decode URL encoded phone number
        $phoneNumber = urldecode($phoneNumber);

        // Cari semua transaksi customer berdasarkan phone number
        $purchases = Sale::where('phone_number', $phoneNumber)
            ->orderBy('invoice_date', 'desc')
            ->get();

        if ($purchases->isEmpty()) {
            return redirect()->route('sales.dashboard')->with('error', 'Customer tidak ditemukan');
        }

        $customer = [
            'name' => $purchases->first()->customer_name,
            'phone_number' => $phoneNumber,
            'total_purchases' => $purchases->count(),
            'first_purchase' => $purchases->last()->invoice_date,
            'last_purchase' => $purchases->first()->invoice_date,
        ];

        // Kelompokkan followup berdasarkan stage
        $followupSummary = [
            'h1' => [
                'pending' => $purchases->where('followup_h1_status', 'pending')->count(),
                'done' => $purchases->where('followup_h1_status', 'done')->count(),
                'skipped' => $purchases->where('followup_h1_status', 'skipped')->count(),
            ],
            'h7' => [
                'pending' => $purchases->where('followup_h7_status', 'pending')->count(),
                'done' => $purchases->where('followup_h7_status', 'done')->count(),
                'skipped' => $purchases->where('followup_h7_status', 'skipped')->count(),
            ],
            '1month' => [
                'pending' => $purchases->where('followup_1month_status', 'pending')->count(),
                'done' => $purchases->where('followup_1month_status', 'done')->count(),
                'skipped' => $purchases->where('followup_1month_status', 'skipped')->count(),
            ],
        ];

        return view('sales.customer-detail', [
            'customer' => $customer,
            'purchases' => $purchases,
            'followupSummary' => $followupSummary,
        ]);
    }

    /**
     * Update status followup
     */
    public function updateFollowupStatus(Request $request, $invoice_number)
    {
        $request->validate([
            'followup_type' => 'required|in:h1,h7,1month',
            'status' => 'required|in:pending,done,skipped',
            'notes' => 'nullable|string|max:500',
        ]);

        $sale = Sale::where('invoice_number', $invoice_number)->firstOrFail();
        $followupType = $request->followup_type;
        $status = $request->status;
        $notes = $request->notes ?? null;

        // Update status berdasarkan tipe followup
        if ($followupType === 'h1') {
            $sale->followup_h1_status = $status;
            $sale->followup_h1_notes = $notes;
        } elseif ($followupType === 'h7') {
            $sale->followup_h7_status = $status;
            $sale->followup_h7_notes = $notes;
        } elseif ($followupType === '1month') {
            $sale->followup_1month_status = $status;
            $sale->followup_1month_notes = $notes;
        }

        $sale->last_followup_at = Carbon::now();
        $sale->save();

        return redirect()->back()->with('success', "Followup status berhasil diupdate");
    }

    public function index(Request $request)
    {
        $query = Sale::with('items'); // Eager load items

        // Filter by search
        if ($request->search) {
            $query->where('customer_name', 'like', "%{$request->search}%")
                ->orWhere('phone_number', 'like', "%{$request->search}%")
                ->orWhere('invoice_number', 'like', "%{$request->search}%");
        }

        // Filter by followup status
        if ($request->followup_status && $request->followup_status !== 'all') {
            $followupStatus = $request->followup_status;
            if ($followupStatus === 'pending') {
                $query->where(function ($q) {
                    $q->where('followup_h1_status', 'pending')
                        ->orWhere('followup_h7_status', 'pending')
                        ->orWhere('followup_1month_status', 'pending');
                });
            } elseif ($followupStatus === 'done') {
                $query->where(function ($q) {
                    $q->where('followup_h1_status', 'done')
                        ->orWhere('followup_h7_status', 'done')
                        ->orWhere('followup_1month_status', 'done');
                });
            }
        }

        $sales = $query->orderBy('invoice_date', 'desc')->paginate(20);

        return view('sales.index', [
            'sales' => $sales,
        ]);
    }

    public function import()
    {
        return view('sales.import');
    }

    public function importStore(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        try {
            Excel::import(new SalesImport, $request->file('file'));

            return redirect()
                ->route('sales.index')
                ->with('success', 'Data penjualan berhasil diimport ke database!');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Error import data: ' . $e->getMessage());
        }
    }

    public function show(Sale $sale)
    {
        return view('sales.show', compact('sale'));
    }
}
