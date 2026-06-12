<?php

namespace App\Http\Controllers;

use App\Models\PendingCustomer;
use Illuminate\Http\Request;

class PendingCustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = PendingCustomer::with('status');

        $search   = trim($request->search ?? '');
        $searchBy = in_array($request->search_by, ['name', 'phone_number']) ? $request->search_by : 'name';

        if ($search !== '') {
            $tokens = array_filter(explode(' ', $search));
            $query->where(function ($q) use ($tokens, $searchBy) {
                foreach ($tokens as $token) {
                    $q->where($searchBy, 'like', "%{$token}%");
                }
            });
        }

        if ($request->status_id) {
            $query->where('status_id', $request->status_id);
        }

        $customers = $query->orderBy('entry_date', 'desc')
            ->paginate(20)
            ->appends($request->query());

        $statuses = \App\Models\PendingCustomerStatus::all();

        return view('pending-customers.index', compact('customers', 'statuses', 'search', 'searchBy'));
    }

    public function followup(Request $request)
    {
        $referenceDate    = $request->date ? \Carbon\Carbon::parse($request->date) : now();
        $referenceDateStr = $referenceDate->toDateString();
        $type             = $request->type; // null = semua yang jatuh tempo hari itu

        $query = PendingCustomer::with('status');

        if ($type) {
            match($type) {
                'h+1'     => $query->whereDate('followup_h1_date', $referenceDateStr),
                'h+7'     => $query->whereDate('followup_h7_date', $referenceDateStr),
                'h+1month'=> $query->whereDate('followup_h1month_date', $referenceDateStr),
                default   => null,
            };
        } else {
            $query->where(function ($q) use ($referenceDateStr) {
                $q->whereDate('followup_h1_date', $referenceDateStr)
                  ->orWhereDate('followup_h7_date', $referenceDateStr)
                  ->orWhereDate('followup_h1month_date', $referenceDateStr);
            });
        }

        if ($request->status_id) {
            $query->where('status_id', $request->status_id);
        }

        $customers = $query->orderBy('entry_date', 'asc')
            ->paginate(20)
            ->appends($request->query());

        $statuses = \App\Models\PendingCustomerStatus::all();

        return view('pending-customers.followup', compact('customers', 'statuses', 'referenceDate', 'type'));
    }

    public function create()
    {
        $statuses = \App\Models\PendingCustomerStatus::all();
        return view('pending-customers.create', compact('statuses'));
    }

    public function store(Request $request)
    {
        // Determine validation rules based on whether creating new status
        if ($request->boolean('create_new_status')) {
            $request->validate([
                'name' => 'required|string|max:255',
                'phone_number' => 'required|string|max:20',
                'entry_date' => 'required|date',
                'new_status_name' => 'required|string|max:255',
                'notes' => 'nullable|string',
            ]);

            // Create new status
            $status = \App\Models\PendingCustomerStatus::create([
                'name' => $request->new_status_name,
            ]);

            $statusId = $status->id;
        } else {
            $request->validate([
                'name' => 'required|string|max:255',
                'phone_number' => 'required|string|max:20',
                'entry_date' => 'required|date',
                'status_id' => 'required|exists:pending_customer_statuses,id',
                'notes' => 'nullable|string',
            ]);

            $statusId = $request->status_id;
        }

        $entryDate = \Carbon\Carbon::parse($request->entry_date);

        PendingCustomer::create([
            'name' => $request->name,
            'phone_number' => $request->phone_number,
            'entry_date' => $request->entry_date,
            'status_id' => $statusId,
            'notes' => $request->notes,
            'followup_h1_date' => $entryDate->clone()->addDay(),
            'followup_h1_status' => 'pending',
            'followup_h7_date' => $entryDate->clone()->addDays(7),
            'followup_h7_status' => 'pending',
            'followup_h1month_date' => $entryDate->clone()->addMonth(1),
            'followup_h1month_status' => 'pending',
        ]);

        return redirect()
            ->route('pending-customers.index')
            ->with('success', 'Data calon customer berhasil ditambahkan!');
    }

    public function edit(PendingCustomer $pendingCustomer)
    {
        $statuses = \App\Models\PendingCustomerStatus::all();
        return view('pending-customers.edit', compact('pendingCustomer', 'statuses'));
    }

    public function update(Request $request, PendingCustomer $pendingCustomer)
    {
        // Determine validation rules based on whether creating new status
        if ($request->boolean('create_new_status')) {
            $request->validate([
                'name' => 'required|string|max:255',
                'phone_number' => 'required|string|max:20',
                'entry_date' => 'required|date',
                'new_status_name' => 'required|string|max:255',
                'notes' => 'nullable|string',
            ]);

            // Create new status
            $status = \App\Models\PendingCustomerStatus::create([
                'name' => $request->new_status_name,
            ]);

            $statusId = $status->id;
        } else {
            $request->validate([
                'name' => 'required|string|max:255',
                'phone_number' => 'required|string|max:20',
                'entry_date' => 'required|date',
                'status_id' => 'required|exists:pending_customer_statuses,id',
                'notes' => 'nullable|string',
            ]);

            $statusId = $request->status_id;
        }

        $entryDate = \Carbon\Carbon::parse($request->entry_date);

        $pendingCustomer->update([
            'name' => $request->name,
            'phone_number' => $request->phone_number,
            'entry_date' => $request->entry_date,
            'status_id' => $statusId,
            'notes' => $request->notes,
            'followup_h1_date' => $entryDate->clone()->addDay(),
            'followup_h7_date' => $entryDate->clone()->addDays(7),
            'followup_h1month_date' => $entryDate->clone()->addMonth(1),
        ]);

        return redirect()
            ->route('pending-customers.index')
            ->with('success', 'Data calon customer berhasil diupdate!');
    }

    public function destroy(PendingCustomer $pendingCustomer)
    {
        $pendingCustomer->delete();

        return redirect()
            ->route('pending-customers.index')
            ->with('success', 'Data calon customer berhasil dihapus!');
    }

    public function updateFollowupCheckpoint(Request $request, PendingCustomer $pendingCustomer)
    {
        $type = $request->input('type', 'h+1');
        $typeField = str_replace('+', '', $type); // Convert h+1 -> h1, h+7 -> h7, h+1month -> h1month
        
        $dateField = "followup_{$typeField}_last_date";
        
        $pendingCustomer->update([
            $dateField => now()->toDateString(),
        ]);

        return redirect()
            ->back()
            ->with('success', "Follow-up {$type} berhasil dicatat!");
    }

    public function destroyStatus(\App\Models\PendingCustomerStatus $status)
    {
        // Nullify status_id on related customers (handled by nullOnDelete FK constraint)
        $status->delete();

        return redirect()
            ->back()
            ->with('success', 'Status berhasil dihapus. Data calon customer tetap ada.');
    }
}
