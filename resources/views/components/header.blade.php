<header>
    <div class="topbar d-flex align-items-center">
        <nav class="gap-3 navbar navbar-expand">
            <div class="mobile-toggle-menu"><i class='bx bx-menu'></i>
            </div>
            <div class="top-menu ms-auto">
                <ul class="gap-1 navbar-nav align-items-center">
                    <li class="nav-item dark-mode d-none d-sm-flex">
                        <a class="nav-link dark-mode-icon" href="javascript:;"><i class='bx bx-moon'></i>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="px-3 user-box dropdown">
                <a class="d-flex align-items-center nav-link dropdown-toggle dropdown-toggle-nocaret" href="#"
                    role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="{{ asset('assets/images/avatars/user.png') }}" class="user-img" alt="user avatar">
                    <div class="user-info ps-3">
                        <p class="mb-0 user-name">{{ $user->name }}</p>
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item" href="{{ route('profile.edit') }}">
                            <i class="bx bx-user"></i><span>Pengaturan Akun</span>
                        </a>
                    </li>
                    <li>

                        <a class="dropdown-item" href="javascript:;"
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">

                            <i class='bx bx-log-out-circle'></i>
                            <span>Logout</span>
                        </a>
                        @auth
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        @endauth
                    </li>
                </ul>
            </div>
        </nav>
    </div>
</header>
