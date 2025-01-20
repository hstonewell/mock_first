@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/auth/register.css')}}">
@endsection

@if (session('status') == 'verification-link-sent')
@section('messages')
<div class="attendance__alert--success">
    <p>ご登録いただいたメールアドレスに確認用のリンクをお送りしました。</p>
</div>
@endsection
@endif

@section('content')
<div class="verify__inner">
    <h2 class="content__heading">メールアドレスをご確認ください</h2>
    <div class="registered">
        <p>Atteをご利用いただくにはメール認証が必要です。</p>
        <p>もし確認用メールが送信されていない場合は、下記をクリックしてください。</p>
        <form action="{{ route('verification.send') }}" method="POST">
            @csrf
            <button class="registered__btn" type="submit">確認メールを再送信する</button>
        </form>
    </div>
</div>
@endsection