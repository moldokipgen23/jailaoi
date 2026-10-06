<?php

namespace App\Services;

use App\Models\Package;
use App\Models\Payment_Option;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

class CashfreeOrderVerifier
{
    public function verify(string $orderId, int $userId, ?Package $package = null): array
    {
        $invalid = fn () => ValidationException::withMessages(['payment' => 'Payment could not be verified for this account and package.']);
        if (!preg_match('/^[A-Za-z0-9_-]{1,100}$/D', $orderId)) throw $invalid();
        $ledger = DB::table('tbl_cashfree_orders')->where('order_id', $orderId)->first();
        if (!$ledger || (int) $ledger->user_id !== $userId
            || ($package && ((int) $ledger->package_id !== $package->id
                || (int) round((float) $ledger->amount * 100) !== (int) round((float) $package->price * 100)))) throw $invalid();
        $option = Payment_Option::where('name', 'cashfree')->first();
        if (!$option || !$option->key_1 || !$option->key_2) throw $invalid();
        $base = $option->is_live === '1' ? 'https://api.cashfree.com/pg' : 'https://sandbox.cashfree.com/pg';
        $response = Http::timeout(15)->withHeaders([
            'x-api-version' => '2023-08-01', 'x-client-id' => $option->key_1, 'x-client-secret' => $option->key_2,
        ])->get($base . '/orders/' . rawurlencode($orderId));
        $data = $response->json();
        if (!$response->successful() || !is_array($data)
            || ($data['order_id'] ?? '') !== $orderId
            || (string) ($data['customer_details']['customer_id'] ?? '') !== (string) $userId
            || ($data['order_currency'] ?? '') !== 'INR'
            || (int) round((float) ($data['order_amount'] ?? 0) * 100) !== (int) round((float) $ledger->amount * 100)
            || ($package && ($data['order_status'] ?? '') !== 'PAID')) throw $invalid();
        return $data;
    }
}
