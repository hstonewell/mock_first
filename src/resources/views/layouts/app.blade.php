<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atte</title>
    <link rel="stylesheet" href="https://unpkg.com/ress/dist/ress.min.css" />
    <link rel="stylesheet" href="{{ asset('css/common.css')}}">
    @yield('css')
</head>

<body>
    <div class="app">
        <header class="header">
            <h1 class="header__heading">Atte</h1>
            @auth
            <div class="header__link">
                <a href="{{ route('index') }}" class="header__link--item">ホーム</a>
                <a href="{{ route('attendance.user') }}" class="header__link--item" {{ request()->routeIs('attendance.user') ? 'disabled' : '' }}>{{ Auth::user()->name }}さんの勤怠一覧</a>
                <a href="{{ route('attendance.view') }}" class="header__link--item" {{ request()->routeIs('attendance.view') ? 'disabled' : '' }}>日付一覧</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="header__link--item" class="header__link--item">ログアウト</button>
                </form>
            </div>
            @endauth
        </header>

        @if(session('message'))
        <div class="attendance__alert--success">{{ session('message') }}</div>
        @endif
        @if ($errors->any())
        <div class="attendance__alert--error">{{ $errors->first('msg') }}</div>
        @endif

        <div class="content">
            @yield('content')
        </div>
        <footer class="footer">
            <small>Atte, inc.</small>
        </footer>
    </div>
</body>

</html>