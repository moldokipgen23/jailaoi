<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Services\InvoiceService;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function __construct(
        protected InvoiceService $invoiceService
    ) {}

    public function download($id)
    {
        $transaction = Transaction::with(['user', 'package'])->findOrFail($id);
        return $this->invoiceService->download($transaction);
    }

    public function view($id)
    {
        $transaction = Transaction::with(['user', 'package'])->findOrFail($id);
        return $this->invoiceService->stream($transaction);
    }
}
