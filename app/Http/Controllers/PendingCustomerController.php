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

        // Filter by date range
        if ($request->date_from) {
            $query->whereDate('entry_date', '>=', $request->date_from);
        }
        if ($request->date_to) {
            $query->whereDate('entry_date', '<=', $request->date_to);
        }

        $sort      = 'desc';
        $customers = $query->orderBy('entry_date', 'desc')
            ->paginate(20)
            ->appends($request->query());

        $statuses = \App\Models\PendingCustomerStatus::all();

        return view('pending-customers.index', compact('customers', 'statuses', 'search', 'searchBy', 'sort'));
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

    public function sendWhatsapp(Request $request, PendingCustomer $pendingCustomer)
    {
        $type = $request->input('type', 'h+1'); // h+1 | h+7 | h+1month
        $typeField = str_replace('+', '', $type); // h1 | h7 | h1month
        
        $templateKey = match($type) {
            'h+1'      => 'h1',
            'h+7'      => 'h7',
            'h+1month' => '1month',
            default    => 'h1',
        };

        $settings = \App\Models\QontakSetting::getSettings();
        $templateField = match($type) {
            'h+1'      => 'pending_template_h1',
            'h+7'      => 'pending_template_h7',
            'h+1month' => 'pending_template_1month',
            default    => 'pending_template_h1',
        };
        $templateId = $settings->$templateField;

        $varsCountField = $templateField . '_vars';
        $varsCount = $settings->$varsCountField ?? 2;

        if (empty($templateId)) {
            return back()->with('error', "UUID Template Qontak untuk calon pelanggan {$type} belum diatur di Pengaturan Qontak!");
        }

        $bodyParams = [];
        $mappings = $settings->variable_mappings[$templateField] ?? [];

        for ($i = 1; $i <= $varsCount; $i++) {
            $source = $mappings[$i]['source'] ?? ($i == 1 ? 'customer_name' : 'notes');
            $customVal = $mappings[$i]['custom_value'] ?? '';

            $valText = match ($source) {
                'customer_name' => $pendingCustomer->name,
                'notes'         => $pendingCustomer->notes ?: '-',
                'item_name'     => '-',
                'invoice_date'  => $pendingCustomer->entry_date ? $pendingCustomer->entry_date->format('d-m-Y') : '-',
                'status'        => $pendingCustomer->status->name ?? '-',
                'custom'        => $customVal,
                default         => '',
            };

            $bodyParams[] = [
                'key' => (string) $i,
                'value' => 'var_' . $i,
                'value_text' => $valText,
            ];
        }

        $qontakService = new \App\Services\QontakService();
        $result = $qontakService->sendWhatsappDirect(
            $pendingCustomer->phone_number,
            $pendingCustomer->name,
            $templateId,
            $bodyParams
        );

        if ($result['success']) {
            $dateField = "followup_{$typeField}_last_date";
            
            $pendingCustomer->update([
                $dateField => now()->toDateString(),
            ]);

            return back()->with('success', "Pesan WhatsApp berhasil dikirim ke {$pendingCustomer->name}!");
        }

        return back()->with('error', "Gagal mengirim WhatsApp: " . ($result['error'] ?? 'Terjadi kesalahan pada Qontak API.'));
    }

    /**
     * Broadcast WA templates to all pending prospective customer records based on active date and type filters
     */
    public function broadcastAll(Request $request)
    {
        // Bypass PHP execution time limits to prevent timeout on long synchronous loops
        set_time_limit(0);
        ini_set('max_execution_time', 0);

        $type = $request->input('type');
        $referenceDate = $request->input('date') ? \Carbon\Carbon::parse($request->input('date')) : now();
        $referenceDateStr = $referenceDate->toDateString();

        $query = PendingCustomer::with('status');

        if ($type) {
            $typeField = str_replace('+', '', $type); // h1 | h7 | h1month
            $query->whereDate("followup_{$typeField}_date", $referenceDateStr)
                  ->whereNull("followup_{$typeField}_last_date");
        } else {
            $query->where(function ($q) use ($referenceDateStr) {
                $q->where(function($q2) use ($referenceDateStr) {
                    $q2->whereDate('followup_h1_date', $referenceDateStr)
                       ->whereNull('followup_h1_last_date');
                })->orWhere(function($q2) use ($referenceDateStr) {
                    $q2->whereDate('followup_h7_date', $referenceDateStr)
                       ->whereNull('followup_h7_last_date');
                })->orWhere(function($q2) use ($referenceDateStr) {
                    $q2->whereDate('followup_h1month_date', $referenceDateStr)
                       ->whereNull('followup_h1month_last_date');
                });
            });
        }

        if ($request->status_id) {
            $query->where('status_id', $request->status_id);
        }

        $records = $query->get();

        if ($records->isEmpty()) {
            return back()->with('error', 'Tidak ada data calon customer pending yang perlu di-follow-up untuk filter ini.');
        }

        $settings = \App\Models\QontakSetting::getSettings();
        $qontakService = new \App\Services\QontakService();
        $successCount = 0;
        $failCount = 0;
        $failDetails = [];

        foreach ($records as $customer) {
            // Find which due types are active and pending
            $dueTypes = [];
            if ($customer->followup_h1_date && $customer->followup_h1_date->toDateString() === $referenceDateStr && !$customer->followup_h1_last_date) {
                $dueTypes[] = 'h+1';
            }
            if ($customer->followup_h7_date && $customer->followup_h7_date->toDateString() === $referenceDateStr && !$customer->followup_h7_last_date) {
                $dueTypes[] = 'h+7';
            }
            if ($customer->followup_h1month_date && $customer->followup_h1month_date->toDateString() === $referenceDateStr && !$customer->followup_h1month_last_date) {
                $dueTypes[] = 'h+1month';
            }

            // We must filter active dueTypes if request type filter is active
            if ($type) {
                $dueTypes = array_filter($dueTypes, fn($dt) => $dt === $type);
            }

            foreach ($dueTypes as $dt) {
                $templateField = match($dt) {
                    'h+1'      => 'pending_template_h1',
                    'h+7'      => 'pending_template_h7',
                    'h+1month' => 'pending_template_1month',
                };
                $templateId = $settings->$templateField;

                if (empty($templateId)) {
                    $failCount++;
                    $failDetails[] = "{$customer->name} ({$dt}): Template ID belum diatur.";
                    continue;
                }

                $varsCountField = $templateField . '_vars';
                $varsCount = $settings->$varsCountField ?? 2;

                $bodyParams = [];
                $mappings = $settings->variable_mappings[$templateField] ?? [];

                for ($i = 1; $i <= $varsCount; $i++) {
                    $source = $mappings[$i]['source'] ?? ($i == 1 ? 'customer_name' : 'notes');
                    $customVal = $mappings[$i]['custom_value'] ?? '';

                    $valText = match ($source) {
                        'customer_name' => $customer->name,
                        'notes'         => $customer->notes ?: '-',
                        'item_name'     => '-',
                        'invoice_date'  => $customer->entry_date ? $customer->entry_date->format('d-m-Y') : '-',
                        'status'        => $customer->status->name ?? '-',
                        'custom'        => $customVal,
                        default         => '',
                    };

                    $bodyParams[] = [
                        'key' => (string) $i,
                        'value' => 'var_' . $i,
                        'value_text' => $valText,
                    ];
                }

                $result = $qontakService->sendWhatsappDirect(
                    $customer->phone_number,
                    $customer->name,
                    $templateId,
                    $bodyParams
                );

                if ($result['success']) {
                    $typeField = str_replace('+', '', $dt);
                    $customer->update([
                        "followup_{$typeField}_last_date" => now()->toDateString(),
                    ]);
                    $successCount++;
                } else {
                    $failCount++;
                    $failDetails[] = "{$customer->name} ({$dt}): " . ($result['error'] ?? 'Gagal API');
                }

                // Sleep for 1 second between requests to avoid connection timeouts or rate throttling
                sleep(1);
            }
        }

        $message = "Broadcast massal selesai. Sukses: {$successCount}, Gagal: {$failCount}.";
        if (!empty($failDetails)) {
            $message .= " Detail kegagalan: " . implode(' | ', $failDetails);
            return back()->with('error', $message);
        }

        return back()->with('success', $message);
    }
}
