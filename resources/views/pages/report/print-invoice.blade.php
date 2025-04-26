<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
    <title>Print Struk</title>
    <style>
        html {
            font-family: sans-serif;
            font-size: 7pt;
            line-height: 8pt !important;
        }

        p {
            line-height: 3pt !important;
        }

        table {
            width: 100%;
            margin: 0;
            font-size: 7pt;
            line-height: 3pt;
        }

        tr td {
            padding-top: 3px;
        }

        .right {
            text-align: right;
        }

        center {
            margin: 0;
        }

        .doted {
            border-bottom: 1px dotted #333;
            width: 100%;
            margin-top: 3px;
            margin-bottom: 3px;
        }
    </style>

    <script>
        window.print();
    </script> 
</head>

<body class="receipt">
    <section>
        --
        <center>
            <b> GLOBAL TEKNO KOMPUTER</b><br>
            Telp. 082-0000-0000 
        </center>
        <div class="doted"></div>
        <table>
            <tbody>
                <tr>
                    <td>
                        No. Invoice
                    </td>
                    <td>
                        :
                    </td>
                    <td>
                        {{ $sale->invoice_number }} </td>
                </tr>
                <tr>
                    <td>
                        Kasir
                    </td>
                    <td>
                        :
                    </td>
                    <td>
                        {{ $sale->cashier->name }} </td>
                </tr>
                <tr>
                    <td>
                        Tanggal
                    </td>
                    <td>
                        :
                    </td>
                    <td>
                        {{ $sale->created_at->format('d-m-Y H:i:s') }}
                    </td>
                </tr>
            </tbody>
        </table>
        <div class="doted"></div>
        <table>
            <thead style="text-align: left;">
                <tr>
                    <th>Produk</th>
                    <th>Diskon</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($sale->saleItems as $item)
                <tr>
                    <td>
                        ({{ $item->product->sku }}) {{ $item->product->name }} ({{ $item->quantity }} x {{ number_format($item->price, 0, ',', '.') }})
                    </td>
                    <td>Rp. {{ number_format($item->discount, 0, ',', '.') }}</td>
                    <td>Rp. {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="doted"></div>
        <table>
            <tbody>
                <tr>
                    <td><b>Total Diskon</b></td>
                    <td>:</td>
                    <td>{{ $sale->saleItems->sum('discount') }}</td>
                </tr>
                <tr>
                    <td><b>Total Bayar</b></td>
                    <td>:</td>
                    <td>{{ $sale->total_amount }}</td>
                </tr>
                <tr>
                    <td><b>Dibayar</b></td>
                    <td>:</td>
                    <td>{{ $sale->paid_amount }}</td>
                </tr>
                <tr>
                    <td><b>Kembali</b></td>
                    <td>:</td>
                    <td>{{ $sale->change_amount }}</td>
                </tr>
            </tbody>
        </table>
        <div class="doted"></div>
        <center>
            <b>TERIMA KASIH <br> ATAS KUNJUNGAN ANDA</b>
        </center>
        <br>
        <br>
    </section>

</body>

</html>
