<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login</title>
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">

    <link href="https://fonts.googleapis.com/css?family=Nunito:300,400,400i,600,700,800,900" rel="stylesheet">
    <link href="{{ asset('assets/fonts/fontawesome/css/all.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/themes/lite-purple.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/plugins/sweetalert2.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/customize/custom.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/customize/responsive.css') }}" rel="stylesheet">
    <script src='https://www.google.com/recaptcha/api.js'></script>
    <style>
        .auth-logo img {
            width: 150px;
            height: 75px;
            object-fit: contain;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <div class="auth-layout-wrap" style="background-image: url({{ asset('assets/images/background-simnotaris.jpg') }})">
        <div class="auth-content">

            <div class="card o-hidden">
                <div class="row">
                    <div class="col-md-12">
                        <div class="p-4">
                            <div class="auth-logo text-center mb-3">
                                <img src="{{ asset('assets/images/logo-simnotaris.png') }}" alt="" srcset="">
                                <h2 class="font-weight-bold">SIM Notaris PPAT</h2>
                            </div>
                            <div class="tab-content">
                                <div class="tab-pane active" id="tab-sign-in">
                                    <h1 class="mb-3 text-22">Sign In</h1>

                                    <form class="mb-3" action="{{ url('/auth') }}" method="post" id="form-data">
                                        <div class="form-group">
                                            <label for="username">Username</label>
                                            <input class="form-control form-control-rounded" id="username"
                                                name="username" type="text" placeholder="Username"
                                                class="form-control">
                                            <small class="message-error text-danger"
                                                data-target="username_error"></small>
                                        </div>
                                        <div class="form-group">
                                            <label for="password">Password</label>
                                            <input class="form-control form-control-rounded" id="password"
                                                type="password" name="password" placeholder="Password"
                                                class="form-control">
                                            <small class="message-error text-danger"
                                                data-target="password_error"></small>
                                        </div>
                                        <div class="g-recaptcha" data-sitekey="{{ env('G_CAPTCHA_SITE_KEY') }}"></div>

                                        <!-- Sign In -->
                                        <div class="mt-3">
                                            <button type="submit" class="btn btn-rounded btn-primary col-md-6">
                                                <i class="fas fa-sign-in-alt mr-2"></i> Sign In
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="text-center mt-3 mb-2">
                            <p class="m-0">&copy;2024 Copyright Blitaris Tekno</p>
                            <p class="m-0 font-weight-bold">All rights reserved</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/js/plugins/jquery-3.3.1.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/loadingoverlay.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('assets/js/scripts/script.min.js') }}"></script>
    <script src="{{ asset('assets/scripts/utils/script.js') }}"></script>
    <script>
        const baseUrl = (url) => {
            return `{{ url('/') }}${url}`;
        }
    </script>
    <script>
        $('#form-data').submit(function(e) {
            e.preventDefault();

            let data = new FormData(this);

            $("body").LoadingOverlay("show");

            $.httpRequest({
                url: this.action,
                method: this.method,
                data: data,
                response: (res) => {
                    $("body").LoadingOverlay("hide");
                    $(".message-error").empty();

                    switch (res.statusCode) {
                        case 200:
                            window.location.replace(baseUrl('/administrator/dashboard'));
                            break;
                        case 400:
                            let error = res.data.error;
                            let index = Object.keys(error);
                            index.forEach((val) => {
                                $(`small[data-target="${val}_error"]`).text(error[val]);
                            });
                            break;
                        case 403:
                            swal("Maaf !", res.message, "error");
                            break;
                        case 404:
                            swal("Maaf !", res.message, "error");
                            break;
                        case 500:
                            swal("Maaf !", res.message, "error");
                            break;
                        default:
                            // code
                            break;
                    }
                },
            });
        });
    </script>
</body>

</html>
