@extends('layouts.app')


@section('content')
    <div class="page-content">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Add New Product</h5>
                <hr>
                <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-6">
                            <div class="mb-3">
                                <label for="sku" class="form-label">Kode Barang</label>
                                <input type="text" class="form-control @error('sku') is-invalid @enderror" id="sku"
                                    value="{{ old('sku', $sku) }}" name="sku">
                                @error('sku')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">Nama Barang</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    id="name" value="{{ old('name') }}" name="name">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="mb-3">
                                <label for="merk" class="form-label">Merk</label>
                                <input type="text" class="form-control @error('merk') is-invalid @enderror"
                                    id="merk" value="{{ old('merk') }}" name="merk">
                                @error('merk')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="mb-3">
                                <label for="category_id" class="form-label">Kategori</label>
                                <select class="form-select @error('category_id') is-invalid @enderror" id="category_id"
                                    name="category_id">
                                    <option value="">Pilih Kategori</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}"
                                            {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}</option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="mb-3">
                                <label for="stock" class="form-label">Stok Awal</label>
                                <input type="number" min="0"
                                    class="form-control @error('stock') is-invalid @enderror" id="stock"
                                    value="{{ old('stock') }}" name="stock" placeholder="0">
                                @error('stock')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="mb-3">
                                <label for="unit" class="form-label">Satuan Barang</label>
                                <select class="form-select select2-tag @error('unit') is-invalid @enderror" id="unit"
                                    name="unit">
                                    <option value="">Pilih atau ketik lalu enter</option>
                                    @foreach ($units as $unit)
                                        <option value="{{ $unit->unit }}" {{ old('unit') == $unit->unit ? 'selected' : '' }}>
                                            {{ $unit->unit }}</option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Contoh: Pcs, Set, Unit.</small>
                                @error('unit')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="mb-3">
                                <label for="price" class="form-label">Harga Jual</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" class="form-control @error('price') is-invalid @enderror"
                                        id="price" value="{{ old('price') }}" name="price"
                                        oninput="formatRupiah(this)">
                                </div>
                                @error('price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="mb-3">
                                <label for="image" class="form-label">Foto Produk</label>
                                <input type="file" accept="image/*"
                                    class="form-control @error('image') is-invalid @enderror" id="image" name="image">
                                <small class="text-muted">Format: jpg, jpeg, png</small>
                                <small class="text-muted">Maksimal ukuran: 2MB</small>
                                @error('image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <a href="{{ route('products.index') }}" class="btn btn-secondary">Back</a>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
            </div>
        </div>
    </div>
@endsection
@push('js')
    <script defer>
        $(document).ready(function() {
            $(".select2-tag").select2({
                theme: "bootstrap-5",
                width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' :
                    'style',
                placeholder: $(this).data('placeholder'),
                tags: true,
            });
        })
    </script>
@endpush
