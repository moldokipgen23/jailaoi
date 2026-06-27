<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Common;
use App\Models\Payment_Option;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
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
                'amount'     => 'required|numeric|min:1',
                'order_id'   => 'required|string',
            ]);

            if ($validation->fails()) {
                return $this->common->API_Response(400, $validation->errors()->first(), null);
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

            $payload = [
                'order_id'     => $request->order_id,
                'order_amount' => (float) $request->amount,
                'order_currency' => 'INR',
                'customer_details' => [
                    'customer_id'    => (string) $request->user_id,
                    'customer_email' => $request->email ?? 'user@jailaoi.com',
                    'customer_phone' => $request->phone ?? '9999999999',
                ],
                'order_meta' => [
                    'return_url' => env('APP_URL') . '/payment/cashfree/callback?order_id={order_id}',
                ],
            ];

            $response = Http::withHeaders([
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
            $validation = Validator::make($request->all(), [
                'order_id' => 'required|string',
            ]);

            if ($validation->fails()) {
                return $this->common->API_Response(400, $validation->errors()->first(), null);
            }

            $option = Payment_Option::where('name', 'cashfree')->first();
            if (!$option || empty($option->key_1) || empty($option->key_2)) {
                return $this->common->API_Response(400, 'Cashfree not configured.', null);
            }

            $appId     = $option->key_1;
            $secretKey = $option->key_2;
            $isLive    = $option->is_live === '1';
            $baseUrl   = $isLive
                ? 'https://api.cashfree.com/pg'
                : 'https://sandbox.cashfree.com/pg';

            $response = Http::withHeaders([
                'x-api-version'   => '2023-08-01',
                'x-client-id'     => $appId,
                'x-client-secret' => $secretKey,
            ])->get("{$baseUrl}/orders/{$request->order_id}");

            if (!$response->successful()) {
                return $this->common->API_Response(400, 'Failed to verify order.', null);
            }

            $data   = $response->json();
            $status = $data['order_status'] ?? 'UNKNOWN';
            $paid   = $status === 'PAID';

            return $this->common->API_Response(200, $paid ? 'Payment verified' : "Payment status: {$status}", [
                'paid'         => $paid,
                'order_status' => $status,
                'order_id'     => $request->order_id,
            ]);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
}
