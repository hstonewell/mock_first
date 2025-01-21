@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendance.css')}}">
@endsection

@section('content')
<div class="attendance-list">
    <form method="POST" action="{{ route('attendance.date') }}" class="attendance-date">
        @csrf
        <input type="hidden" name="displayDate" value="{{ $date }}">
        <button type="submit" class="attendance-date__btn" name="prevDate">
            &lt;</button>
        <h2 class="content__heading">{{ $date }}</h2>
        <button type="submit" class="attendance-date__btn" name="nextDate">&gt;</button>
    </form>
    <table class="attendance-list__inner">
        <tr class="attendance-list__row">
            <th class="attendance-list__header">名前</th>
            <th class="attendance-list__header">勤務開始</th>
            <th class="attendance-list__header">勤務終了</th>
            <th class="attendance-list__header" id="breaktime">休憩時間</th>
            <th class="attendance-list__header" id="totalhours">勤務時間</th>
        </tr>
        @foreach ($records as $record)
        <tr class="attendance-list__row">
            <td class="attendance-list__item">
                <a href="{{ route('attendance.user', ['user_id' => $record->user->id]) }}" class="attendance-list__username">
                    {{ $record->user->name }}
                </a>
            </td>
            <td class="attendance-list__item">{{ $record->clock_in }}</td>
            <td class="attendance-list__item">{{ $record->clock_out }}</td>
            <td class="attendance-list__item" id="breaktime">{{ $record->total_break }}</td>
            <td class="attendance-list__item" id="totalhours">{{ $record->actual_working }}</td>
        </tr>
        @endforeach
    </table>
    <div class="pagination">{{ $records->appends(['date' => $date])->links() }}</div>
</div>
@endsection