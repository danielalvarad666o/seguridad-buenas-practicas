// smsVerification.js

var countdown = 60; // 1 minuto en segundos

var countdownInterval = setInterval(function () {
    var minutes = Math.floor(countdown / 60);
    var seconds = countdown % 60;
    document.getElementById('countdown').innerHTML = minutes + ":" + (seconds < 10 ? "0" : "") + seconds;

    // Deshabilitar el botón y el input al llegar a 0
    if (countdown <= 0) {
        clearInterval(countdownInterval);
        document.getElementById('resendButton').disabled = false;
        document.getElementById('btnenviar').disabled = true;
        document.getElementById('code').disabled = true;
    } else {
        countdown--;
    }
}, 1000);

function resendCode() {
    countdown = 60;
    document.getElementById('countdown').innerHTML = '1:00';
    document.getElementById('resendButton').disabled = true;
    document.getElementById('btnenviar').disabled = false;
    document.getElementById('code').disabled = false;

    // Cambiar la siguiente línea por la URL correcta y el userId correspondiente
    // window.location.href = '{{ route("smsNew", ["userId" => ":userId"]) }}'.replace(':userId', "{{ request()->route('userId') }}");

    window.location.href = '/smsNew/' + IdSmsNew;
}
