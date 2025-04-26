@extends('layouts.app')

@section('content')
    <div class="page-content" x-data="alpineData">
        <div class="row justify-content-center">
            <div class="col-8">
                <div class="card">
                    <div class="card-body">
                        <p class="card-title">Filter Penjualan</p>
                        <hr>
                        <form action="{{ route('report.purchases') }}" method="GET">
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

        @if (session()->has('success') || session()->has('error'))
            <div class="row">
                <div class="col-12">
                    <div class="alert alert-{{ session()->has('success') ? 'success' : 'danger' }} alert-dismissible fade show"
                        role="alert">
                        {{ session('success') ?? session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>
            </div>
        @endif

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Laporan Pembelian</h5>
                        <hr>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped dataTable">
                                <thead>
                                    <tr>
                                        <th width="5%">#</th>
                                        <th>Order Number</th>
                                        <th>Kasir</th>
                                        <th>Tanggal</th>
                                        <th>Supplier</th>
                                        <th>Total</th>
                                        <th width="10%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($purchases as $purchase)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $purchase->order_number }}</td>
                                            <td>{{ $purchase->user->name }}</td>
                                            <td>{{ $purchase->created_at->format('d-m-Y H:i:s') }}</td>
                                            <td>{{ $purchase->supplier->name ?? '-' }}</td>
                                            <td>Rp. {{ number_format($purchase->total_amount, 0, ',', '.') }}</td>
                                            <td>
                                                <button type="button" class="btn btn-primary btn-sm"
                                                    @click="showDetail({{ json_encode($purchase) }})">
                                                    <i class="bx bx-detail"></i> Detail
                                                </button>
                                                <form action="{{ route('report.purchases.destroy', $purchase->id) }}"
                                                    method="POST" class="d-inline delete-form">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-danger btn-sm delete-button">
                                                        <i class="bx bx-trash"></i> Hapus
                                                    </button>
                                                </form>
                                                <a target="_blank"
                                                    href="{{ route('report.purchases.print', $purchase->id) }}"
                                                    class="btn btn-info btn-sm">
                                                    <i class="bx bx-printer"></i> Print
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center">Tidak ada data</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <hr>
                        <div class="row justify-content-end">
                            <div class="col-3">
                                <div class="row">
                                    <div class="col-6">
                                        <p>Total Pembelian:</p>
                                    </div>
                                    <div class="col-6">
                                        <strong>{{ $purchases->count() }}</strong>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        <p>Total Pembelian:</p>
                                    </div>
                                    <div class="col-6">
                                        <strong>Rp.
                                            {{ number_format($purchases->sum('total_amount'), 0, ',', '.') }}</strong>
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
                                <th>Order Number</th>
                                <td>:</td>
                                <td><span x-text="detail.order_number"></span></td>
                            </tr>
                            <tr>
                                <th>Kasir</th>
                                <td>:</td>
                                <td><span x-text="detail.user"></span></td>
                            </tr>
                            <tr>
                                <th>Supplier</th>
                                <td>:</td>
                                <td><span x-text="detail.supplier"></span></td>
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
                                        <th>Harga</th>
                                        <th>Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="(item, index) in detail.items" :key="index">
                                        <tr>
                                            <td x-text="index + 1"></td>
                                            <td x-text="`(${item.product_code}) ${item.product_name}`"></td>
                                            <td x-text="`${item.quantity} ${item.unit}`"></td>
                                            <td x-text="`Rp. ${Number(item.price).toLocaleString('ID')}`"></td>
                                            <td x-text="`Rp. ${Number(item.subtotal).toLocaleString('ID')}`"></td>
                                        </tr>
                                    </template>
                                </tbody>
                                <tfoot>
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
            Alpine.data('alpineData', () => ({
                detail: {
                    order_number: '',
                    user: '',
                    supplier: '',
                    total_amount: '',
                    created_at: '',
                    items: []
                },
                showDetail(purchase) {
                    this.detail.order_number = purchase.order_number;
                    this.detail.total_amount = Number(purchase.total_amount).toLocaleString(
                        'id-ID');
                    this.detail.user = purchase.user.name ?? '-';
                    this.detail.supplier = purchase.supplier?.name ?? '-';
                    this.detail.created_at = new Date(purchase.created_at).toLocaleString(
                        'id-ID', {
                            day: 'numeric',
                            month: 'long',
                            year: 'numeric',
                            hour: '2-digit',
                            minute: '2-digit',
                        });
                    this.detail.items = purchase.purchase_items.map(item => ({
                        product_code: item.product.sku,
                        product_name: item.product.name,
                        quantity: item.quantity,
                        unit: item.product.unit,
                        price: item.price,
                        subtotal: item.subtotal
                    }));

                    new bootstrap.Modal(document.getElementById('detailModal')).show();
                }
            }));
        });

        $(document).ready(function() {
            $('.dataTable').DataTable();

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
                })
            });
        });
    </script>
@endpush
