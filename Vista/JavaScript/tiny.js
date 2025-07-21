// Inicializar TinyMCE con configuración personalizada
tinymce.init({
  selector: '#contenido',
  language: 'es',
  height: 400,
  menubar: false,
  plugins: [
    'advlist', 'autolink', 'lists', 'charmap', 'preview', 'anchor',
    'searchreplace', 'visualblocks', 'fullscreen',
    'insertdatetime', 'table', 'paste', 'help', 'wordcount'
  ],
  toolbar: 'undo redo | bold italic underline | alignleft aligncenter alignright alignjustify | ' +
           'bullist numlist | preview fullscreen',
  content_style: 'body { font-family: Arial, sans-serif; font-size:14px; }'
});

// Dropdown tipo informe
document.querySelectorAll('.tipo-informe-option').forEach((item) => {
  item.addEventListener('click', function (e) {
    e.preventDefault();
    const valor = this.getAttribute('data-value');
    const texto = this.textContent;

    document.getElementById('dropdownTipoInforme').textContent = texto;
    document.getElementById('tipo_informe').value = valor;

    const destinatarioDiv = document.getElementById('destinatarioContainer');
    if (valor === 'con_destinatario') {
      destinatarioDiv.style.display = 'block';
    } else {
      destinatarioDiv.style.display = 'none';
      document.getElementById('destinatario').value = '';
    }
  });
});

// Validación al enviar
const form = document.getElementById('formInforme');
form.addEventListener('submit', function (event) {
  tinymce.triggerSave(); // Actualiza el contenido del textarea

  if (!form.checkValidity()) {
    event.preventDefault();
    event.stopPropagation();
    return;
  }

  const contenidoTexto = tinymce.get('contenido').getContent({ format: 'text' }).trim();
  if (contenidoTexto.length === 0) {
    event.preventDefault();
    event.stopPropagation();
    alert('El contenido no puede estar vacío.');
    return false;
  }
});

// Limpiar formulario completo
const btnLimpiar = document.getElementById('btnLimpiar');
btnLimpiar.addEventListener('click', function () {
  form.reset(); // Limpiar inputs normales

  // Limpiar editor
  if (tinymce.get('contenido')) {
    tinymce.get('contenido').setContent('');
  }

  // Ocultar y limpiar destinatario
  document.getElementById('destinatario').value = '';
  document.getElementById('destinatarioContainer').style.display = 'none';

  // Resetear dropdown
  document.getElementById('dropdownTipoInforme').textContent = 'Informe Normal';
  document.getElementById('tipo_informe').value = 'normal';
});
