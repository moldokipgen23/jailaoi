<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Common;
use App\Models\Package;
use App\Models\Payment_Option;
use App\Models\Transaction;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CashfreeController extends Controller
{
    private Common $common;

    public function __construct()
    {
        $this->common = new Common;
    }

    /** Create a Cashfree order and return the payment_session_id to the client. */
    public function createOrder(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'user_id'    => 'required',
                'package_id' => 'required',
                'amount'     => 'nullable|numeric',
                'order_id'   => 'required|string|regex:/^[A-Za-z0-9_-]{1,100}$/',
                'return_url' => 'nullable|string',
            ]);

            if ($validation->fails()) {
                return $this->common->API_Response(400, $validation->errors()->first(), null);
            }

            $package = Package::where('id', $request->package_id)->where('status', 1)->first();
            if (!$package || (float) $package->price <= 0) {
                return $this->common->API_Response(400, 'Package is unavailable.', null);
            }
            if (DB::table('tbl_cashfree_orders')->where('order_id', $request->order_id)->exists()) {
                return $this->common->API_Response(400, 'Please start a new payment attempt.', null);
            }

            // Load Cashfree credentials from the payment option row
            $option = Payment_Option::where('name', 'cashfree')->first();
            if (!$option || empty($option->key_1) || empty($option->key_2)) {
                return $this->common->API_Response(400, 'Cashfree is not configured. Please add App ID and Secret Key in the admin panel.', null);
            }

            $appId     = $option->key_1;
            $secretKey = $option->key_2;
            $isLive    = $option->is_live === '1';
            $baseUrl   = $isLive
                ? 'https://api.cashfree.com/pg'
                : 'https://sandbox.cashfree.com/pg';

            // JAILAOI: Flutter Web does a full-page redirect for Cashfree checkout
            // (no in-app WebView like Android/iOS), so it must send back its own
            // origin as return_url. Restrict to known app hosts to avoid open redirect.
            $allowedReturnHosts = ['jailaoi.com', 'www.jailaoi.com', 'm.jailaoi.com', 'portal.jailaoi.com', 'localhost'];
            $returnUrl = env('APP_URL') . '/payment/cashfree/callback?order_id={order_id}';
            if ($request->filled('return_url')) {
                $host = parse_url($request->return_url, PHP_URL_HOST);
                if ($host && in_array($host, $allowedReturnHosts, true)) {
                    $separator = str_contains($request->return_url, '?') ? '&' : '?';
                    $returnUrl = $request->return_url . $separator . 'order_id={order_id}';
                }
            }

            $payload = [
                'order_id'     => $request->order_id,
                'order_amount' => (float) $package->price,
                'order_currency' => 'INR',
                'customer_details' => [
                    'customer_id'    => (string) $request->user_id,
                    'customer_email' => $request->email ?? 'user@jailaoi.com',
                    'customer_phone' => $request->phone ?? '9999999999',
                ],
                'order_meta' => [
                    'return_url' => $returnUrl,
                ],
            ];

            $response = Http::timeout(15)->withHeaders([
                'x-api-version' => '2023-08-01',
                'x-client-id'   => $appId,
                'x-client-secret' => $secretKey,
                'Content-Type'  => 'application/json',
            ])->post("{$baseUrl}/orders", $payload);

            if (!$response->successful()) {
                $msg = $response->json('message') ?? $response->body();
                return $this->common->API_Response(400, "Cashfree error: {$msg}", null);
            }

            $data = $response->json();

            // JAILAOI: Persist order context server-side so the webhook can complete
            // the purchase on its own — it never trusts fields sent by the client.
            DB::table('tbl_cashfree_orders')->insert(
                [
                    'order_id' => $request->order_id,
                    'user_id'    => $request->user_id,
                    'package_id' => $request->package_id,
                    'amount'     => (float) $package->price,
                    'status'     => 'created',
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );

            return $this->common->API_Response(200, 'Order created', [
                'payment_session_id' => $data['payment_session_id'] ?? null,
                'order_id'           => $data['order_id'] ?? $request->order_id,
                'is_live'            => $isLive,
            ]);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }

    /** Verify order status after payment (called from client after checkout). */
    public function verifyOrder(Request $request)
    {
        try {
            $request->validate(['order_id' => 'required|string', 'user_id' => 'nullable|integer|min:1']);
            $ledger = DB::table('tbl_cashfree_orders')->where('order_id', $request->order_id)->first();
            $userId = $request->user_id ?? $ledger?->user_id ?? 0;
            $data = app(\App\Services\CashfreeOrderVerifier::class)->verify($request->order_id, (int) $userId);
            $status = $data['order_status'] ?? 'UNKNOWN';
            return $this->common->API_Response(200, $status === 'PAID' ? 'Payment verified' : "Payment status: {$status}", [
                'paid' => $status === 'PAID', 'order_status' => $status, 'order_id' => $request->order_id,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['status' => 422, 'message' => $e->validator->errors()->first()], 422);
        } catch (Exception $e) {
            Log::error('Cashfree verification failed: ' . $e->getMessage());
            return response()->json(['status' => 503, 'message' => 'Payment verification is temporarily unavailable.'], 503);
        }
    }
    public function createSubscription(Request $request)
    {
        try {
            if (DB::table('tbl_general_setting')->where('key', 'cashfree_subscription_enabled')->value('value') !== '1') {
                return $this->common->API_Response(400, 'Auto-renew subscriptions are currently unavailable.', null);
            }
            $validation = Validator::make($request->all(), [
                'user_id'         => 'required',
                'package_id'      => 'required',
                'subscription_id' => 'required|string|regex:/^[A-Za-z0-9_-]{1,100}$/',
                'email'           => 'required|email',
                'phone'           => 'required|string',
                'name'            => 'nullable|string',
                'return_url'      => 'nullable|string',
            ]);

            if ($validation->fails()) {
                return $this->common->API_Response(400, $validation->errors()->first(), null);
            }

            $pkg = Package::where('id', $request->package_id)->where('status', '1')->first();
            if (!$pkg || (float) $pkg->price <= 0 || !in_array(strtolower($pkg->type), ['month', 'year'], true) || (int) $pkg->time < 1) {
                return $this->common->API_Response(400, __('api_msg.please_enter_right_package_id'), null);
            }

            if (DB::table('tbl_cashfree_subscriptions')->where('subscription_id', $request->subscription_id)->exists()) {
                return $this->common->API_Response(400, 'Please start a new subscription attempt.', null);
            }

            $option = Payment_Option::where('name', 'cashfree')->first();
            if (!$option || empty($option->key_1) || empty($option->key_2)) {
                return $this->common->API_Response(400, 'Cashfree is not configured. Please add App ID and Secret Key in the admin panel.', null);
            }

            $appId     = $option->key_1;
            $secretKey = $option->key_2;
            $isLive    = $option->is_live === '1';
            $baseUrl   = $isLive
                ? 'https://api.cashfree.com/pg'
                : 'https://sandbox.cashfree.com/pg';

            $allowedReturnHosts = ['jailaoi.com', 'www.jailaoi.com', 'm.jailaoi.com', 'portal.jailaoi.com', 'localhost'];
            $returnUrl = env('APP_URL') . '/payment/cashfree/callback?subscription_id={subscription_id}';
            if ($request->filled('return_url')) {
                $host = parse_url($request->return_url, PHP_URL_HOST);
                if ($host && in_array($host, $allowedReturnHosts, true)) {
                    $separator = str_contains($request->return_url, '?') ? '&' : '?';
                    $returnUrl = $request->return_url . $separator . 'subscription_id={subscription_id}';
                }
            }

            // Map our package duration to Cashfree's plan interval fields
            $intervalType = strtoupper(strtolower($pkg->type ?? 'Month')) === 'YEAR' ? 'YEAR' : 'MONTH';
            $intervals = max(1, (int) ($pkg->time ?? 1));
            // Generous cap on renewal cycles — the mandate itself is cancellable anytime,
            // this is just Cashfree's required upper bound, not a real limit we intend to hit.
            $maxCycles = $intervalType === 'YEAR' ? 20 : 60;
            $amount = (float) $pkg->price;

            $payload = [
                'subscription_id' => $request->subscription_id,
                'customer_details' => [
                    'customer_name'  => $request->name ?? 'JailaOi User',
                    'customer_email' => $request->email,
                    'customer_phone' => $request->phone,
                ],
                'plan_details' => [
                    // Cashfree only allows alphanumerics and a few special characters in plan_name
                    'plan_name'         => preg_replace('/[^A-Za-z0-9 _-]/', '', $pkg->name . ' Auto Renew'),
                    'plan_type'         => 'PERIODIC',
                    'plan_currency'     => 'INR',
                    'plan_amount'       => $amount,
                    'plan_max_amount'   => $amount,
                    'plan_max_cycles'   => $maxCycles,
                    'plan_intervals'    => $intervals,
                    'plan_interval_type' => $intervalType,
                ],
                'authorization_details' => [
                    'authorization_amount' => $amount,
                    'authorization_amount_refund' => false,
                    'payment_methods' => ['card', 'upi', 'enach'],
                ],
                'subscription_meta' => [
                    'return_url' => $returnUrl,
                    // RBI e-mandate framework requires a pre-debit notification before
                    // every recurring charge — this is what makes Cashfree send it.
                    'notification_channel' => ['SMS', 'EMAIL'],
                ],
            ];

            $response = Http::timeout(15)->withHeaders([
                'x-api-version'   => '2025-01-01',
                'x-client-id'     => $appId,
                'x-client-secret' => $secretKey,
                'Content-Type'    => 'application/json',
            ])->post("{$baseUrl}/subscriptions", $payload);

            if (!$response->successful()) {
                $msg = $response->json('message') ?? $response->body();
                Log::error("Cashfree createSubscription failed: {$msg}");
                return $this->common->API_Response(400, "Cashfree error: {$msg}", null);
            }

            $data = $response->json();

            DB::table('tbl_cashfree_subscriptions')->insert(
                [
                    'subscription_id' => $request->subscription_id,
                    'user_id'     => $request->user_id,
                    'package_id'  => $request->package_id,
                    'plan_amount' => $amount,
                    'status'      => 'created',
                    'updated_at'  => now(),
                    'created_at'  => now(),
                ]
            );

            return $this->common->API_Response(200, 'Subscription created', [
                'subscription_session_id' => $data['subscription_session_id'] ?? null,
                'subscription_id'         => $data['subscription_id'] ?? $request->subscription_id,
                'is_live'                 => $isLive,
            ]);
        } catch (Exception $e) {
            Log::error('Cashfree createSubscription exception: ' . $e->getMessage());
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }

    /** User-facing cancel — this is the required RBI opt-out path for the recurring mandate. */
    public function subscriptionStatus(Request $request)
    {
        $request->validate(['subscription_id' => 'required|string|regex:/^[A-Za-z0-9_-]{1,100}$/']);
        $sub = DB::table('tbl_cashfree_subscriptions')->where('subscription_id', $request->subscription_id)
            ->where('user_id', $request->user_id)->first();
        if (!$sub) return response()->json(['status' => 404, 'message' => 'Subscription not found.'], 404);
        $transaction = Transaction::where('user_id', $request->user_id)->where('cf_subscription_id', $sub->subscription_id)
            ->where('status', 1)->where('expiry_date', '>', now()->format('Y-m-d H:i'))->latest('expiry_date')->first();
        return response()->json(['status' => 200, 'message' => $transaction ? 'Paid access confirmed.' : 'Awaiting payment confirmation.',
            'result' => ['paid' => (bool) $transaction, 'subscription_status' => $sub->status,
                'expiry_date' => $transaction?->expiry_date]])->header('Cache-Control', 'private, no-store');
    }

    public function cancelSubscription(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'user_id'         => 'required',
                'subscription_id' => 'required|string|regex:/^[A-Za-z0-9_-]{1,100}$/',
            ]);
            if ($validation->fails()) {
                return $this->common->API_Response(400, $validation->errors()->first(), null);
            }

            $sub = DB::table('tbl_cashfree_subscriptions')->where('subscription_id', $request->subscription_id)->first();
            if (!$sub || (string) $sub->user_id !== (string) $request->user_id) {
                return $this->common->API_Response(400, 'Subscription not found.', null);
            }

            $option = Payment_Option::where('name', 'cashfree')->first();
            if (!$option || empty($option->key_1) || empty($option->key_2)) {
                return $this->common->API_Response(400, 'Cashfree not configured.', null);
            }

            $isLive  = $option->is_live === '1';
            $baseUrl = $isLive ? 'https://api.cashfree.com/pg' : 'https://sandbox.cashfree.com/pg';

            $response = Http::timeout(15)->withHeaders([
                'x-api-version'   => '2025-01-01',
                'x-client-id'     => $option->key_1,
                'x-client-secret' => $option->key_2,
                'Content-Type'    => 'application/json',
            ])->post("{$baseUrl}/subscriptions/{$request->subscription_id}/manage", [
                'subscription_id' => $request->subscription_id,
                'action'          => 'CANCEL',
            ]);

            if (!$response->successful()) {
                $msg = $response->json('message') ?? $response->body();
                return $this->common->API_Response(400, "Cashfree error: {$msg}", null);
            }

            DB::table('tbl_cashfree_subscriptions')->where('subscription_id', $request->subscription_id)
                ->update(['status' => 'cancelled', 'updated_at' => now()]);

            return $this->common->API_Response(200, 'Subscription cancelled. No further renewals will be charged.');
        } catch (Exception $e) {
            Log::error('Cashfree cancelSubscription exception: ' . $e->getMessage());
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }

    /**
     * Server-to-server webhook — Cashfree calls this directly once a payment
     * resolves, independent of whether the customer's browser/app is still
     * open. This is the source of truth for crediting a purchase; the
     * client-side verify+addTransaction path is just for instant UI feedback
     * and is safe to run in parallel because both paths dedupe on order_id.
     */
    public function webhook(Request $request)
    {
        try {
            $option = Payment_Option::where('name', 'cashfree')->first();
            if (!$option || empty($option->key_2)) {
                Log::error('Cashfree webhook: gateway not configured');
                return response()->json(['status' => 400], 400);
            }

            $secretKey = $option->key_2;
            $timestamp = $request->header('x-webhook-timestamp');
            $signature = $request->header('x-webhook-signature');
            $rawBody   = $request->getContent();

            if (!$timestamp || !$signature) {
                Log::warning('Cashfree webhook: missing signature headers');
                return response()->json(['status' => 400], 400);
            }

            // Reject stale requests (>5 min old) to limit replay-attack window
            $timestampSeconds = preg_match('/^[0-9]{13}$/D', $timestamp) ? (int) floor((int) $timestamp / 1000) : (int) $timestamp;
            if (!preg_match('/^[0-9]{10}([0-9]{3})?$/D', $timestamp) || abs(time() - $timestampSeconds) > 300) {
                Log::warning('Cashfree webhook: stale timestamp');
                return response()->json(['status' => 400], 400);
            }

            $expectedSignature = base64_encode(hash_hmac('sha256', $timestamp . $rawBody, $secretKey, true));
            if (!hash_equals($expectedSignature, $signature)) {
                Log::warning('Cashfree webhook: signature mismatch');
                return response()->json(['status' => 401], 401);
            }

            $payload   = json_decode($rawBody, true);
            $eventType = $payload['type'] ?? '';

            // -------------------- One-time order events (legacy path, still supported) --------------------
            if (in_array($eventType, ['PAYMENT_SUCCESS_WEBHOOK', 'PAYMENT_FAILED_WEBHOOK', 'PAYMENT_USER_DROPPED_WEBHOOK'], true)) {
                $orderId       = $payload['data']['order']['order_id'] ?? null;
                $paymentStatus = $payload['data']['payment']['payment_status'] ?? null;

                if (!$orderId) {
                    return response()->json(['status' => 200]);
                }

                $pending = DB::table('tbl_cashfree_orders')->where('order_id', $orderId)->first();
                if (!$pending) {
                    Log::warning("Cashfree webhook: unknown order_id {$orderId}");
                    return response()->json(['status' => 200]);
                }

                if ($eventType === 'PAYMENT_SUCCESS_WEBHOOK' && $paymentStatus === 'SUCCESS') {
                    $this->creditOrder($pending, $orderId);
                } else {
                    DB::table('tbl_cashfree_orders')->where('order_id', $orderId)
                        ->where('status', 'created')
                        ->update(['status' => 'failed', 'updated_at' => now()]);
                }

                return response()->json(['status' => 200]);
            }

            // -------------------- Subscription (auto-renew) events --------------------
            if (in_array($eventType, ['SUBSCRIPTION_AUTH_STATUS', 'SUBSCRIPTION_PAYMENT_SUCCESS', 'SUBSCRIPTION_PAYMENT_FAILED', 'SUBSCRIPTION_STATUS_CHANGED'], true)) {
                $subscriptionId = $payload['data']['subscription_id']
                    ?? $payload['data']['subscription_details']['subscription_id']
                    ?? null;

                if (!$subscriptionId) {
                    return response()->json(['status' => 200]);
                }

                $sub = DB::table('tbl_cashfree_subscriptions')->where('subscription_id', $subscriptionId)->first();
                if (!$sub) {
                    Log::warning("Cashfree webhook: unknown subscription_id {$subscriptionId}");
                    return response()->json(['status' => 200]);
                }

                $cfPaymentId = $payload['data']['cf_payment_id'] ?? $payload['data']['payment_id'] ?? null;

                if ($eventType === 'SUBSCRIPTION_AUTH_STATUS') {
                    // Mandate setup + the combined first charge (authorization_amount == plan amount)
                    $paymentStatus = $payload['data']['payment_status'] ?? null;
                    if ($paymentStatus === 'SUCCESS') {
                        DB::table('tbl_cashfree_subscriptions')->where('subscription_id', $subscriptionId)
                            ->update(['status' => 'active', 'updated_at' => now()]);
                        $this->creditSubscriptionCharge($sub, $subscriptionId, (string) ($cfPaymentId ?? ''), $payload['data']);
                    } else {
                        DB::table('tbl_cashfree_subscriptions')->where('subscription_id', $subscriptionId)
                            ->where('status', 'created')
                            ->update(['status' => 'failed', 'updated_at' => now()]);
                    }
                } elseif ($eventType === 'SUBSCRIPTION_PAYMENT_SUCCESS') {
                    // A recurring renewal charge succeeded — extend access, log a new transaction
                    $this->creditSubscriptionCharge($sub, $subscriptionId, (string) ($cfPaymentId ?? ''), $payload['data']);
                } elseif ($eventType === 'SUBSCRIPTION_STATUS_CHANGED') {
                    $newStatus = strtolower($payload['data']['subscription_details']['subscription_status'] ?? '');
                    if (in_array($newStatus, ['active', 'on_hold', 'cancelled', 'customer_cancelled', 'customer_paused', 'expired', 'completed', 'link_expired', 'bank_approval_pending', 'card_expired'], true)) {
                        DB::table('tbl_cashfree_subscriptions')->where('subscription_id', $subscriptionId)
                            ->update(['status' => $newStatus, 'updated_at' => now()]);
                    }
                }
                // SUBSCRIPTION_PAYMENT_FAILED: nothing to grant, just leave existing access as-is until it naturally expires

                return response()->json(['status' => 200]);
            }

            return response()->json(['status' => 200]);
        } catch (Exception $e) {
            Log::error('Cashfree webhook error: ' . $e->getMessage());
            return response()->json(['status' => 503, 'message' => 'Payment processing is temporarily unavailable.'], 503);
        }
    }

    /** Idempotently grant the package for a paid order. Safe to call more than once. */
    private function creditOrder(object $pending, string $orderId): void
    {
        app(\App\Services\CashfreePaymentCredit::class)->order($orderId, (int) $pending->user_id, (int) $pending->package_id);
    }

    /**
     * Idempotently grant one subscription charge (the initial mandate auth-charge,
     * or any later renewal). Unlike creditOrder, this runs once PER CHARGE over the
     * subscription's lifetime — dedup key is the individual cf_payment_id, not the
     * (reusable) subscription_id.
     */
    private function creditSubscriptionCharge(object $sub, string $subscriptionId, string $chargeId, array $payment): void
    {
        if (($payment['payment_status'] ?? '') !== 'SUCCESS' || !is_numeric($payment['payment_amount'] ?? null)
            || (int) round((float) $payment['payment_amount'] * 100) !== (int) round((float) $sub->plan_amount * 100)
            || ($payment['payment_currency'] ?? '') !== 'INR'
            || filter_var($payment['authorization_details']['authorization_amount_refund'] ?? false, FILTER_VALIDATE_BOOLEAN) && ($payment['payment_type'] ?? '') === 'AUTH') {
            throw new \RuntimeException('Subscription payment does not match the agreed charge.');
        }
        if ($chargeId === '') throw new \RuntimeException('Recurring payment event has no stable payment ID.');
        DB::transaction(function () use ($sub, $subscriptionId, $chargeId) {
            $user = \App\Models\User::whereKey($sub->user_id)->lockForUpdate()->first();
            $locked = DB::table('tbl_cashfree_subscriptions')->where('subscription_id', $subscriptionId)->lockForUpdate()->first();
            if (!$user || !$locked) throw new \RuntimeException('Subscription account is unavailable.');
            $sub = $locked;
            if (Transaction::where('transaction_id', $chargeId)->exists()) {
                return; // this exact charge was already recorded (webhook retry)
            }

            $pkg = Package::where('id', $sub->package_id)->where('status', '1')->first();
            if (!$pkg || (float) $pkg->price <= 0 || !in_array(strtolower($pkg->type), ['month', 'year'], true) || (int) $pkg->time < 1) {
                Log::error("Cashfree webhook: package {$sub->package_id} not found/inactive for subscription {$subscriptionId}");
                return;
            }

            $expiry = app(\App\Services\SubscriptionExpiry::class)->next((int) $sub->user_id, $pkg);

            $txn = new Transaction();
            $txn->user_id = $sub->user_id;
            $txn->package_id = $sub->package_id;
            $txn->price = $sub->plan_amount;
            $txn->description = 'cashfree_subscription';
            $txn->transaction_id = $chargeId;
            $txn->cf_subscription_id = $subscriptionId;
            $txn->expiry_date = $expiry;
            $txn->status = 1;
            $txn->save();

            Transaction::where('user_id', $sub->user_id)
                ->where('id', '!=', $txn->id)
                ->update(['status' => 0]);
        });
    }
}
