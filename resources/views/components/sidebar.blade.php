<div class="sidebar-wrapper" data-simplebar="true">
    <div class="sidebar-header">
        <div>
            <img src="{{ asset('assets/images/gtc-logo.png') }}" class="logo-icon" alt="logo icon" style="height: 50px">
        </div>
        <div>
            <h4 class="logo-text">Sistem Kasir</h4>
        </div>
        <div class="toggle-icon ms-auto"><i class='bx bx-first-page'></i>
        </div>
    </div>
    <!--navigation-->
    <ul class="metismenu" id="menu">
        <li>
            <a href="{{ route('dashboard') }}" class="">
                <div class="parent-icon"><i class='bx bx-home'></i>
                </div>
                <div class="menu-title">Dashboard</div>
            </a>
        </li>
        <li>
            <a href="{{ route('cashier.index') }}" class="">
                <div class="parent-icon"><i class='bx bx-cart-alt'></i>
                </div>
                <div class="menu-title">Kasir</div>
            </a>
        </li>
        <li>
            <a href="{{ route('restock.index') }}" class="">
                <div class="parent-icon"><i class='bx bx-cart-alt'></i>
                </div>
                <div class="menu-title">Restock Barang</div>
            </a>
        </li>

        <li>
            <a href="javascript:;" class="has-arrow">
                <div class="parent-icon"><i class='bx bx-cart-alt'></i>
                </div>
                <div class="menu-title">Laporan</div>
            </a>
            <ul>
                <li> 
                    <a href="{{ route('report.sales') }}">
                        <i class="bx bx-right-arrow-alt"></i>Nota Penjualan
                    </a>
                </li>
                <li> 
                    <a href="{{ route('report.saleItems') }}">
                        <i class="bx bx-right-arrow-alt"></i>Penjualan Barang
                    </a>
                </li>
                <li> 
                    <a href="{{ route('report.purchases') }}">
                        <i class="bx bx-right-arrow-alt"></i>Nota Pembelian
                    </a>
                </li>
                <li> 
                    <a href="{{ route('report.purchaseItems') }}">
                        <i class="bx bx-right-arrow-alt"></i>Pembelian Barang
                    </a>
                </li>
            </ul>
        </li>
        <li>
            <a href="javascript:;" class="has-arrow">
                <div class="parent-icon"><i class='bx bx-cart-alt'></i>
                </div>
                <div class="menu-title">Data Master</div>
            </a>
            <ul>
                <li> <a href="{{ route('products.index') }}"><i class="bx bx-right-arrow-alt"></i>Barang</a>
                </li>
                <li> <a href="{{ route('categories.index') }}"><i class="bx bx-right-arrow-alt"></i>Kategori</a>
                </li>
                <li> <a href="{{ route('suppliers.index') }}"><i class="bx bx-right-arrow-alt"></i>Supplier</a>
                </li>
            </ul>
        </li>
        <li>
            <a href="{{ route('users.index') }}" class="">
                <div class="parent-icon"><i class='bx bx-users'></i>
                </div>
                <div class="menu-title">Data User</div>
            </a>
        </li>
    </ul>
    <!--end navigation-->
</div>
