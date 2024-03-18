// Archivo: public/js/countdown-manager.js

class CountdownManager {
    static init(initialCountdown, resendUrl, userId) {
        this.countdown = initialCountdown;
        this.resendUrl = resendUrl.replace(':userId', userId);

        this.countdownInterval = setInterval(() => {
            this.updateCountdown();
        }, 1000);
    }

    static updateCountdown() {
        const minutes = Math.floor(this.countdown / 60);
        const seconds = this.countdown % 60;
        document.getElementById('countdown').innerHTML = `${minutes}:${(seconds < 10 ? '0' : '')}${seconds}`;

        if (this.countdown <= 0) {
            clearInterval(this.countdownInterval);
            this.disableElements();
        } else {
            this.countdown--;
        }
    }

    static disableElements() {
        document.getElementById('resendButton').disabled = false;
        document.getElementById('btnenviar').disabled = true;
        document.getElementById('code').disabled = true;
    }

    static resendCode() {
        this.countdown = 60;
        document.getElementById('countdown').innerHTML = '1:00';
        document.getElementById('resendButton').disabled = true;
        document.getElementById('btnenviar').disabled = false;
        document.getElementById('code').disabled = false;

        // Cambia la siguiente línea por la forma adecuada de redirigir o manejar la lógica de reenvío
        window.location.href = this.resendUrl;
    }
}

// Exporta la clase CountdownManager para que pueda ser utilizada en otros scripts

