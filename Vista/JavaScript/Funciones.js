// --------------------------------------------
// Funciones para validar y filtrar entradas en inputs
// --------------------------------------------

/**
 * Permite solo números (0-9) en el input.
 * @param {HTMLInputElement} input 
 */
function validateInput(input) {
  // Eliminar cualquier carácter que no sea número
  input.value = input.value.replace(/[^0-9]/g, '');
}

/**
 * Permite solo letras (mayúsculas y minúsculas), espacios y acentos.
 * @param {HTMLInputElement} input 
 */
function validarSoloLetras(input) {
  // Eliminar cualquier carácter que no sea letra o espacio
  input.value = input.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '');
}

/**
 * Permite solo números y punto decimal (.)
 * @param {HTMLInputElement} input 
 */
function validarInputConPunto(input) {
  // Eliminar cualquier carácter que no sea número o punto
  input.value = input.value.replace(/[^0-9.]/g, '');
}

/**
 * Permite letras, números, espacios, puntos y acentos.
 * @param {HTMLInputElement} input 
 */
function validarInputLetNum(input) {
  // Eliminar cualquier carácter que no sea número, letra o espacio
  input.value = input.value.replace(/[^0-9.a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '');
}


/**
 * Permite solo letras (mayúsculas y minúsculas), comas, espacios y acentos.
 * @param {HTMLInputElement} input 
 */
function validarLetrasComasEspacios(input) {
  // Reemplaza cualquier carácter que no sea letra, coma, espacio o acento
  input.value = input.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ,\s]/g, '');
}


/**
 * Permite letras, números, acentos y signos especiales, 
 * excluyendo espacios. Se permite todo excepto espacios.
 * @param {HTMLInputElement} input 
 */
function validarInputSinEspacio(input) {
  // Eliminar espacios y caracteres no permitidos
  input.value = input.value.replace(/[^0-9.a-zA-ZáéíóúÁÉÍÓÚñÑ\x21-\x2F\x3A-\x40\x5B-\x60\x7B-\x7E]/g, '');
}

// --------------------------------------------
// Validación de formulario
// --------------------------------------------

/**
 * Valida que se haya seleccionado una opción en un select con id="opcion".
 * Muestra alerta si no se seleccionó nada y evita el envío del formulario.
 * @returns {boolean} true si hay selección, false si no
 */
function validateForm() {
  var selectedOption = document.getElementById("opcion").value;
  if (selectedOption === "") {
    alert("Por favor Seleccione una Opción");
    return false; // Evita el envío del formulario
  }
  return true; // Permite enviar el formulario
}

// --------------------------------------------
// Validación de correo electrónico en input
// --------------------------------------------

/**
 * Permite solo letras, números, arroba (@) y punto (.) en el input.
 * @param {HTMLInputElement} input 
 */
function validarCorreoInput(input) {
  input.value = input.value.replace(/[^a-zA-Z0-9@.]/g, '');
}

// --------------------------------------------
// Mostrar mensajes emergentes (Bootstrap Toast)
// --------------------------------------------

document.addEventListener('DOMContentLoaded', function () {
  var toastLiveExample = document.getElementById('liveToast');
  if (toastLiveExample) {
    var toast = new bootstrap.Toast(toastLiveExample);
    toast.show();
  }
});

// --------------------------------------------
// Mostrar/ocultar contraseña
// --------------------------------------------

function togglePassword() {
  const passwordInput = document.getElementById('password');
  // Nota: El id 'icono-password' debe estar en el elemento que contiene el ícono para que este cambio funcione
  const icon = document.getElementById('icono-password');

  if (passwordInput.type === 'password') {
    passwordInput.type = 'text';
    if (icon) {
      icon.classList.remove('bi-eye');
      icon.classList.add('bi-eye-slash');
    }
  } else {
    passwordInput.type = 'password';
    if (icon) {
      icon.classList.remove('bi-eye-slash');
      icon.classList.add('bi-eye');
    }
  }
}
