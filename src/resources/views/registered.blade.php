<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atte</title>
    <link rel="stylesheet" href="https://unpkg.com/ress/dist/ress.min.css" />
    <link rel="stylesheet" href="{{ asset('css/common.css')}}">
    <link rel="stylesheet" href="{{ asset('css/auth/register.css')}}">
</head>

<body>
    <div class="app">
        <header class="header">
            <div class="header__inner">
                <h1 class="header__heading">Atte</h1>
            </div>
        </header>
        <div class="content">
            <div class="register-form__inner">
                <h2 class="content__heading">登録完了</h2>
                <div class="registered">
                    <p>ご登録ありがとうございました。</p>
                    <form action="{{ route('index') }}" method="GET">
                        @csrf
                        <button class="registered__btn" type="submit" value="出退勤ページ">打刻ページへ</button>
                    </form>
                </div>
            </div>
        </div>
        <footer class="footer">
            <small>Atte, inc.</small>
        </footer>
    </div>
</body>

</html>