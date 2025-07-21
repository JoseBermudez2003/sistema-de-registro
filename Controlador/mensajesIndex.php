
<!-- este codigo es para mostrar los mensajes en el index, es decir, en la pagina de iniciar sesion en el sistema 
y lo cree porque no me mostraban las imagenes con el otro controlador de mensajes por el tema de las rutas -->



<!-- Contenedor fijo para la notificación tipo toast, ubicado en la esquina inferior derecha con padding -->
<div class="toast-container position-fixed bottom-0 end-0 p-3">

  <!-- Toast individual con ID 'liveToast', accesible para lectores de pantalla -->
  <div id="liveToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">

    <!-- Encabezado del toast: contiene un ícono, el título y un botón de cerrar -->
    <div class="toast-header">

      <!-- Ícono del toast: una imagen SVG con borde redondeado y espacio a la derecha -->
      <img src="vista/Recursos/Iconos/house.svg" style="width: 2em; border-radius: 50%;margin-right: 10px">

      <!-- Título del toast: texto en negrita alineado a la izquierda -->
      <strong class="me-auto">CorviSucre</strong>

      <!-- Botón para cerrar el toast, con estilos de Bootstrap y etiqueta accesible -->
      <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>

    <!-- Cuerpo del toast: donde se muestra el mensaje dinámico -->
    <div class="toast-body">

      <!-- Contenedor del mensaje: texto centrado, color negro y tamaño normal -->
      <div style="color: #000; font-size: 1em;" class="text-center">

        <!-- PHP: muestra el contenido de la variable $mensaje -->
        <?php echo $mensaje; ?>
      </div>
    </div>
  </div>
</div>
