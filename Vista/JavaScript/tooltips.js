  // Inicializar tooltips
  //este codigo esta aqui ya que, aunque si los tooltips los "?" de los inputs de contraseña
  //igual se van a ver, esto esta para que s evena mejor, en forma de bloque 
  document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
      return new bootstrap.Tooltip(tooltipTriggerEl);
    });
  });

