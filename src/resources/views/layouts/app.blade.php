<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atte</title>
    <link rel="stylesheet" href="https://unpkg.com/ress/dist/ress.min.css" />
    <link rel="stylesheet" href="{{ asset('css/common.css')}}">
    <script src="https://kit.fontawesome.com/88521f16f4.js" crossorigin="anonymous"></script>
    @yield('css')
</head>

<body>
    <div class="app">
        <header class="header">
            <div class="header__inner">
                <h1 class="header__heading">Atte</h1>
                @auth
                @if(Auth::user()->email_verified_at)
                <div class="header__link">
                    <a href="{{ route('index') }}" class="header__link--item">ホーム</a>
                    <a href="{{ route('attendance.user') }}" class="header__link--item" {{ request()->routeIs('attendance.user') ? 'disabled' : '' }}>{{ Auth::user()->name }}さんの勤怠一覧</a>
                    <a href="{{ route('attendance.view') }}" class="header__link--item" {{ request()->routeIs('attendance.view') ? 'disabled' : '' }}>日付一覧</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="header__link--item" class="header__link--item">ログアウト</button>
                    </form>
                </div>
                <div class="header__link--buttons" hidden>
                    <a href="{{ route('index') }}" class="header__link--btn" alt="ホーム"><i class="fa-solid fa-house fa-3x" style="color: #000;"></i></a>
                    <a href="{{ route('attendance.user') }}" class="header__link--btn" {{ request()->routeIs('attendance.user') ? 'disabled' : '' }} alt="勤怠一覧"><i class="fa-solid fa-user fa-3x" style="color: #000;"></i></a>
                    <a href="{{ route('attendance.view') }}" class="header__link--btn" {{ request()->routeIs('attendance.view') ? 'disabled' : '' }} alt="日付一覧"><i class="fa-solid fa-calendar-days fa-3x" style="color: #000;"></i></a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="header__link--item" class="header__link--btn" alt="ログアウト"><i class="fa-solid fa-right-from-bracket fa-3x" style="color: #000;"></i></button>
                    </form>
                </div>
                @endif
                @endauth
            </div>
        </header>

        <div class="content">
            @if(session('message'))
            <div class="attendance__alert--success">{{ session('message') }}</div>
            @endif
            @if ($errors->any())
            <div class="attendance__alert--error">{{ $errors->first('msg') }}</div>
            @endif
            @yield('messages')
            @yield('content')
        </div>
        <footer class="footer">
            <small>Atte, inc.</small>
        </footer>
    </div>
</body>

</html>