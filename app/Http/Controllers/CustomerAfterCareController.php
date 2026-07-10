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

    public function sendWhatsapp(Sale $sale)
    {
        $type = request('type', 'Aftercare h+1');

        $statusColumn = match($type) {
            'Aftercare h+1'     => 'followup_h1_status',
            'Aftercare h+7'     => 'followup_h7_status',
            'Aftercare h+1bulan'=> 'followup_1month_status',
            default             => 'followup_h1_status',
        };
        
        $templateKey = match($type) {
            'Aftercare h+1'     => 'h1',
            'Aftercare h+7'     => 'h7',
            'Aftercare h+1bulan'=> '1month',
            default             => 'h1',
        };

        $notesColumn = match($type) {
            'Aftercare h+1'     => 'followup_h1_notes',
            'Aftercare h+7'     => 'followup_h7_notes',
            'Aftercare h+1bulan'=> 'followup_1month_notes',
            default             => 'followup_h1_notes',
        };

        $settings = \App\Models\QontakSetting::getSettings();
        $templateField = match($type) {
            'Aftercare h+1'     => 'sales_template_h1',
            'Aftercare h+7'     => 'sales_template_h7',
            'Aftercare h+1bulan'=> 'sales_template_1month',
            default             => 'sales_template_h1',
        };
        $templateId = $settings->$templateField;

        $varsCountField = $templateField . '_vars';
        $varsCount = $settings->$varsCountField ?? 3;

        if (empty($templateId)) {
            return back()->with('error', 'UUID Template WhatsApp belum diatur di menu pengaturan Qontak.');
        }

        $itemsList = $sale->items->pluck('item_name')->implode(', ');
        $invoiceDateStr = $sale->invoice_date ? $sale->invoice_date->format('d-m-Y') : '';

        $bodyParams = [];
        $mappings = $settings->variable_mappings[$templateField] ?? [];

        for ($i = 1; $i <= $varsCount; $i++) {
            $source = $mappings[$i]['source'] ?? ($i == 1 ? 'customer_name' : ($i == 2 ? 'item_name' : 'invoice_date'));
            $customVal = $mappings[$i]['custom_value'] ?? '';

            $valText = match ($source) {
                'customer_name' => $sale->customer_name,
                'item_name'     => $itemsList ?: '-',
                'invoice_date'  => $invoiceDateStr,
                'notes'         => $sale->notes ?: '-',
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
            $sale->phone_number,
            $sale->customer_name,
            $templateId,
            $bodyParams
        );

        if ($result['success']) {
            $currentNotes = $sale->$notesColumn;
            $sentVars = collect($bodyParams)->pluck('value_text')->implode(', ');
            $logEntry = '[WA Sent: ' . now()->format('Y-m-d H:i') . '] (params: ' . $sentVars . ')';
            $newNotes = $currentNotes ? $currentNotes . "\n" . $logEntry : $logEntry;

            $sale->update([
                $statusColumn => 'completed',
                $notesColumn => $newNotes,
                'last_followup_at' => now(),
            ]);

            return back()->with('success', "Pesan WhatsApp berhasil dikirim ke {$sale->customer_name}!");
        }

        return back()->with('error', "Gagal mengirim WhatsApp: " . ($result['error'] ?? 'Terjadi kesalahan pada Qontak API.'));
    }

    /**
     * Broadcast WA templates to all pending aftercare records based on active date and type filters
     */
    public function broadcastAll(Request $request)
    {
        $type = $request->input('type', 'all');
        $referenceDate = $request->input('date') ? Carbon::parse($request->input('date')) : Carbon::now();

        $query = Sale::query();

        if ($type === 'all') {
            $d1  = $referenceDate->copy()->subDays(1)->toDateString();
            $d7  = $referenceDate->copy()->subDays(7)->toDateString();
            $d30 = $referenceDate->copy()->subDays(30)->toDateString();

            $query->where(function ($q) use ($d1, $d7, $d30) {
                $q->where(function ($q2) use ($d1) {
                    $q2->whereDate('invoice_date', $d1)
                       ->where('followup_h1_status', 'pending');
                })->orWhere(function ($q2) use ($d7) {
                    $q2->whereDate('invoice_date', $d7)
                       ->where('followup_h7_status', 'pending');
                })->orWhere(function ($q2) use ($d30) {
                    $q2->whereDate('invoice_date', $d30)
                       ->where('followup_1month_status', 'pending');
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
                  ->where($statusColumn, 'pending');
        }

        $records = $query->with('items')->get();
        
        if ($records->isEmpty()) {
            return back()->with('error', 'Tidak ada data aftercare berstatus pending yang perlu dikirim untuk filter ini.');
        }

        $settings = \App\Models\QontakSetting::getSettings();
        $qontakService = new \App\Services\QontakService();
        $successCount = 0;
        $failCount = 0;
        $failDetails = [];

        foreach ($records as $sale) {
            // Determine which type matches this sale
            $saleType = '';
            $d1 = $referenceDate->copy()->subDays(1)->toDateString();
            $d7 = $referenceDate->copy()->subDays(7)->toDateString();
            $d30 = $referenceDate->copy()->subDays(30)->toDateString();
            $invoiceDateStr = $sale->invoice_date->toDateString();

            if ($invoiceDateStr === $d1) {
                $saleType = 'Aftercare h+1';
            } elseif ($invoiceDateStr === $d7) {
                $saleType = 'Aftercare h+7';
            } elseif ($invoiceDateStr === $d30) {
                $saleType = 'Aftercare h+1bulan';
            } else {
                continue;
            }

            // We must skip if the status is not pending for that specific type
            $statusField = match($saleType) {
                'Aftercare h+1'     => 'followup_h1_status',
                'Aftercare h+7'     => 'followup_h7_status',
                'Aftercare h+1bulan'=> 'followup_1month_status',
            };

            if ($sale->$statusField !== 'pending') {
                continue;
            }

            $templateField = match($saleType) {
                'Aftercare h+1'     => 'sales_template_h1',
                'Aftercare h+7'     => 'sales_template_h7',
                'Aftercare h+1bulan'=> 'sales_template_1month',
            };
            $templateId = $settings->$templateField;

            if (empty($templateId)) {
                $failCount++;
                $failDetails[] = "{$sale->customer_name} ({$saleType}): Template ID belum diatur.";
                continue;
            }

            $varsCountField = $templateField . '_vars';
            $varsCount = $settings->$varsCountField ?? 3;

            $itemsList = $sale->items->pluck('item_name')->implode(', ');
            $invoiceDateFormatted = $sale->invoice_date ? $sale->invoice_date->format('d-m-Y') : '';

            $bodyParams = [];
            $mappings = $settings->variable_mappings[$templateField] ?? [];

            for ($i = 1; $i <= $varsCount; $i++) {
                $source = $mappings[$i]['source'] ?? ($i == 1 ? 'customer_name' : ($i == 2 ? 'item_name' : 'invoice_date'));
                $customVal = $mappings[$i]['custom_value'] ?? '';

                $valText = match ($source) {
                    'customer_name' => $sale->customer_name,
                    'item_name'     => $itemsList ?: '-',
                    'invoice_date'  => $invoiceDateFormatted,
                    'notes'         => $sale->notes ?: '-',
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
                $sale->phone_number,
                $sale->customer_name,
                $templateId,
                $bodyParams
            );

            if ($result['success']) {
                $notesColumn = match($saleType) {
                    'Aftercare h+1'     => 'followup_h1_notes',
                    'Aftercare h+7'     => 'followup_h7_notes',
                    'Aftercare h+1bulan'=> 'followup_1month_notes',
                };

                $currentNotes = $sale->$notesColumn;
                $logEntry = '[WA Sent: ' . now()->format('Y-m-d H:i') . '] (params: ' . $sale->customer_name . ', ' . ($itemsList ?: '-') . ', ' . $invoiceDateFormatted . ')';
                $newNotes = $currentNotes ? $currentNotes . "\n" . $logEntry : $logEntry;

                $sale->update([
                    $statusField => 'completed',
                    $notesColumn => $newNotes,
                    'last_followup_at' => now(),
                ]);
                
                $successCount++;
            } else {
                $failCount++;
                $failDetails[] = "{$sale->customer_name}: " . ($result['error'] ?? 'Gagal API');
            }

            // Sleep for 1 second between requests to avoid connection timeouts or rate throttling
            sleep(1);
        }

        $message = "Broadcast massal selesai. Sukses: {$successCount}, Gagal: {$failCount}.";
        if (!empty($failDetails)) {
            $message .= " Detail kegagalan: " . implode(' | ', $failDetails);
            return back()->with('error', $message);
        }

        return back()->with('success', $message);
    }
}
