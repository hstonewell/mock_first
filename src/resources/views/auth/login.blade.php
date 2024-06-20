@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/auth/login.css')}}">
@endsection

@section('content')
<div class="login-form__inner">
    <h2 class="content__heading">ログイン</h2>
    <div class="login-form__form">
        <form class="login-form" method="post" action="/login">
            @csrf
            <div class="login-form__item">
                <input class="login-form__input" type="text" name="email" id="email" placeholder="メールアドレス" value="{{ old('email') }}">
            </div>
            @if ($errors->has('email'))
            @foreach($errors->get('email') as $message)
            <p class="login-form__error-message">
                {{ $message }}
            </p>
            @endforeach
            @endif
            <div class="login-form__item">
                <input class="login-form__input" type="password" name="password" id="password" placeholder="パスワード">
            </div>
            @if ($errors->has('password'))
            @foreach($errors->get('password') as $message)
            <p class="login-form__error-message">
                {{ $message }}
            </p>
            @endforeach
            @endif
            <input class="login-form__btn btn" type="submit" value="ログイン">
        </form>
    </div>
    <div class="login-form__footer">
        <p>アカウントをお持ちでない方はこちら</p>
        <p><a href="/register">会員登録</a></p>
    </div>
</div>

@endsection