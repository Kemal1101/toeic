<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>TOEIC Login Page</title>
    <!--begin::Primary Meta Tags-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="title" content="TOEIC | Login Page" />
    <meta name="author" content="ColorlibHQ" />
    <meta
      name="description"
      content="AdminLTE is a Free Bootstrap 5 Admin Dashboard, 30 example pages using Vanilla JS."
    />
    <meta
      name="keywords"
      content="bootstrap 5, bootstrap, bootstrap 5 admin dashboard, bootstrap 5 dashboard, bootstrap 5 charts, bootstrap 5 calendar, bootstrap 5 datepicker, bootstrap 5 tables, bootstrap 5 datatable, vanilla js datatable, colorlibhq, colorlibhq dashboard, colorlibhq admin dashboard"
    />
    <!--end::Primary Meta Tags-->
    <!--begin::Fonts-->
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css"
      integrity="sha256-tXJfXfp6Ewt1ilPzLDtQnJV4hclT9XuaZUKyUvmyr+Q="
      crossorigin="anonymous"
    />
    <!--end::Fonts-->
    <!--begin::Third Party Plugin(OverlayScrollbars)-->
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.10.1/styles/overlayscrollbars.min.css"
      integrity="sha256-tZHrRjVqNSRyWg2wbppGnT833E/Ys0DHWGwT04GiqQg="
      crossorigin="anonymous"
    />
    <!--end::Third Party Plugin(OverlayScrollbars)-->
    <!--begin::Third Party Plugin(Bootstrap Icons)-->
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
      integrity="sha256-9kPW/n5nn53j4WMRYAxe9c1rCY96Oogo/MKSVdKzPmI="
      crossorigin="anonymous"
    />
    <!--end::Third Party Plugin(Bootstrap Icons)-->
    <!--begin::Required Plugin(AdminLTE)-->
    <link rel="stylesheet" href="{{asset('adminlte/dist/css/adminlte.css') }}" />
    <!--end::Required Plugin(AdminLTE)-->
    <style>
      body {
        background: radial-gradient(circle at center, #ffffff, #a3c4f3, #5a9bf6);
        height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
        font-family: 'Source Sans 3', sans-serif;
      }
      .login-container {
        display: flex;
        width: 720px;
        height: 400px;
        border-radius: 15px;
        box-shadow: 0 8px 24px rgba(0,0,0,0.15);
        overflow: hidden;
        background: white;
      }
      .login-left {
        flex: 1;
        background: linear-gradient(135deg, #6bb7ff, #2a6df4);
        color: white;
        padding: 40px 30px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        text-align: center;
        gap: 15px;
      }
      .login-left img {
        width: 120px;
        height: auto;
        border-radius: 10px;
        object-fit: contain;
      }
      .login-left h1 {
        font-weight: 900;
        font-size: 3rem;
        margin: 0;
        letter-spacing: 2px;
      }
      .login-left p {
        font-weight: 600;
        font-size: 1rem;
        margin-top: 10px;
      }
      .login-right {
        flex: 1;
        padding: 40px 30px;
        display: flex;
        flex-direction: column;
        justify-content: center;
      }
      .login-right form label {
        font-weight: 600;
        font-size: 1rem;
        margin-bottom: 6px;
      }
      .input-group-text {
        background-color: #f0f0f0;
        border-left: none;
        cursor: pointer;
      }
      .form-control {
        border-right: none;
        border-radius: 0.25rem 0 0 0.25rem;
      }
      .input-group .form-control:focus {
        box-shadow: none;
        border-color: #2a6df4;
      }
      .input-group {
        border: 1px solid #ccc;
        border-radius: 0.25rem;
        overflow: hidden;
      }
      .input-group .input-group-text {
        border: none;
        border-left: 1px solid #ccc;
        display: flex;
        justify-content: center;
        align-items: center;
        width: 45px;
        color: #555;
      }
      .forgot-password {
        font-size: 0.875rem;
        margin-top: -10px;
        margin-bottom: 20px;
        text-align: right;
      }
      .forgot-password a {
        color: #2a6df4;
        text-decoration: none;
      }
      .forgot-password a:hover {
        text-decoration: underline;
      }
      .btn-signin {
        background-color: #2a6df4;
        border: none;
        color: white;
        font-weight: 700;
        font-size: 1.25rem;
        padding: 12px 0;
        border-radius: 12px;
        width: 100%;
        box-shadow: 0 4px 12px rgba(42,109,244,0.5);
        transition: background-color 0.3s ease;
      }
      .btn-signin:hover {
        background-color: #1e4ecf;
      }
      .error-text {
        font-size: 0.875rem;
        color: #dc3545;
        margin-top: 4px;
        display: block;
      }
    </style>
  </head>
  <!--end::Head-->
  <!--begin::Body-->
  <body>
    <div class="login-container" role="main">
      <div class="login-left" aria-label="TOEIC branding and information">
        <img src="{{asset('logo.jpeg') }}" alt="JTI Logo" />
        <h1>SIPTOC</h1>
        <p>Sistem Pendaftaran TOEIC<br>Politeknik Negeri Malang</p>
      </div>
      <div class="login-right">
        <form action="{{ url('login') }}" method="POST" id="form-login" novalidate>
          <meta name="csrf-token" content="{{ csrf_token() }}">
          <div class="mb-4">
            <label for="username" class="form-label">Username</label>
            <div class="input-group">
              <input
                type="text"
                name="username"
                id="username"
                class="form-control"
                placeholder="Masukkan Username"
                aria-describedby="username-icon"
                required
                minlength="4"
                maxlength="20"
              />
              <span class="input-group-text" id="username-icon">
                <i class="bi bi-envelope"></i>
              </span>
            </div>
            <span id="error-username" class="error-text"></span>
          </div>
          <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <div class="input-group" id="password-group">
              <input
                type="password"
                name="password"
                id="password"
                class="form-control"
                placeholder="Masukkan Password"
                aria-describedby="password-icon"
                required
                minlength="6"
                maxlength="20"
              />
              <span class="input-group-text" id="password-icon" style="cursor:pointer;" title="Toggle Password Visibility">
                <i class="bi bi-eye-slash" id="toggle-password-icon"></i>
              </span>
            </div>
            <span id="error-password" class="error-text"></span>
          </div>
          <div class="forgot-password">
            <a href="forgot-password.html">Forgot password?</a>
          </div>
          <button type="submit" class="btn btn-signin">Sign In</button>
        </form>
      </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/additional-methods.min.js"></script>

    <!-- SweetAlert2 CSS (optional but recommended) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" />

    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
      // Password toggle show/hide
      document.getElementById('password-icon').addEventListener('click', function () {
        const passwordInput = document.getElementById('password');
        const icon = document.getElementById('toggle-password-icon');
        if (passwordInput.type === 'password') {
          passwordInput.type = 'text';
          icon.classList.remove('bi-eye-slash');
          icon.classList.add('bi-eye');
        } else {
          passwordInput.type = 'password';
          icon.classList.remove('bi-eye');
          icon.classList.add('bi-eye-slash');
        }
      });

      $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });

      $(document).ready(function () {
        $('#form-login').validate({
          rules: {
            username: { required: true, minlength: 4, maxlength: 20 },
            password: { required: true, minlength: 6, maxlength: 20 }
          },
          submitHandler: function (form) {
            $.ajax({
              url: form.action,
              type: form.method,
              data: $(form).serialize(),
              success: function (response) {
                if (response.status) {
                  Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: response.message
                  }).then(function () {
                    window.location = response.redirect;
                  });
                } else {
                  $('.error-text').text('');
                  $.each(response.msgField, function (prefix, val) {
                    $('#error-' + prefix).text(val[0]);
                  });
                  Swal.fire({
                    icon: 'error',
                    title: 'Terjadi Kesalahan',
                    text: response.message
                  });
                }
              }
            });
            return false;
          },
          errorElement: 'span',
          errorPlacement: function (error, element) {
            error.addClass('invalid-feedback');
            element.closest('.input-group').append(error);
          },
          highlight: function (element, errorClass, validClass) {
            $(element).addClass('is-invalid');
          },
          unhighlight: function (element, errorClass, validClass) {
            $(element).removeClass('is-invalid');
          }
        });
      });
    </script>
  </body>
  <!--end::Body-->
</html>
