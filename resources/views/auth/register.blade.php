<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Register</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Poppins', sans-serif;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      background: #f4f4f4;
    }

    .register-box {
      background-color: #fff;
      padding: 40px;
      border-radius: 10px;
      box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
      width: 100%;
      max-width: 400px;
      transition: all 0.3s ease;
    }

    .register-box:hover {
      transform: translateY(-5px);
      box-shadow: 0 12px 35px rgba(0, 0, 0, 0.2);
    }

    h2 {
      text-align: center;
      margin-bottom: 30px;
      color: #333;
      font-weight: 400;
      font-size: 24px;
    }

    .input-box {
      position: relative;
      margin: 25px 0;
    }

    .input-box input {
      width: 100%;
      padding: 15px 10px;
      background: none;
      border: 1px solid #ccc;
      border-radius: 5px;
      outline: none;
      color: #333;
      font-size: 14px;
      transition: border-color 0.3s ease;
    }

    .input-box input:focus {
      border-color: #1abc9c;
    }

    .input-box label {
      position: absolute;
      top: 50%;
      left: 12px; /* Sesuaikan agar label berada dalam input */
      transform: translateY(-50%);
      color: #888;
      font-size: 14px;
      pointer-events: none;
      transition: all 0.3s ease;
      background-color: #fff;
      padding: 0 5px;
    }

    .input-box input:focus ~ label,
    .input-box input:valid ~ label {
      top: -2px;
      left: 3px;
      font-size: 12px;
      color: #1abc9c;
      padding-left: 5px;
      padding-right: 5px;
    }

    .input-box input.invalid {
      border-color: red;
    }

    .input-box input.valid {
      border-color: #1abc9c;
    }

    button.btn {
      width: 100%;
      background: #1abc9c;
      color: #fff;
      padding: 15px;
      border: none;
      cursor: pointer;
      font-size: 16px;
      border-radius: 5px;
      transition: background 0.3s;
      margin-top: 10px;
    }

    button.btn:hover {
      background: #16a085;
    }

    .back-btn {
      display: block;
      text-align: center;
      margin-top: 20px;
      color: #888;
      text-decoration: none;
      font-size: 12px;
    }

    .back-btn:hover {
      text-decoration: underline;
    }

    p {
      color: #888;
      text-align: center;
      margin-top: 20px;
      font-size: 12px;
    }

    p a {
      color: #1abc9c;
      text-decoration: underline;
      font-weight: 300;
    }

    /* Media Query yang Diperbarui untuk Maksimal 768px */
    @media (max-width: 768px) {
      .register-box {
        padding: 30px;
        max-width: 350px; /* Tingkatkan dari 320px menjadi 350px */
      }
      h2 {
        font-size: 22px;
      }
    }

    /* Media Query Tambahan untuk Layar Lebih Kecil (480px) */
    @media (max-width: 480px) {
      .register-box {
        padding: 30px;
        width: 90%; /* Mengisi 90% dari lebar layar */
      }
      h2 {
        font-size: 20px;
      }
      .input-box input {
        padding: 12px 8px;
      }
      button.btn {
        padding: 12px;
        font-size: 14px;
      }
    }
  </style>
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300&display=swap" rel="stylesheet">
  <script>
    function validatePassword() {
      const password = document.getElementById("password");
      const confirmPassword = document.getElementById("confirmPassword");

      if (password.value !== confirmPassword.value) {
        confirmPassword.classList.add("invalid");
        confirmPassword.classList.remove("valid");
      } else {
        confirmPassword.classList.add("valid");
        confirmPassword.classList.remove("invalid");
      }
    }
  </script>
</head>
<body>
  <div class="register-box">
    <h2>Registrasi</h2>
    <form action="{{ route('register') }}" method="POST">
      @csrf
      <div class="input-box">
        <input type="text" name="nama" id="nama" required  value="{{ old('nama') }}">
        <label>Nama</label>
      </div>
      <div class="input-box">
        <input type="text" name="username" id="username" required value="{{ old('username') }}">
        <label>Username</label>
        @if ($errors->has('username'))
        <span style="color: red; font-size: 12px;">{{ $errors->first('username') }}</span>
        @endif
      </div>
      <div class="input-box">
        <input type="password" name="password" id="password" required oninput="validatePassword()">
        <label>Password</label>
      </div>
      <div class="input-box">
        <input type="password" name="password_confirmation" id="confirmPassword" required oninput="validatePassword()">
        <label>Konfirmasi Password</label>
      </div>    
      <button type="submit" class="btn">Registrasi</button>
    </form>
    <p>Sudah punya akun? <a href="/login">Login</a></p>
    {{-- <a href="/" class="back-btn">Kembali ke Beranda</a> --}}
  </div>
</body>
</html>
