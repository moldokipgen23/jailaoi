<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice #{{ $transaction->id }}</title>
    <style>
        @page { margin: 20px; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; padding-bottom: 15px; border-bottom: 3px solid #2ECC71; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 22px; color: #2ECC71; }
        .header p { margin: 3px 0 0; font-size: 11px; color: #888; }
        .invoice-title { font-size: 18px; font-weight: bold; margin-bottom: 15px; }
        .row { width: 100%; overflow: hidden; margin-bottom: 10px; }
        .col-6 { width: 48%; float: left; }
        .col-6.right { text-align: right; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th { background: #2ECC71; color: #fff; padding: 8px; text-align: left; font-size: 11px; }
        td { padding: 7px; border-bottom: 1px solid #ddd; font-size: 11px; }
        .total-row td { font-weight: bold; border-top: 2px solid #333; border-bottom: 0; padding-top: 10px; }
        .footer { text-align: center; margin-top: 30px; font-size: 10px; color: #aaa; border-top: 1px solid #ddd; padding-top: 10px; }
        .badge-active { color: #2ECC71; font-weight: bold; }
        .badge-expired { color: #e74c3c; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ App_Name() }}</h1>
        <p>Invoice</p>
    </div>

    <div class="invoice-title">INVOICE #{{ $transaction->id }}</div>

    <div class="row">
        <div class="col-6">
            <strong>Bill To:</strong><br>
            {{ $transaction->user->full_name ?? 'N/A' }}<br>
            {{ $transaction->user->email ?? '' }}<br>
            @if($transaction->user->mobile_number)
                {{ $transaction->user->mobile_number }}
            @endif
        </div>
        <div class="col-6 right">
            <strong>Invoice Date:</strong> {{ date('d M Y', strtotime($transaction->created_at)) }}<br>
            <strong>Transaction ID:</strong> {{ $transaction->transaction_id }}<br>
            <strong>Status:</strong>
            <span class="{{ $transaction->status ? 'badge-active' : 'badge-expired' }}">
                {{ $transaction->status ? 'Active' : 'Expired' }}
            </span>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width:60%">Description</th>
                <th style="width:20%">Period</th>
                <th style="width:20%;text-align:right">Amount</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $transaction->description ?: ($transaction->package->name ?? 'Subscription') }}</td>
                <td>{{ date('d M Y', strtotime($transaction->created_at)) }} — {{ date('d M Y', strtotime($transaction->expiry_date)) }}</td>
                <td style="text-align:right">{{ $currency }}{{ number_format((float)$transaction->price, 2) }}</td>
            </tr>
            <tr class="total-row">
                <td colspan="2" style="text-align:right">Total</td>
                <td style="text-align:right">{{ $currency }}{{ number_format((float)$transaction->price, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        {{ App_Name() }} — Thank you for your purchase!<br>
        Generated on {{ date('d M Y H:i') }}
    </div>
</body>
</html>
