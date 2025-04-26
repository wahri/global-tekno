<!DOCTYPE html>
<html lang="id" moznomarginboxes mozdisallowselectionprint>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Software pembelian">
    <meta name="author" content="Codekop">

    <title>Cetak Nota</title>
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet"
        href="https://app.codekop.com/posv1/helper/cetak/../../assets/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
    <link rel="stylesheet" href="https://app.codekop.com/posv1/helper/cetak/../../assets/dist/css/adminlte.min.css">
    <style>
        * {
            font-size: 12pt;
            font-family: 'Arial';
        }

        td,
        th,
        tr,
        table {
            border-collapse: collapse;
        }

        td.description,
        th.description {
            text-align: left;
            width: 275px;
            max-width: 200px;
        }

        td.no,
        th.no {
            width: 40px;
            max-width: 40px;
            text-align: center;
            word-break: break-all;
        }

        td.quantity,
        th.quantity {
            width: 50px;
            max-width: 50px;
            text-align: center;
            word-break: break-all;
        }

        td.price,
        th.price {
            width: 150px;
            max-width: 150px;
            word-break: break-all;
        }

        .centered {
            text-align: center;
            align-content: center;
        }

        .ticket {
            max-width: 400px;
        }

        img {
            max-width: inherit;
            width: inherit;
        }

        @media print {

            .hidden-print,
            .hidden-print * {
                display: none !important;
            }
        }
    </style>
</head>

<body class="hold-transition sidebar-collapse" style="-webkit-print-color-adjust: exact !important;">
    <div class="wrapper">
        <section class="content">
            <div class="container">
                <div id="laporan">
                    <div class="mt-4">
                        <h4 class="text-center font-weight-bold">
                            GLOBAL TEKNO KOMPUTER </h4>
                        <p class="text-center">Telp. 080-0000-0000</p>
                        <div class="table-resposive-sm">
                            <span class="float-left">Akun : {{ $purchase->user->name }}</span>
                            <br>
                            <span class="float-left">Supplier : {{ $purchase->supplier->name }}</span>
                            <span class="float-right">Tanggal :
                                {{ $purchase->created_at->format('d-m-Y H:i:s') }}</span>
                            <table class="table table-bordered table-sm">
                                <thead>
                                    <tr class="font-weight-bold">
                                        <th class="quantity">No</th>
                                        <th>KETERANGAN</th>
                                        <th>JML</th>
                                        <th>HARGA</th>
                                        <th class="price">SUB TOTAL</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($purchase->purchaseItems as $item)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>({{ $item->product->sku }}) {{ $item->product->name }}</td>
                                            <td>{{ $item->quantity }}</td>
                                            <td>{{ number_format($item->price, 0, ',', '.') }}</td>
                                            <td>{{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="4" class="text-right">Total Pembelian</th>
                                        <th>{{ number_format($purchase->total_amount, 0, ',', '.') }}</th>
                                    </tr>
                                </tfoot>
                            </table>
                            <p class="text-center mt-4"> </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</body>
<script>
    window.print();
</script>

</html>
