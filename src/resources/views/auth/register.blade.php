@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/auth/register.css')}}">
@endsection

@section('content')
<div class="register-form__inner">
    <h2 class="content__heading">会員登録</h2>
    <div class="register-form__form">
        <form class="register-form" method="post" action="/register">
            @csrf
            <div class="register-form__item">
                <input class="register-form__input" type="text" name="name" id="name" placeholder="名前" value="{{ old('name') }}">
            </div>
            @if ($errors->has('name'))
            @foreach($errors->get('name') as $message)
            <p class="register-form__error-message">
                {{ $message }}
            </p>
            @endforeach
            @endif

            <div class="register-form__item">
                <input class="register-form__input" type="text" name="email" id="email" placeholder="メールアドレス" value="{{ old('email') }}">
            </div>
            @if ($errors->has('email'))
            @foreach($errors->get('email') as $message)
            <p class="register-form__error-message">
                {{ $message }}
            </p>
            @endforeach
            @endif
            <div class="register-form__item">
                <input class="register-form__input" type="password" name="password" id="password" placeholder="パスワード" value="{{ old('password') }}">
            </div>
            @if ($errors->has('password'))
            @foreach($errors->get('password') as $message)
            <p class="register-form__error-message">
                {{ $message }}
            </p>
            @endforeach
            @endif
            <div class="register-form__item">
                <input class="register-form__input" type="password" name="password_confirmation" id="password_confirmation" placeholder="確認用パスワード" value="{{ old('password_confirmation') }}">
            </div>
            @if ($errors->has('password_confirmation'))
            @foreach($errors->get('password_confirmation') as $message)
            <p class="register-form__error-message">
                {{ $message }}
            </p>
            @endforeach
            @endif
            <input class="register-form__btn btn" type="submit" value="会員登録">
        </form>
    </div>
    <div class="register-form__footer">
        <p>アカウントをお持ちの方はこちらから</p>
        <p><a href="/login">ログイン</a></p>
    </div>
</div>

@endsection