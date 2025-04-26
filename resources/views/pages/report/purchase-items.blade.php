@extends('layouts.app')

@section('content')
    <div class="page-content">
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
                {{-- filter by start and end date --}}
                <div class="card">
                    <div class="card-body">
                        <p class="card-title">Filter Data Penjualan</p>
                        <hr>
                        <form action="{{ route('report.purchaseItems') }}" method="GET">
                            <div class="row mb-3">
                                <div class="col-12">
                                    <label for="category_id" class="form-label">Kategori</label>
                                    <select class="form-select" id="category_id" name="category_id">
                                        <option value="">Semua Kategori</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}"
                                                {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="border rounded border p-3 mb-3">
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
                                        <label for="start_date" class="form-label">Tanggal Awal</label>
                                        <input type="date" class="form-control" id="start_date" name="start_date"
                                            value="{{ request('start_date') }}">
                                    </div>
                                    <div class="col-6">
                                        <label for="end_date" class="form-label">Tanggal Akhir</label>
                                        <input type="date" class="form-control" id="end_date" name="end_date"
                                            value="{{ request('end_date') }}">
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
                            <table class="table table-bordered table-striped dataTable" id="purchaseItemsTable">
                                <thead>
                                    <tr>
                                        <th width="5%">#</th>
                                        <th>Invoice</th>
                                        <th>Kode Barang</th>
                                        <th>Nama Barang</th>
                                        <th>Kategori</th>
                                        <th>Supplier</th>
                                        <th>Qty</th>
                                        <th>Harga Beli</th>
                                        <th>Total</th>
                                        <th>Tanggal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($purchaseItems as $item)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $item->purchase->order_number }}</td>
                                            <td>{{ $item->product->sku }}</td>
                                            <td>{{ $item->product->name }}</td>
                                            <td>{{ $item->product->category->name }}</td>
                                            <td>{{ $item->purchase->supplier->name }}</td>
                                            <td>{{ $item->quantity }}</td>
                                            <td>Rp. {{ number_format($item->price, 0, ',', '.') }}</td>
                                            <td>Rp. {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                            <td>{{ $item->created_at->format('d-m-Y H:i:s') }}</td>
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
                                        <p>Total Qty:</p>
                                    </div>
                                    <div class="col-6">
                                        <strong>{{ $purchaseItems->sum('quantity') }}</strong>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        <p>Total Pembelian:</p>
                                    </div>
                                    <div class="col-6">
                                        <strong>Rp. {{ number_format($purchaseItems->sum('subtotal'), 0, ',', '.') }}</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        $(document).ready(function() {
            var table = $('#purchaseItemsTable').DataTable({
                buttons: ['excel', 'pdf', 'print']
            });

            table.buttons().container()
                .appendTo('#exportButton .col-md-6:eq(0)');
        });
    </script>
@endpush
