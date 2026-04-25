<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول</title>
    <style>
        body {
            font-family: Tahoma, Arial, sans-serif;
            background: #f3f4f6;
            margin: 0;
            padding: 0;
        }

        .login-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        .login-card {
            width: 100%;
            max-width: 420px;
            background: #fff;
            border-radius: 14px;
            padding: 28px;
            box-shadow: 0 10px 30px rgba(0,0,0,.08);
        }

        .login-card h1 {
            margin: 0 0 8px;
            font-size: 26px;
            text-align: center;
        }

        .login-card p {
            margin: 0 0 24px;
            text-align: center;
            color: #6b7280;
        }

        .form-group {
            margin-bottom: 16px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }

        input[type="email"],
        input[type="password"] {
            width: 100%;
            box-sizing: border-box;
            padding: 12px 14px;
            border: 1px solid #d1d5db;
            border-radius: 10px;
            font-size: 15px;
        }

        .remember {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 18px;
        }

        .btn {
            width: 100%;
            background: #2563eb;
            color: #fff;
            border: none;
            padding: 12px 16px;
            border-radius: 10px;
            cursor: pointer;
            font-size: 16px;
        }

        .btn:hover {
            opacity: .95;
        }

        .alert {
            padding: 12px 14px;
            border-radius: 10px;
            margin-bottom: 16px;
            font-size: 14px;
        }

        .alert-danger {
            background: #fee2e2;
            color: #991b1b;
        }

        .alert-success {
            background: #dcfce7;
            color: #166534;
        }

        .error-text {
            color: #b91c1c;
            font-size: 13px;
            margin-top: 6px;
        }
    </style>
</head>
<body>
    <div class="login-wrapper">
        <div class="login-card">
            <h1>تسجيل الدخول</h1>
            <p>أدخل بيانات حسابك للوصول إلى النظام</p>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul style="margin:0; padding-right:18px;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('login.attempt') }}">
                @csrf

                <div class="form-group">
                    <label for="email">الإيميل</label>
                    <input
                        id="email"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        autofocus
                    >
                </div>

                <div class="form-group">
                    <label for="password">كلمة المرور</label>
                    <input
                        id="password"
                        type="password"
                        name="password"
                        required
                    >
                </div>

                <div class="remember">
                    <input type="checkbox" name="remember" id="remember" value="1">
                    <label for="remember" style="margin:0; font-weight:normal;">تذكرني</label>
                </div>

                <button type="submit" class="btn">دخول</button>
            </form>
        </div>
    </div>
</body>
</html>