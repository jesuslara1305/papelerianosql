/* ============================================================
   js/pago.js — Sanrio Shop
   Funcionalidad de la vista de pago (checkout/pago.php).
   Portado de ProyectoWeb/js/scripts.js (sección "PAGO.PHP"),
   quitando todo lo que dependía del carrito en localStorage
   y de la reserva de stock por sesión (aquí el carrito y el
   inventario viven en el servidor / MongoDB).
   ============================================================ */

/* ── Selector de método de pago ─────────────────────────────── */
function setMetodo(metodo, btn) {
    document.querySelectorAll('.pago-metodo-btn').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('.pago-panel').forEach(p => p.classList.remove('active'));
    btn.classList.add('active');
    document.getElementById('panel-' + metodo)?.classList.add('active');
}

/* ── Tarjeta visual: flip al capturar el CVV ────────────────── */
function flipCard(toBack) {
    document.getElementById('cardInner')?.classList.toggle('flipped', toBack);
}

/* ── Formateo del número de tarjeta + detección de marca ────── */
function formatCardNumber(input) {
    let val = input.value.replace(/\D/g, '').slice(0, 16);
    val = val.replace(/(.{4})/g, '$1 ').trim();
    input.value = val;

    const display = document.getElementById('cardNumberDisplay');
    if (display) {
        const padded = val.replace(/\s/g, '').padEnd(16, '•');
        display.textContent = padded.replace(/(.{4})/g, '$1 ').trim();
    }

    const logoEl = document.getElementById('cardLogoFront');
    if (!logoEl) return;
    const digits = val.replace(/\s/g, '');
    if (digits.startsWith('4'))      logoEl.innerHTML = '<i class="fab fa-cc-visa"></i>';
    else if (/^5[1-5]/.test(digits)) logoEl.innerHTML = '<i class="fab fa-cc-mastercard"></i>';
    else if (/^3[47]/.test(digits))  logoEl.innerHTML = '<i class="fab fa-cc-amex"></i>';
    else                              logoEl.innerHTML = '<i class="far fa-credit-card"></i>';
}

/* ── Nombre del titular en la tarjeta visual ─────────────────── */
function updateCardName(input) {
    const el = document.getElementById('cardNameDisplay');
    if (el) el.textContent = (input.value.toUpperCase() || 'NOMBRE APELLIDO').slice(0, 22);
}

/* ── Comprueba si una fecha MM/AA ya venció ─────────────────── */
function tarjetaVencida(valor) {
    if (!valor || valor.length < 5) return false; // incompleta, no validar aún
    const [mmStr, aaStr] = valor.split('/');
    const mes  = parseInt(mmStr, 10);
    const anio = 2000 + parseInt(aaStr, 10);
    if (isNaN(mes) || isNaN(anio)) return false;
    const hoy     = new Date();
    const mesHoy  = hoy.getMonth() + 1;
    const anioHoy = hoy.getFullYear();
    if (anio < anioHoy) return true;
    if (anio === anioHoy && mes < mesHoy) return true;
    return false;
}

/* ── Formateo de la fecha de vencimiento + validación visual ─── */
function formatExp(input) {
    let val = input.value.replace(/\D/g, '');

    if (val.length >= 2) {
        let mes = parseInt(val.substring(0, 2));
        if (mes > 12) val = '12' + val.substring(2);
        if (mes === 0) val = '01' + val.substring(2);
        input.value = val.substring(0, 2) + '/' + val.substring(2, 4);
    } else {
        input.value = val;
    }

    const el = document.getElementById('cardExpDisplay');
    if (el) el.textContent = input.value || 'MM/AA';

    let errEl = document.getElementById('exp-error-msg');
    if (!errEl) {
        errEl = document.createElement('div');
        errEl.id = 'exp-error-msg';
        errEl.style.display = 'none';
        errEl.innerHTML = '<i class="fas fa-exclamation-circle me-1"></i>Esta tarjeta ya está vencida.';
        input.closest('.pago-group')?.appendChild(errEl);
    }

    const btnConfirmar = document.getElementById('btnConfirmar');
    if (tarjetaVencida(input.value)) {
        errEl.style.display = 'block';
        input.style.borderColor = '#dc3545';
        if (btnConfirmar) btnConfirmar.disabled = true;
    } else {
        errEl.style.display = 'none';
        input.style.borderColor = '';
        if (btnConfirmar) btnConfirmar.disabled = false;
    }
}

/* ── CVV: solo dígitos + reflejo en la tarjeta visual ────────── */
function updateCvv(input) {
    input.value = input.value.replace(/\D/g, '').slice(0, 4);
    const el = document.getElementById('cardCvvDisplay');
    if (el) el.textContent = input.value.padEnd(3, '•');
}

/* ── Validación final antes de enviar el formulario a MongoDB ── */
document.addEventListener('DOMContentLoaded', () => {
    const form = document.querySelector('#formPago');
    if (!form) return;

    form.addEventListener('submit', (e) => {
        const expInput = document.getElementById('cardExp');
        if (expInput && tarjetaVencida(expInput.value)) {
            e.preventDefault();
            alert('La tarjeta ingresada ya está vencida. Verifica la fecha de vencimiento.');
            return false;
        }

        const btn = document.getElementById('btnConfirmar');
        if (btn) {
            btn.classList.add('loading');
            btn.disabled = true;
        }
        return true;
    });
});
