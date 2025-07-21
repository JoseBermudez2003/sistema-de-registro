<?php 
// Función para eliminar un archivo
function eliminarArchivo($ruta) {
    if (file_exists($ruta)) {
        return unlink($ruta);
    }
    return false;
}
// Función para listar las firmas digitales
function listarFirmasDigitales($pdo) {
    try {
        $query = "SELECT id, nombre_firmante, cargo_firmante FROM firmas_digitales";
        $stmt = $pdo->query($query);
        $firmas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $firmas;
    } catch (PDOException $e) {
        error_log("Error en InformeModelo::listarFirmasDigitales: " . $e->getMessage());
        return [];
    }
}
// Función para guardar una nueva firma digital
function guardarFirmaDigital($pdo, $nombre_firmante, $cargo_firmante, $ruta_imagen) {
    try {
        $query = "INSERT INTO firmas_digitales (nombre_firmante, cargo_firmante, ruta_imagen) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(1, $nombre_firmante, PDO::PARAM_STR);
        $stmt->bindParam(2, $cargo_firmante, PDO::PARAM_STR);
        $stmt->bindParam(3, $ruta_imagen, PDO::PARAM_STR);
        return $stmt->execute();
    } catch (PDOException $e) {
        error_log("Error en InformeModelo::guardarFirmaDigital: " . $e->getMessage());
        return false;
    }
}
// Función para eliminar una firma digital
function eliminarFirmaDigital($pdo, $id_firma) {
    try {
        $query = "DELETE FROM firmas_digitales WHERE id = ?";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(1, $id_firma, PDO::PARAM_INT);
        return $stmt->execute();
    } catch (PDOException $e) {
        error_log("Error en InformeModelo::eliminarFirmaDigital: " . $e->getMessage());
        return false;
    }
}
// Función para obtener la información de una firma digital por su ID
function obtenerFirmaDigitalPorId($pdo, $id_firma) {
    try {
        $query = "SELECT nombre_firmante, cargo_firmante, ruta_imagen FROM firmas_digitales WHERE id = ?";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(1, $id_firma, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error en InformeModelo::obtenerFirmaDigitalPorId: " . $e->getMessage());
        return null;
    }
}
// Función para verificar si existe una firma registrada con el mismo nombre
function existeFirmaConNombre($pdo, $nombreFirmante) {
    $query = "SELECT COUNT(*) FROM firmas_digitales WHERE nombre_firmante = :nombre";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':nombre', $nombreFirmante);
    $stmt->execute();
    return (int) $stmt->fetchColumn() > 0;
}
?>