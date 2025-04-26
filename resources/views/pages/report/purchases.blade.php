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

        <div class="row">
            <div class="col-6">
                {{-- filter by month and year --}}
                <div class="card">
                    <div class="card-body">
                        <p class="card-title">Cari Pembelian berdasarkan bulan</p>
                        <hr>
                        <form action="{{ route('report.sales') }}" method="GET">
                            <div class="row">
                                <div class="col-10">
                                    <input type="month" class="form-control" id="month" name="month"
                                        value="{{ request('month') }}">
                                </div>
                                <div class="col-2">
                                    <button type="submit" class="btn btn-primary">Cari</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-6">
                {{-- filter by start and end date --}}
                <div class="card">
                    <div class="card-body">
                        <p class="card-title">Cari Pembelian berdasarkan tanggal</p>
                        <hr>
                        <form action="{{ route('report.sales') }}" method="GET">
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
                        <h5 class="card-title">Laporan Pembelian</h5>
                        <hr>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped dataTable">
                                <thead>
                                    <tr>
                                        <th width="5%">#</th>
                                        <th>Order Number</th>
                                        <th>Tanggal</th>
                                        <th>Supplier</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($purchases as $purchase)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $purchase->order_number }}</td>
                                            <td>{{ $purchase->created_at->format('d-m-Y') }}</td>
                                            <td>{{ $purchase->supplier->name ?? '-' }}</td>
                                            <td>Rp. {{ number_format($purchase->total_amount, 0, ',', '.') }}</td>
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
    </div>
@endsection

@push('js')
    <script>
        $(document).ready(function() {
            $('.dataTable').DataTable();
        });
    </script>
@endpush
