<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <title>Registro</title>
</head>
<body style="background-color: beige;">

<div style="background-color: white; margin-top: 0%;">
    <nav align="center" class="navbar navbar-light bg-light">
        <span class="navbar-brand mb-0 h1">Laravel</span>
    </nav>
</div>
@if(session('mensaje'))
<div class="alert alert-success">
    <p>{{session('mensaje')}}</p>
</div>
@elseif(session('error'))
<div class="alert alert-danger">
    <p>{{session('error')}}</p>
</div>
@endif


<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Registro</div>
                <div class="card-body">
                    <form id="miFormulario" method="POST" action="{{ route('registroS') }}">
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
                            <label for="name">Nombre</label>
                            <input type="text" name="name" id="name" class="form-control" required>
                        </div>
                        @error('name')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                          @enderror

                        <div class="form-group">
                            <label for="email">Correo Electrónico</label>
                            <input type="email" name="email" id="email" class="form-control" required>
                            @error('email')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
                        </div>


                        <div class="form-group">
                            <label for="phone">Telefono</label>
                            <input type="tel" name="phone" id="phone" class="form-control" required>
                            @error('phone')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
                        </div>




                        




                        <div class="form-group">
                            <label for="password">Contraseña</label>
                            <input type="password" name="password" id="password" class="form-control" required>
                            @error('password')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
                        </div>

                        <div class="form-group">
                            <label for="password_confirmation">Confirmar Contraseña</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required>
                        </div>

                        <!-- <button type="submit" class="btn btn-primary">Enviar Datos</button> -->
                        <button class="g-recaptcha btn btn-primary" 
        data-sitekey="{{ env('RECAPTCHA_SITE_KEY') }}" 
        data-callback='onSubmit' 
        data-action='submit' type="submit">Enviar Datos</button>

        
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
<script>
   function onSubmit(token) {
     document.getElementById("miFormulario").submit();
   }
 </script>
</body>
</html>
