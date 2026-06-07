<?php

namespace App\Imports;

use App\Models\Sale;
use App\Models\CustomerAftercare;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;

class SalesImport implements ToModel, WithHeadingRow, WithBatchInserts
{
    public function model(array $row)
    {
        // Skip jika invoice number kosong
        if (empty($row['no_faktur'] ?? $row['invoice_number'] ?? null)) {
            return null;
        }

        // Mapping kolom dari Excel
        $invoiceNumber = $row['no_faktur'] ?? $row['invoice_number'] ?? null;
        $tanggal = $row['tanggal'] ?? $row['invoice_date'] ?? null;
        $customerName = $row['kontak'] ?? $row['customer_name'] ?? null;
        $phoneNumber = $row['no_telfon_kontak'] ?? $row['phone_number'] ?? null;

        // Convert Excel date format ke Carbon
        if ($tanggal) {
            try {
                $invoiceDate = is_numeric($tanggal) 
                    ? Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($tanggal))
                    : Carbon::createFromFormat('Y-m-d', $tanggal);
            } catch (\Exception $e) {
                $invoiceDate = Carbon::now();
            }
        } else {
            $invoiceDate = Carbon::now();
        }

        // Cek apakah sale sudah ada
        $sale = Sale::where('invoice_number', $invoiceNumber)->first();

        if ($sale) {
            // Update jika sudah ada
            $sale->update([
                'unit_name' => $row['unit_usaha'] ?? $row['unit_name'] ?? null,
                'department' => $row['departement'] ?? $row['department'] ?? null,
                'warehouse' => $row['gudang'] ?? $row['warehouse'] ?? null,
                'customer_name' => $customerName,
                'phone_number' => $phoneNumber,
                'sales_person' => $row['sales'] ?? $row['sales_person'] ?? null,
                'payment_method' => $row['pembayaran'] ?? $row['payment_method'] ?? null,
                'status' => $row['status'] ?? 'Lunas',
            ]);
            return null;
        }

        // Calculate followup dates
        $followupH1Date = $invoiceDate->copy()->addDay(1);
        $followupH7Date = $invoiceDate->copy()->addDays(7);
        $followup1MonthDate = $invoiceDate->copy()->addDays(30);

        // Buat sale baru
        $newSale = new Sale([
            'invoice_number' => $invoiceNumber,
            'invoice_date' => $invoiceDate,
            'unit_name' => $row['unit_usaha'] ?? $row['unit_name'] ?? null,
            'department' => $row['departement'] ?? $row['department'] ?? null,
            'warehouse' => $row['gudang'] ?? $row['warehouse'] ?? null,
            'customer_name' => $customerName,
            'phone_number' => $phoneNumber,
            'sales_person' => $row['sales'] ?? $row['sales_person'] ?? null,
            'payment_method' => $row['pembayaran'] ?? $row['payment_method'] ?? null,
            'status' => $row['status'] ?? 'Lunas',
            'followup_h1_date' => $followupH1Date,
            'followup_h7_date' => $followupH7Date,
            'followup_1month_date' => $followup1MonthDate,
            'followup_h1_status' => 'pending',
            'followup_h7_status' => 'pending',
            'followup_1month_status' => 'pending',
        ]);

        $newSale->save();

        // Create aftercare records automatically
        $this->createAfterCareRecords($newSale, $invoiceDate);

        return null;
    }

    private function createAfterCareRecords(Sale $sale, Carbon $invoiceDate)
    {
        $aftercareTypes = [
            'Aftercare h+1' => 1,      // +1 hari
            'Followup h+7' => 7,       // +7 hari
            'Followup h+1bulan' => 30, // +30 hari
        ];

        foreach ($aftercareTypes as $type => $daysToAdd) {
            $scheduledDate = $invoiceDate->copy()->addDays($daysToAdd);
            
            CustomerAftercare::create([
                'sale_id' => $sale->id,
                'customer_name' => $sale->customer_name,
                'phone_number' => $sale->phone_number,
                'type' => $type,
                'scheduled_date' => $scheduledDate,
                'status' => 'pending',
            ]);
        }
    }

    public function batchSize(): int
    {
        return 500;
    }
}
