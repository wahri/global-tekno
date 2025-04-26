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
                                    <th scope="col">Harga Jual</th>
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
                                        <td>Rp. {{ number_format($product->price, 0, ',', '.') }}</td>
                                        <td>{{ $product->stock }} {{ $product->unit }}</td>
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
                                    <th scope="col">Harga</th>
                                    <th scope="col" class="text-center" width="100">Qty</th>
                                    <th scope="col">Diskon (Rp)</th>
                                    <th scope="col">Subtotal</th>
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
                                        <td x-text="`Rp. ${parseInt(cart.price).toLocaleString('ID')}`"></td>
                                        <td>
                                            <input type="number" class="form-control form-control-sm"
                                                x-model="cart.quantity"
                                                @input.debounce.750ms="updateCart(cart.id, cart.quantity, cart.discount)">
                                        </td>
                                        <td>
                                            <input type="number" class="form-control form-control-sm"
                                                x-model="cart.discount"
                                                @input.debounce.750ms="updateCart(cart.id, cart.quantity, cart.discount)">
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
                                <input type="text" class="form-control form-control" id="invoice_number"
                                    name="invoice_number" placeholder="Order Number" x-model="invoice_number">
                            </div>
                        </div>
                        <div class="row justify-content-end mb-3">
                            <span class="fw-bold col-3">Jumlah Bayar:</span>
                            <div class="col-5">
                                <div class="input-group">
                                    <input type="text" class="form-control form-control" id="paid_amount"
                                        name="paid_amount" placeholder="Masukkan Jumlah Uang" x-model="paid_amount">
                                        <button class="btn btn-success" type="button" @click="paid_amount = total">Lunas</button>
                                </div>
                            </div>
                        </div>
                        <div class="row justify-content-end mb-3">
                            <span class="fw-bold col-3">Kembalian:</span>
                            <div class="col-5">
                                <template x-if="paid_amount > 0">
                                    <span class="fw-bold" x-text="`Rp. ${parseInt(paid_amount - total).toLocaleString('ID')}`"></span>
                                </template>
                                <template x-if="paid_amount <= 0">
                                    <span class="fw-bold" x-text="`Rp. 0`"></span>
                                </template>
                            </div>
                        </div>

                        <div class="row justify-content-end mb-3">
                            <div class="col-5">
                                <button class="btn btn-primary btn-lg" type="button"
                                    @click="submitOrder(invoice_number, supplier_id)">
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
                invoice_number: '{{ $invoice_number }}',
                paid_amount: '',
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
                    axios.post(`{{ url('cashier/scanCode') }}`, {
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
                            text: err.response.data.message ||
                                'Failed to scan product!',
                        });
                    });
                },
                addToCart(product_id, quantity) {
                    axios.post(`{{ url('cashier/addToCart') }}`, {
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
                            text: err.response.data.message ||
                                'Failed to add product to cart!',
                        });
                    });
                },
                updateCart(id, quantity, discount) {
                    axios.put(`{{ url('cashier/updateCart') }}/${id}`, {
                        quantity: quantity,
                        discount: discount
                    }).then(res => {
                        this.carts = res.data.carts;
                    }).catch(err => {
                        console.error(err);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: err.response.data.message ||
                                'Failed to update cart!',
                        });
                    });
                },
                removeFromCart(id) {
                    axios.delete(`{{ url('cashier/removeFromCart') }}/${id}`).then(res => {
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
                    axios.delete(`{{ url('cashier/clearCart') }}`).then(res => {
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
                submitOrder(invoice_number, supplier_id) {
                    axios.post(`{{ url('cashier/submitOrder') }}`, {
                        invoice_number: invoice_number,
                        paid_amount: this.paid_amount,
                        change_amount: this.paid_amount - this.total,
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
