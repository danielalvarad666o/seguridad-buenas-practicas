<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <title>sms</title>
</head>
<body style="background-color: beige;">

<div style="background-color: white; margin-top: 0%;">
    <nav align="center" class="navbar navbar-light bg-light">
        <span class="navbar-brand mb-0 h1">Laravel</span>
    </nav>
</div>

@if(session('mensaje'))
<div class="alert alert-success">
    <p>{{ session('mensaje') }}</p>
</div>
@elseif(session('error'))
<div class="alert alert-danger">
    <p>{{ session('error') }}</p>
</div>
@endif

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Codigo SMS</div>
                <div class="card-body">
                    <form id="miFormulario" method="POST" action="{{route('smsVerificacion') }}">
                        @csrf

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="form-group">
                            <label for="name1">Se envió un número al teléfono registrado</label>
                            <br>
                            <strong><label for="name">Código</label></strong>
                            <input type="text" name="code" id="code" class="form-control" inputmode="numeric" pattern="[0-9]*" required>
                        </div>

                        <div class="mt-3">
                            <strong><span id="countdown">1:00</span></strong>
                            <button id="resendButton" class="btn btn-warning" onclick="resendCode()" disabled>Enviar código de nuevo</button>
                        </div>
                        <br>

                        <!-- Botón para enviar datos -->
                        <button id="btnenviar" class="g-recaptcha btn btn-primary"
                                data-sitekey="{{ env('RECAPTCHA_SITE_KEY') }}"
                                data-callback='onSubmit'
                                data-action='submit' type="submit">Enviar Datos</button>

                        <!-- Temporizador de 1 minuto y botón de enviar de nuevo -->
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://www.google.com/recaptcha/api.js"></script>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="{{ asset('js/recaptcha.js') }}"></script>
@php
    $variablephp = request()->route('userId');
@endphp

<script>
    var IdSmsNew = "{{ $variablephp }}";
</script>
<script src="{{ asset('js/sms.js') }}"></script>

</body>
</html>