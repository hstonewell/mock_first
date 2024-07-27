@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendance.css')}}">
@endsection

@section('link')
<div class="header__link">
    <a href="/index" class="header__link--item">ホーム</a>
    <a href="/attendance" class="header__link--item" disabled>日付一覧</a>
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="button" class="header__link--item">ログアウト</button>
    </form>
</div>
@endsection

@section('content')
<div class="attendance-list">
    <h2 class="content__heading">{{ Auth::user()->name }}さんの勤怠記録</h2>
    </form>
    <table class="attendance-list__inner">
        <tr class="attendance-list__row">
            <th class="attendance-list__header">日付</th>
            <th class="attendance-list__header">勤務開始</th>
            <th class="attendance-list__header">勤務終了</th>
            <th class="attendance-list__header">休憩時間</th>
            <th class="attendance-list__header">勤務時間</th>
        </tr>
        @foreach ($records as $record)
        <tr class="attendance-list__row">
            <td class="attendance-list__item">{{ $record->date }}</td>
            <td class="attendance-list__item">{{ $record->clock_in }}</td>
            <td class="attendance-list__item">{{ $record->clock_out }}</td>
            <td class="attendance-list__item">{{ $record->total_break }}</td>
            <td class="attendance-list__item">{{ $record->actual_working }}</td>
        </tr>
        @endforeach
    </table>
    <div class="pagination">{{ $records->links() }}</div>
</div>
@endsection