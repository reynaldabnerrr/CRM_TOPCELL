<?php

namespace App\Imports;

use App\Models\Sale;
use App\Models\SaleItem;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;

class SalesImport implements ToArray, WithCalculatedFormulas
{
    public function array(array $array)
    {
        if (empty($array)) {
            return;
        }

        $saleCount = 0;
        $itemCount = 0;
        $i = 0;

        \Log::info('Starting import, total rows: ' . count($array));

        while ($i < count($array)) {
            $row = $array[$i] ?? [];
            
            // Skip row kosong
            if (empty(array_filter($row))) {
                $i++;
                continue;
            }

            // Cek apakah ini header row - harus ada "No. Faktur" di [1]
            if (isset($row[1]) && strtolower($row[1]) === 'no. faktur') {
                $i++; // Skip header
                
                // Next row adalah sale data
                if ($i < count($array)) {
                    $saleRow = $array[$i];
                    $invoiceNumber = $saleRow[1] ?? null;
                    
                    // Validasi invoice format
                    if (!empty($invoiceNumber) && is_string($invoiceNumber) && strpos($invoiceNumber, '/') !== false) {
                        \Log::info("Row {$i}: Processing invoice {$invoiceNumber}");
                        
                        $invoiceData = [
                            'no_faktur' => $invoiceNumber,
                            'tanggal' => $saleRow[3] ?? null,
                            'unit_usaha' => $saleRow[4] ?? null,
                            'departement' => $saleRow[5] ?? null,
                            'gudang' => $saleRow[6] ?? null,
                            'kontak' => $saleRow[7] ?? null,
                            'no_telfon_kontak' => $saleRow[8] ?? null,
                            'sales' => $saleRow[9] ?? null,
                            'pembayaran' => $saleRow[11] ?? null,
                            'status' => $saleRow[16] ?? 'Lunas',
                        ];
                        
                        $this->saveSale($invoiceData);
                        $saleCount++;
                        
                        $i++; // Move to next row
                        
                        // Skip blank row
                        if ($i < count($array) && empty(array_filter($array[$i]))) {
                            $i++;
                        }
                        
                        // Skip item header row (cek Kode Barang di [1])
                        if ($i < count($array) && isset($array[$i][1]) && strtolower($array[$i][1]) === 'kode barang') {
                            $i++;
                        }
                        
                        // Process item rows sampai SUBTOTAL
                        while ($i < count($array)) {
                            $itemRow = $array[$i];
                            
                            // Stop jika SUBTOTAL atau header baru
                            if (($itemRow[0] ?? null) === 'SUBTOTAL' || 
                                (isset($itemRow[1]) && strtolower($itemRow[1]) === 'no. faktur')) {
                                break;
                            }
                            
                            // Skip blank rows
                            if (empty(array_filter($itemRow))) {
                                $i++;
                                continue;
                            }
                            
                            // Process item: [null, kode, nama, sales, vendor, harga_beli, harga_jual, diskon, pajak, qty, sn, modal, satuan, jumlah_modal, jumlah_pendapatan]
                            $itemCode = $itemRow[1] ?? null;
                            $itemName = $itemRow[2] ?? null;
                            
                            if (!empty($itemCode) && !empty($itemName) && is_string($itemName)) {
                                \Log::info("Row {$i}: Processing item {$itemCode}");
                                
                                $itemData = [
                                    'invoice_number' => $invoiceNumber,
                                    'item_code' => $itemCode,
                                    'item_name' => $itemName,
                                    'vendor' => $itemRow[4] ?? null,
                                    'quantity' => (int) ($itemRow[9] ?? 0),
                                    'unit' => $itemRow[12] ?? null,
                                    'purchase_price' => $this->parsePrice($itemRow[5] ?? 0),
                                    'selling_price' => $this->parsePrice($itemRow[6] ?? 0),
                                    'discount' => $this->parseDiscount($itemRow[7] ?? 0),
                                    'tax' => $this->parsePrice($itemRow[8] ?? 0),
                                    'serial_number' => (string) ($itemRow[10] ?? null),
                                    'total_revenue' => $this->parsePrice($itemRow[14] ?? 0),
                                    'profit' => $this->parsePrice($itemRow[16] ?? 0),
                                ];
                                
                                $this->saveItem($itemData);
                                $itemCount++;
                            }
                            
                            $i++;
                        }
                        
                        continue;
                    }
                }
            }
            
            $i++;
        }
        
        \Log::info("Import finished: {$saleCount} sales, {$itemCount} items");
    }

