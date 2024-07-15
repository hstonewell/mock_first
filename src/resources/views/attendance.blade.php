@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendance.css')}}">
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
<div class="attendance-list">
    <h2 class="content__heading">{{ $date }}</h2>
    <table class="attendance-list__inner">
        <tr class="attendance-list__row">
            <th class="attendance-list__header">名前</th>
            <th class="attendance-list__header">勤務開始</th>
            <th class="attendance-list__header">勤務終了</th>
            <th class="attendance-list__header">休憩時間</th>
            <th class="attendance-list__header">勤務時間</th>
        </tr>
        @foreach ($records as $record)
        <tr class="attendance-list__row">
            <td class="attendance-list__item">{{ $record->user->name }}</td>
            <td class="attendance-list__item">{{ $record->clock_in }}</td>
            <td class="attendance-list__item">{{ $record->clock_out }}</td>
            <td class="attendance-list__item">{{ $record->total_break }}</td>
            <td class="attendance-list__item">{{ $record->actual_working }}</td>
        </tr>
        @endforeach
    </table>
    <div class="attendance__pagination">{{ $records->links() }}</div>
</div>
@endsection