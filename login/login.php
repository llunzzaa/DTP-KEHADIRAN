
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['id_user'])) {
    header('Location: ../dashboard.php');
    exit;
}

// Membuat token keamanan jika belum tersedia
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login | HADIRIN</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: #f1f5f9;
            padding: 20px;
        }

        .login-container {
            width: 100%;
            max-width: 400px;
            background: #ffffff;
            padding: 40px 35px;
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 8px 25px rgba(15, 23, 42, 0.07);
        }

        .logo {
            width: 65px;
            height: 65px;
            background: #e8eef7;
            color: #334e75;
            border-radius: 16px;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 28px;
            font-weight: bold;
            margin: 0 auto 18px;
        }

        h2 {
            text-align: center;
            color: #26364d;
            margin-bottom: 8px;
            font-size: 23px;
        }

        .subtitle {
            text-align: center;
            color: #8491a3;
            font-size: 14px;
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #475569;
            font-weight: bold;
            font-size: 14px;
        }

        input {
            width: 100%;
            padding: 13px 15px;
            border: 1px solid #dbe2ea;
            border-radius: 9px;
            font-size: 15px;
            outline: none;
            background: #fafbfc;
            transition: 0.2s;
        }

        input:focus {
            border-color: #7186a5;
            background: #ffffff;
            box-shadow: 0 0 0 3px rgba(64, 87, 119, 0.08);
        }

        button {
            width: 100%;
            padding: 14px;
            background: #405777;
            color: white;
            border: none;
            border-radius: 9px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 5px;
            transition: 0.2s;
        }

        button:hover {
            background: #304461;
        }

        .error {
            background: #fef2f2;
            color: #b45353;
            padding: 12px;
            border-radius: 8px;
            font-size: 13px;
            margin-bottom: 18px;
            text-align: center;
        }

        .footer {
            text-align: center;
            margin-top: 25px;
            color: #a0aab8;
            font-size: 12px;
        }

        @media (max-width: 480px) {
            .login-container {
                padding: 30px 22px;
            }
        }
    </style>
</head>

<body>

    <div class="login-container">

        <div class="logo">H</div>

        <h2>LOGIN HADIRIN</h2>

        <p class="subtitle">
            Sistem Informasi Absensi Siswa
        </p>

        <?php if (isset($_GET['error'])): ?>
            <div class="error">
                Username atau password salah!
            </div>
        <?php endif; ?>

        <form action="proses_login.php" method="POST">

            <input
                type="hidden"
                name="csrf_token"
                value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>"
            >

            <div class="form-group">
                <label for="username">Username</label>

                <input
                    type="text"
                    id="username"
                    name="username"
                    placeholder="Masukkan username"
                    autocomplete="username"
                    required
                >
            </div>

            <div class="form-group">
                <label for="password">Password</label>

                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Masukkan password"
                    autocomplete="current-password"
                    required
                >
            </div>

            <button type="submit">
                Login
            </button>

        </form>

        <div class="footer">
            © 2026 HADIRIN - Sistem Absensi Siswa
        </div>

    </div>

</body>
</html>