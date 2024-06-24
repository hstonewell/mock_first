@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/index.css')}}">
@endsection

@section('link')
<div class="header__link">
    <a href="/" class="header__link--item">ホーム</a>
    <a href="/" class="header__link--item">日付一覧</a>
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="header__link--item" class="header__link--item">ログアウト</button>
    </form>
</div>
@endsection

@section('content')
<div class="content__inner">
    <h2 class="content__heading">さんお疲れ様です！</h2>
    <div class="attendance__unit">
        <form action="/clockin" class="attendance__item">
            @csrf
            <button class="attendance__submit">
                <h2 class="attendance__item--active">勤務開始</h2>
            </button>
        </form>
        <form action="/clockOut" class="attendance__item">
            @csrf
            <button class="attendance__submit">
                <h2 class="attendance__item--active">勤務終了</h2>
            </button>
        </form>
        <form action="/" class="attendance__item">
            @csrf
            <button class="attendance__submit">
                <h2 class="attendance__item--active">休憩開始</h2>
            </button>
        </form>
        <form action="/" class="attendance__item">
            @csrf
            <button class="attendance__submit">
                <h2 class="attendance__item--active">休憩終了</h2>
            </button>
        </form>
    </div>
</div>
@endsection