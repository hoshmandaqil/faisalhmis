<header class="header">
    <!-- Row start -->
    <div class="row gutters">
        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-6">
            <img src="{{ asset('assets/img/logo/logo.png') }}" alt="" style="height: 50px" class="mb-4">
        </div>
        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-6">
            <!-- Header actions start -->
            <ul class="header-actions d-print-none">
                <li class="dropdown">
                    <a href="#" id="userSettings" class="user-settings" data-toggle="dropdown"
                        aria-haspopup="true">
                        <span class="avatar">{{ ucfirst(get_avatar(ucfirst(Auth::user()->name))) }}<span
                                class="status busy"></span></span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userSettings">
                        <div class="header-profile-actions">
                            <div class="header-user-profile">
                                <div class="header-user">
                                    {{--                                    <img src="img/user.png" alt="Reatil Admin" /> --}}
                                </div>
                                <h5>{{ ucfirst(Auth::user()->name) }}</h5>
                                <p>{{ Auth::user()->email }}</p>
                                <p>{{ Auth::user()->phone }}</p>
                            </div>

                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                            <a href="{{ url('change_password') }}"<i class="icon-settings"></i> Change Password</a>
                            <a href="{{ route('logout') }}"
                                onclick="event.preventDefault();
                            document.getElementById('logout-form').submit();"><i
                                    class="icon-log-out1"></i> Log Out</a>
                        </div>
                    </div>
                </li>

            </ul>
            <!-- Header actions end -->
        </div>
    </div>
    <!-- Row end -->
</header>
