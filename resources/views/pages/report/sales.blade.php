@extends('layouts.app')

@section('content')
    <div class="page-content" x-data="salesData()">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row justify-content-center">
            <div class="col-8">
                <div class="card">
                    <div class="card-body">
                        <p class="card-title">Filter Penjualan</p>
                        <hr>
                        <form action="{{ route('report.sales') }}" method="GET">
                            <div class="row">
                                <div class="col-12">
                                    <label for="product_id" class="form-label">Bulan Invoice</label>
                                    <input type="month" class="form-control" id="month" name="month"
                                        value="{{ request('month') }}">
                                </div>
                            </div>
                            <div class="login-separater text-center mb-4">
                                <span>ATAU</span>
                                <hr>
                            </div>
                            <div class="row">
                                <div class="col-6">
                                    <div class="mb-3">
                                        <label for="start_date" class="form-label">Tanggal Awal</label>
                                        <input type="date" class="form-control" id="start_date" name="start_date"
                                            value="{{ request('start_date') }}" required>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="mb-3">
                                        <label for="end_date" class="form-label">Tanggal Akhir</label>
                                        <input type="date" class="form-control" id="end_date" name="end_date"
                                            value="{{ request('end_date') }}" required>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">Cari</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Laporan Penjualan</h5>
                        <hr>
                        <div id="exportButton" class="row mb-3">
                            <div class="col-md-6">
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="salesTable">
                                <thead>
                                    <tr>
                                        <th width="5%">#</th>
                                        <th>Invoice</th>
                                        <th>Total HPP</th>
                                        <th>Total Harga</th>
                                        <th>Kasir</th>
                                        <th>Tanggal</th>
                                        <th width="10%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($sales as $sale)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $sale->invoice_number }}</td>
                                            <td>Rp. {{ number_format($sale->saleItems->sum('hpp'), 0, ',', '.') }}</td>
                                            <td>Rp. {{ number_format($sale->total_amount, 0, ',', '.') }}</td>
                                            <td>{{ $sale->cashier->name ?? '-' }}</td>
                                            <td>{{ $sale->created_at->format('d-m-Y H:i:s') }}</td>
                                            <td>
                                                <button type="button" class="btn btn-primary btn-sm"
                                                    @click="showDetail({{ json_encode($sale) }})">
                                                    <i class="bx bx-detail"></i> Detail
                                                </button>
                                                <form action="{{ route('report.sales.destroy', $sale->id) }}"
                                                    method="POST" class="d-inline delete-form">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-danger btn-sm delete-button">
                                                        <i class="bx bx-trash"></i> Hapus
                                                    </button>
                                                </form>
                                                <a target="_blank" href="{{ route('report.sales.print', $sale->id) }}" class="btn btn-info btn-sm">
                                                    <i class="bx bx-printer"></i> Print
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <hr>
                        <div class="row justify-content-end">
                            <div class="col-3">
                                <div class="row">
                                    <div class="col-6">
                                        <p>Total Penjualan:</p>
                                    </div>
                                    <div class="col-6">
                                        <strong>{{ $sales->count() }}</strong>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        <p>Total Penjualan:</p>
                                    </div>
                                    <div class="col-6">
                                        <strong>Rp. {{ number_format($sales->sum('total_amount'), 0, ',', '.') }}</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detail Modal -->
        <div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="detailModalLabel">Detail Transaksi</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <table class="w-100">
                            <tr>
                                <th>Invoice</th>
                                <td>:</td>
                                <td><span x-text="detail.invoice_number"></span></td>
                            </tr>
                            <tr>
                                <th>Kasir</th>
                                <td>:</td>
                                <td><span x-text="detail.cashier_name"></span></td>
                            </tr>
                            <tr>
                                <th>Tanggal</th>
                                <td>:</td>
                                <td><span x-text="detail.created_at"></span></td>
                            </tr>
                        </table>
                        <hr>

                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th width="5%">#</th>
                                        <th>Produk</th>
                                        <th>Qty</th>
                                        <th>Discount</th>
                                        <th>Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="(item, index) in detail.items" :key="index">
                                        <tr>
                                            <td x-text="index + 1"></td>
                                            <td x-text="`(${item.product_code}) ${item.product_name}`"></td>
                                            <td x-text="`${item.quantity} ${item.unit}`"></td>
                                            <td x-text="`Rp. ${Number(item.discount).toLocaleString('ID')}`"></td>
                                            <td x-text="`Rp. ${Number(item.subtotal).toLocaleString('ID')}`"></td>
                                        </tr>
                                    </template>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="4" class="text-end">Subtotal</th>
                                        <td
                                            x-text="`Rp. ${detail.subtotal}`">
                                        </td>
                                    </tr>
                                    <tr>
                                        <th colspan="4" class="text-end">Total Discount</th>
                                        <td x-text="`Rp. ${detail.total_discount}`"></td>
                                    </tr>
                                    <tr>
                                        <th colspan="4" class="text-end">Total</th>
                                        <td x-text="`Rp. ${detail.total_amount}`"></td>
                                    </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script defer>
        document.addEventListener('alpine:init', () => {
            Alpine.data('salesData', () => ({
                detail: {
                    invoice_number: '',
                    total_hpp: '',
                    total_amount: '',
                    cashier_name: '',
                    created_at: '',
                    items: []
                },
                showDetail(sale) {
                    this.detail.invoice_number = sale.invoice_number;
                    this.detail.total_hpp = sale.sale_items.reduce((sum, item) => sum + Number(item
                        .hpp), 0).toLocaleString('id-ID');
                    total_discount = sale.sale_items.reduce((sum, item) => sum + Number(item
                        .discount), 0);
                    this.detail.total_discount = total_discount.toLocaleString('id-ID');
                    this.detail.subtotal = (Number(sale.total_amount) + total_discount)
                        .toLocaleString('id-ID');
                    this.detail.total_amount = Number(sale.total_amount).toLocaleString('id-ID');
                    this.detail.cashier_name = sale.cashier ? sale.cashier.name : '-';
                    this.detail.created_at = new Date(sale.created_at).toLocaleString('id-ID', {
                        day: 'numeric',
                        month: 'long',
                        year: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit',
                    });
                    this.detail.items = sale.sale_items.map(item => ({
                        product_code: item.product.sku,
                        product_name: item.product.name,
                        quantity: item.quantity,
                        unit: item.product.unit,
                        price: item.price,
                        discount: item.discount,
                        subtotal: item.subtotal
                    }));

                    console.log(sale);
                    console.log(this.detail);


                    new bootstrap.Modal(document.getElementById('detailModal')).show();
                }
            }));
        });

        $(document).ready(function() {
            var table = $('#salesTable').DataTable({
                buttons: ['excel', 'pdf', 'print']
            });

            table.buttons().container()
                .appendTo('#exportButton .col-md-6:eq(0)');

            // SweetAlert for delete confirmation
            $('.delete-button').on('click', function(e) {
                e.preventDefault();
                const form = $(this).closest('.delete-form');
                Swal.fire({
                    title: 'Are you sure?',
                    text: "This action cannot be undone!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>
@endpush
