<?php

namespace App\Services;

use App\Models\Package;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CashfreePaymentCredit
{
    public function order(string $orderId, int $userId, int $packageId): Transaction
    {
        $package = Package::where('id', $packageId)->where('status', 1)->first();
        if (!$package) throw ValidationException::withMessages(['package_id' => 'Package is unavailable.']);
        app(CashfreeOrderVerifier::class)->verify($orderId, $userId, $package);
        return DB::transaction(function () use ($orderId, $userId, $package) {
            // All client and webhook credits lock the same account before checking duplicates.
            $user = User::whereKey($userId)->lockForUpdate()->first();
            if (!$user) throw ValidationException::withMessages(['user_id' => 'Account is unavailable.']);
            $ledger = DB::table('tbl_cashfree_orders')->where('order_id', $orderId)->lockForUpdate()->first();
            if (!$ledger || (int) $ledger->user_id !== $userId || (int) $ledger->package_id !== $package->id) {
                throw ValidationException::withMessages(['payment' => 'Payment does not match this purchase.']);
            }
            $existing = Transaction::where('transaction_id', $orderId)->first();
            if ($existing) {
                if ($existing->user_id !== $userId || $existing->package_id !== $package->id
                    || (int) round((float) $existing->price * 100) !== (int) round((float) $ledger->amount * 100)) {
                    throw ValidationException::withMessages(['payment' => 'Payment is already associated with another purchase.']);
                }
                if ($existing->description !== 'cashfree') {
                    $existing->description = 'cashfree';
                    $existing->save();
                }
                DB::table('tbl_cashfree_orders')->where('order_id', $orderId)->update(['status' => 'paid', 'paid_at' => $existing->created_at ?? now(), 'updated_at' => now()]);
                return $existing;
            }
            $transaction = Transaction::create([
                'user_id' => $userId, 'package_id' => $package->id, 'price' => $ledger->amount,
                'description' => 'cashfree', 'transaction_id' => $orderId,
                'expiry_date' => app(SubscriptionExpiry::class)->next($userId, $package),
                'status' => 1,
            ]);
            Transaction::where('user_id', $userId)->where('id', '!=', $transaction->id)->update(['status' => 0]);
            DB::table('tbl_cashfree_orders')->where('order_id', $orderId)->update(['status' => 'paid', 'paid_at' => now(), 'updated_at' => now()]);
            return $transaction;
        });
    }
}
