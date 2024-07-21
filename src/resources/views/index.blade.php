@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/index.css')}}">
@endsection

@section('link')
<div class="header__link">
    <a href="/index" class="header__link--item">ホーム</a>
    <a href="/attendance" class="header__link--item">日付一覧</a>
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="header__link--item" class="header__link--item">ログアウト</button>
    </form>
</div>
@endsection

@section('content')
<div class="content__inner">
    @if(session('message'))
    <div class="attendance__alert--success">{{ session('message') }}</div>
    @endif
    @if ($errors->any())
    <div class="attendance__alert--error">{{ $errors->first('msg') }}</div>
    @endif

    <h2 class="content__heading">{{ Auth::user()->name }}さんお疲れ様です！</h2>

    <div class="attendance__unit">
        <form action="/clockin" method="POST" class="attendance__item">
            @csrf
            <button class="attendance__submit" {{ isset($attendance) ? 'disabled' : '' }}>
                <h2 class="attendance__item--active">勤務開始</h2>
            </button>
        </form>

        <form action="/clockout" method="POST" class="attendance__item">
            @csrf
            <button class="attendance__submit" {{ isset($attendance) && is_null($attendance->clock_out) ? '' : 'disabled' }}>
                <h2 class="attendance__item--active">勤務終了</h2>
            </button>
        </form>

        <form action="/breakstart" method="POST" class="attendance__item">
            @csrf
            <button class="attendance__submit" {{ isset($attendance) && is_null($attendance->clock_out) && (!isset($latestBreak) || !is_null($latestBreak->break_end)) ? '' : 'disabled' }}>
                <h2 class="attendance__item--active">休憩開始</h2>
            </button>
        </form>

        <form action="/breakend" method="POST" class="attendance__item">
            @csrf
            <button class="attendance__submit" {{ isset($latestBreak) && is_null($latestBreak->break_end) ? '' : 'disabled' }}>
                <h2 class="attendance__item--active">休憩終了</h2>
            </button>
        </form>
    </div>
</div>
@endsection