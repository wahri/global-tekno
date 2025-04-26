@extends('layouts.app')

@section('content')
    <div class="page-content" x-data="alpineData()">
        <div class="row">
            <div class="col-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="bx bx-search"></i>Cari Barang
                        </h5>
                        <hr>
                            <div class="mb-3">
                                <input type="text" class="form-control form-control-lg" x-model="sku"
                                    placeholder="Masukkan Kode Barang [ENTER]" @keydown.enter.prevent="scanCode(sku)">
                            </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <table class="table table-bordered table-striped table-hover table-sm dataTable" id="productTable">
                            <thead>
                                <tr>
                                    <th scope="col" class="text-center">No</th>
                                    <th scope="col">Kode</th>
                                    <th scope="col">Barang</th>
                                    <th scope="col">Merk</th>
                                    <th scope="col">Kategori</th>
                                    <th scope="col">Harga Beli</th>
                                    <th scope="col">Stok</th>
                                    <th scope="col" class="text-center" width="100">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($products as $product)
                                    <tr>
                                        <td class="text-center">{{ $loop->iteration }}</td>
                                        <td>{{ $product->sku }}</td>
                                        <td>{{ $product->name }}</td>
                                        <td>{{ $product->merk }}</td>
                                        <td>{{ $product->category->name }}</td>
                                        <td>
                                            @if ($product->lastPurchaseItem)
                                                {{ number_format($product->lastPurchaseItem->price, 0, ',', '.') }}
                                            @else
                                                <span class="text-danger">Belum ada transaksi</span>
                                            @endif
                                        </td>
                                        <td>{{ $product->stock }}</td>
                                        <td class="text-center">
                                            <div class="input-group input-group-sm">
                                                <input type="number" min="1" class="form-control"
                                                    x-model="qty[{{ $product->id }}]" x-init="qty[{{ $product->id }}] = 1">
                                                <button class="btn btn-success" type="button"
                                                    @click="addToCart({{ $product->id }}, qty[{{ $product->id }}])">
                                                    <i class="bx bx-cart"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="card-title">
                                <i class="bx bx-cart"></i>Keranjang
                            </h5>
                            <button class="btn btn-danger btn-sm" type="button" @click="clearCart()">
                                <i class="bx bx-trash"></i> Kosongkan Keranjang
                            </button>
                        </div>
                        <hr>
                        <table class="table table-bordered table-striped table-hover table-sm">
                            <thead>
                                <tr>
                                    <th scope="col" class="text-center">No</th>
                                    <th scope="col">Barang</th>
                                    <th scope="col">Harga Beli</th>
                                    <th scope="col">Harga Jual</th>
                                    <th scope="col" class="text-center" width="100">Qty</th>
                                    <th scope="col">Total</th>
                                    <th scope="col" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-if="carts.length === 0">
                                    <tr>
                                        <td colspan="7" class="text-center">Keranjang Kosong</td>
                                    </tr>
                                </template>
                                <template x-for="cart in carts" :key="cart.id">
                                    <tr>
                                        <td x-text="`${carts.indexOf(cart) + 1}`"></td>
                                        <td x-text="`(${cart.product.sku}) ${cart.product.name}`"></td>
                                        <td>
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text">Rp</span>
                                                <input type="number" class="form-control form-control-sm"
                                                    x-model="cart.price"
                                                    @input.debounce.750ms="updateCart(cart.id, cart.price, cart.quantity, cart.selling_price)">

                                            </div>
                                        </td>
                                        <td>
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text">Rp</span>
                                                <input type="number" class="form-control form-control-sm"
                                                    x-model="cart.selling_price"
                                                    @input.debounce.750ms="updateCart(cart.id, cart.price, cart.quantity, cart.selling_price)">
                                            </div>
                                        </td>
                                        <td>
                                            <input type="number" class="form-control form-control-sm"
                                                x-model="cart.quantity"
                                                @input.debounce.750ms="updateCart(cart.id, cart.price, cart.quantity, cart.selling_price)">
                                        </td>
                                        <td x-text="`Rp. ${parseInt(cart.subtotal).toLocaleString('ID')}`"></td>
                                        <td class="text-center">
                                            <button class="btn btn-sm btn-danger" type="button"
                                                @click="removeFromCart(cart.id)">
                                                <i class="bx bx-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                        <hr>
                        <div class="row justify-content-end mb-3">
                            <span class="fw-bold col-3">Total:</span>
                            <div class="col-5">
                                <span class="fw-bold" x-text="`Rp. ${parseInt(total).toLocaleString('ID')}`"></span>
                            </div>
                        </div>
                        <div class="row justify-content-end mb-3">
                            <span class="fw-bold col-3">Order Number:</span>
                            <div class="col-5">
                                <input type="text" class="form-control form-control" id="order_number"
                                    name="order_number" placeholder="Order Number" x-model="order_number">
                            </div>
                        </div>
                        <div class="row justify-content-end mb-3">
                            <span class="fw-bold col-3">Supplier:</span>
                            <div class="col-5">
                                <select class="form-select form-select" id="supplier_id" name="supplier_id"
                                    x-model="supplier_id">
                                    <option value="">Pilih Supplier</option>
                                    @foreach ($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row justify-content-end mb-3">
                            <div class="col-5">
                                <button class="btn btn-primary btn-lg" type="button"
                                    @click="submitOrder(order_number, supplier_id)">
                                    <i class="bx bx-check"></i> Proses Transaksi
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script defer>
        $(document).ready(function() {
            $('.dataTable').DataTable();
            $(".wrapper").addClass("toggled");
            $(".sidebar-wrapper").hover(function() {
                $(".wrapper").addClass("sidebar-hovered");
            }, function() {
                $(".wrapper").removeClass("sidebar-hovered");
            })
        });
    </script>
    <script defer>
        document.addEventListener('alpine:init', () => {
            Alpine.data('alpineData', () => ({
                carts: @json($carts),
                total: @json($carts->sum('subtotal')),
                order_number: '{{ $order_number }}',
                qty: {},
                sku: '',
                supplier_id: '',
                init() {
                    this.$watch('carts', (value) => {
                        this.total = value.reduce((acc, cart) => acc + parseInt(cart.subtotal ||
                            0), 0);
                    });
                },
                scanCode(sku) {
                    axios.post(`{{ url('restock/scanCode') }}`, {
                        sku: sku
                    }).then(res => {
                        this.carts = res.data.carts;
                        this.sku = '';
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: res.data.message,
                        });
                    }).catch(err => {
                        console.error(err);
                        this.sku = '';
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to scan code!',
                        });
                    });
                },
                addToCart(product_id, quantity) {
                    axios.post(`{{ url('restock/addToCart') }}`, {
                        product_id: product_id,
                        quantity: quantity
                    }).then(res => {
                        this.carts = res.data.carts;
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: res.data.message,
                        });
                    }).catch(err => {
                        console.error(err);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to add product to cart!',
                        });
                    });
                },
                updateCart(id, price, quantity, selling_price) {
                    axios.put(`{{ url('restock/updateCart') }}/${id}`, {
                        price: price,
                        quantity: quantity,
                        selling_price: selling_price
                    }).then(res => {
                        this.carts = res.data.carts;
                    }).catch(err => {
                        console.error(err);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to update cart!',
                        });
                    });
                },
                removeFromCart(id) {
                    axios.delete(`{{ url('restock/removeFromCart') }}/${id}`).then(res => {
                        this.carts = res.data.carts;
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: res.data.message,
                        });
                    }).catch(err => {
                        console.error(err);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to remove product from cart!',
                        });
                    });
                },
                clearCart() {
                    axios.delete(`{{ url('restock/clearCart') }}`).then(res => {
                        this.carts = res.data.carts;
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: res.data.message,
                        });
                    }).catch(err => {
                        console.error(err);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to clear cart!',
                        });
                    });
                },
                submitOrder(order_number, supplier_id) {
                    axios.post(`{{ url('restock/submitOrder') }}`, {
                        order_number: order_number,
                        supplier_id: supplier_id
                    }).then(res => {
                        this.carts = res.data.carts;
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: res.data.message,
                        });
                        location.reload();
                    }).catch(err => {
                        console.error(err);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: err.response.data.message ||
                                'Failed to submit order!',
                        });
                    });
                }
            }));
        });
    </script>
@endpush
