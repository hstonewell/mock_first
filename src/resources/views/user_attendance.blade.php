@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendance.css')}}">
@endsection

@section('content')
<div class="attendance-list">
    <h2 class="content__heading">{{ $user->name }}さんの勤怠記録</h2>
    </form>
    <table class="attendance-list__inner">
        <tr class="attendance-list__row">
            <th class="attendance-list__header">日付</th>
            <th class="attendance-list__header">勤務開始</th>
            <th class="attendance-list__header">勤務終了</th>
            <th class="attendance-list__header" id="breaktime">休憩時間</th>
            <th class="attendance-list__header" id="totalhours">勤務時間</th>
        </tr>
        @foreach ($records as $record)
        <tr class="attendance-list__row">
            <td class="attendance-list__item">{{ $record->date }}</td>
            <td class="attendance-list__item">{{ $record->clock_in }}</td>
            <td class="attendance-list__item">{{ $record->clock_out }}</td>
            <td class="attendance-list__item" id="breaktime">{{ $record->total_break }}</td>
            <td class="attendance-list__item" id="totalhours">{{ $record->actual_working }}</td>
        </tr>
        @endforeach
    </table>
    <div class="pagination">{{ $records->links() }}</div>
</div>
@endsection