    private function saveSale(array $invoiceData)
    {
        $invoiceNumber = $invoiceData['no_faktur'];
        if (empty($invoiceNumber)) {
            return;
        }

        // Parse date
        $tanggal = $invoiceData['tanggal'];
        try {
            if (is_numeric($tanggal)) {
                $invoiceDate = Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($tanggal));
            } else {
                $invoiceDate = Carbon::createFromFormat('d-M-y', $tanggal);
            }
        } catch (\Exception $e) {
            $invoiceDate = Carbon::now();
        }

        $followupH1Date = $invoiceDate->copy()->addDay(1);
        $followupH7Date = $invoiceDate->copy()->addDays(7);
        $followup1MonthDate = $invoiceDate->copy()->addDays(30);

        // updateOrCreate: update jika invoice sudah ada, create jika belum
        Sale::updateOrCreate(
            ['invoice_number' => $invoiceNumber], // Where condition
            [ // Update/Create values
                'invoice_date' => $invoiceDate,
                'unit_name' => $invoiceData['unit_usaha'],
                'department' => $invoiceData['departement'],
                'warehouse' => $invoiceData['gudang'],
                'customer_name' => $invoiceData['kontak'],
                'phone_number' => $this->formatPhone($invoiceData['no_telfon_kontak']),
                'sales_person' => $invoiceData['sales'],
                'payment_method' => $invoiceData['pembayaran'],
                'status' => $invoiceData['status'],
                'followup_h1_date' => $followupH1Date,
                'followup_h7_date' => $followupH7Date,
                'followup_1month_date' => $followup1MonthDate,
                'followup_h1_status' => 'pending',
                'followup_h7_status' => 'pending',
                'followup_1month_status' => 'pending',
            ]
        );
    }

    private function saveItem(array $itemData)
    {
        // updateOrCreate: update jika item sudah ada, create jika belum
        SaleItem::updateOrCreate(
            [ // Where condition
                'invoice_number' => $itemData['invoice_number'],
                'item_code' => $itemData['item_code'],
            ],
            [ // Update/Create values
                'item_name' => $itemData['item_name'],
                'vendor' => $itemData['vendor'],
                'quantity' => $itemData['quantity'],
                'unit' => $itemData['unit'],
                'purchase_price' => $itemData['purchase_price'],
                'selling_price' => $itemData['selling_price'],
                'discount' => $itemData['discount'],
                'tax' => $itemData['tax'],
                'serial_number' => $itemData['serial_number'],
                'total_revenue' => $itemData['total_revenue'],
                'profit' => $itemData['profit'],
            ]
        );
    }

    private function parsePrice($value)
    {
        if (empty($value) || $value === '-') {
            return 0;
        }

        if (is_numeric($value)) {
            return (float) $value;
        }

        if (is_string($value)) {
            $value = str_replace(['Rp.', 'Rp', '.', ',', ' '], '', $value);
        }

        return (float) ($value ?: 0);
    }

    private function parseDiscount($value)
    {
        if (empty($value) || $value === '-') {
            return 0;
        }

        if (is_numeric($value)) {
            return (float) $value;
        }

        if (is_string($value)) {
            $value = str_replace(['%', ' '], '', $value);
        }

        return (float) ($value ?: 0);
    }

    private function formatPhone($phone)
    {
        if (empty($phone)) {
            return null;
        }

        if (is_numeric($phone)) {
            return (string) $phone;
        }

        return $phone;
    }
}

