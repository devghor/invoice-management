<html>

<head>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }

        .header {
            text-align: center;
            margin-bottom: 10px;
        }

        .header h1 {
            font-size: 18px;
            margin: 0;
        }

        .header p {
            margin: 2px 0;
        }

        .info {
            margin-bottom: 20px;
        }

        .info p {
            margin: 3px 0;
        }

        .invoice-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            font-size: 12px;
        }

        .invoice-table th,
        .invoice-table td {
            border: 1px solid #000;
            padding: 5px;
            text-align: center;
        }


        .footer {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
            font-weight: bold;
        }

        .footer>div {
            width: 50%;
        }

        .footer>div:first-child {
            text-align: left;
        }

        .footer>div:last-child {
            text-align: right;
        }
    </style>
</head>

<body>

    <div class="header">
        <h1>{{ $invoice->company->name_en ?? '' }}</h1>
        <p>{{ $invoice->company->address ?? '' }}</p>
        @php
            $phone = $invoice->company->mobile_no ?? '';
            $email = $invoice->company->email ?? '';
        @endphp
        <p>
            @if ($phone)
                Phone: {{ $phone }} |
                @endif @if ($email)
                    Email: {{ $email }}
                @endif
        </p>
    </div>

    <div class="info">
        <p><strong>Bill No:</strong> {{ $invoice->bill_no }}</p>
        <p><strong>Date:</strong> {{ $invoice->invoice_date ? $invoice->invoice_date->format('d/m/Y') : null }}</p>
        <p><strong>Name:</strong> {{ $invoice->customer->name ?? '' }}</p>
        <p><strong>Address:</strong> {{ $invoice->customer->address ?? '' }}</p>
    </div>

    <table class="invoice-table">
        <thead>
            <tr>
                <th>SL</th>
                <th>R.Q </br> S.L</th>
                <th>Parts Name</th>
                <th>M/C NAME</th>
                <th>P:NO</th>
                <th>Brand</th>
                <th>Qty</th>
                <th>Rate</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
            @php
                $netPrice = 0;
            @endphp
            @foreach ($invoice->invoiceItems as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->rq_sl }}</td>
                    <td>{{ $item->product->name ?? '' }}</td>
                    <td>{{ $item->product->mc_name ?? '' }}</td>
                    <td>{{ $item->product->p_no }}</td>
                    <td>{{ $item->brand }}</td>
                    <td>{{ $item->qty }}</td>
                    <td>{{ number_format($item->price_rate, 2) }}</td>
                    <td>{{ number_format($item->total_price, 2) }}</td>
                </tr>
                @php
                    $netPrice += $item->total_price;
                @endphp
            @endforeach
            @for ($i = count($invoice->invoiceItems); $i < 17; $i++)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
            @endfor
            <tr>
                <td></td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td><strong>Total:</strong> </td>
                <td>{{ number_format($netPrice, 2) }}</td>
            </tr>
        </tbody>
    </table>
    <table style="width: 100%; margin-top: 50px; border-collapse: collapse; border: none;">
        <tr>
            <td style="width: 50%; text-align: left;">
                <p style="margin: 0; display: inline-block; border-top: 1px solid #000;">
                    Received by
                </p>
            </td>
            <td style="width: 50%; text-align: right;">
                <p style="margin: 0; display: inline-block; border-top: 1px solid #000;">
                    {{ strtoupper($invoice->company->name_en ?? '') }}
                </p>
            </td>
        </tr>
    </table>

</html>
