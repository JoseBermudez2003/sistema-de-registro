<?php
// Iniciar o reanudar la sesión para acceder a las variables de sesión
session_start(); 

// Inicializar variable para almacenar mensajes del sistema
$mensaje = ""; 

// Incluir archivos necesarios para la operación
include 'Conexion/Conexion.php';          // Conexión a la base de datos
include 'Funciones/RegistrarHistorial.php'; // Función para registrar acciones en el historial

// Verificar si se recibió un ID por POST (para identificar qué caso eliminar)
if (!empty($_POST["id"])) {
    // Obtener el ID del caso a eliminar desde el formulario POST
    $id = $_POST["id"];
    
    // Consulta SQL para marcar el caso como inactivo (eliminación lógica)
    // Cambia el estado a 0 en lugar de borrar físicamente el registro
    $eliminarSoli = "UPDATE casos SET estado = 0 WHERE id = :id";
    
    // Preparar la consulta SQL para ejecución segura (evita inyecciones SQL)
    $stmtSoli = $pdo->prepare($eliminarSoli);
    // Vincular el parámetro :id con la variable $id
    $stmtSoli->bindParam(':id', $id);

    // Iniciar transacción para asegurar la integridad de los datos
    // Si algo falla, se puede deshacer toda la operación
    $pdo->beginTransaction(); 

    try {
        // Ejecutar la consulta de actualización/eliminación
        $stmtSoli->execute();

        // Confirmar los cambios en la base de datos si todo fue bien
        $pdo->commit();
        
        // Registrar esta acción en el historial del sistema
        // Parámetros: conexión PDO, cédula del usuario desde sesión, descripción de la acción
        registrarHistorial($pdo, $_SESSION['cedula'], "Eliminó un caso");

        // Crear mensaje de éxito para mostrar al usuario
        $_SESSION['mensaje'] = "Se ha eliminado este caso";
        
        // Redirigir a la página que muestra la lista de casos
        // Usar ubicación relativa (../ para subir directorios)
        header("Location: ../Vista/Vistas/listacasos.php");
        // Terminar la ejecución del script después de redireccionar
        exit();
        
    } catch (PDOException $e) {
        // Si ocurre algún error en el bloque try:
        
        // Revertir todos los cambios realizados durante la transacción
        $pdo->rollBack(); 
        
        // Crear mensaje de error detallado
        $mensaje = "Error al eliminar el Historial: " . $e->getMessage();
        // Almacenar el mensaje en sesión para mostrarlo después de redirección
        $_SESSION['mensaje'] = $mensaje; 
        
        // Redirigir a la lista de casos (aunque hubo error)
        header("Location: ../Vista/Vistas/listacasos.php");
        exit();
    }
}

// Cerrar la conexión a la base de datos (buena práctica)
$pdo = null;
?>