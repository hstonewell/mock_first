@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/auth/register.css')}}">
@endsection

@section('content')
<div class="register-form__inner">
    <h2 class="content__heading">登録完了</h2>
    <div class="register-form__form">
        <p>ご登録ありがとうございました。</p>
        <button class="register-form__btn btn" type="submit" value="出退勤ページ">出退勤ページ</button>
    </div>
    <div class="register-form__footer">
        <form method="POST" action="{{ route('logout') }}">
            <input type="hidden" id="logout" value="別のユーザでログインする">
            <p>別のユーザでログインする</p>
        </form>
    </div>
</div>

@endsection