<?php

namespace App\Services;

use App\Models\General_Setting;
use App\Models\Transaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class InvoiceService
{
    public function generate(Transaction $transaction): string
    {
        $currency = $this->getCurrency();

        $pdf = Pdf::loadView('invoice', [
            'transaction' => $transaction,
            'currency' => $currency,
        ]);

        $filename = "invoice_{$transaction->id}.pdf";
        $path = "invoices/{$filename}";

        Storage::disk('local')->put($path, $pdf->output());

        return $path;
    }

    public function download(Transaction $transaction)
    {
        $currency = $this->getCurrency();

        $pdf = Pdf::loadView('invoice', [
            'transaction' => $transaction,
            'currency' => $currency,
        ]);

        return $pdf->download("invoice_{$transaction->id}.pdf");
    }

    public function stream(Transaction $transaction)
    {
        $currency = $this->getCurrency();

        $pdf = Pdf::loadView('invoice', [
            'transaction' => $transaction,
            'currency' => $currency,
        ]);

        return $pdf->stream("invoice_{$transaction->id}.pdf");
    }

    private function getCurrency(): string
    {
        try {
            $setting = General_Setting::where('key', 'currency_code')->first();
            return $setting?->value ?? '₹';
        } catch (\Exception $e) {
            return '₹';
        }
    }
}
