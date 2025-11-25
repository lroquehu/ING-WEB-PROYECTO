<?php
class Notificacion {
    private $db;
    private $table = 'Notificaciones';

    public function __construct() {
        require_once __DIR__ . '/../Configuracion/conexion.php';
        $conexion = new Conexion();
        $this->db = $conexion->conectar();
    }

    /**
     * Crea una nueva notificación en la base de datos.
     *
     * @param int $id_usuario ID del usuario que recibirá la notificación.
     * @param string $tipo Tipo de notificación (e.g., 'favorito', 'mensaje').
     * @param string $mensaje El texto de la notificación.
     * @param string $enlace URL a la que se redirigirá al hacer clic.
     * @return bool True si se creó correctamente, false en caso contrario.
     */
    public function crear($id_usuario, $tipo, $mensaje, $enlace) {
        try {
            $query = "INSERT INTO {$this->table} (id_usuario, tipo, mensaje, enlace, leido, fecha)
                      VALUES (:id_usuario, :tipo, :mensaje, :enlace, 0, GETDATE())";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
            $stmt->bindParam(':tipo', $tipo);
            $stmt->bindParam(':mensaje', $mensaje);
            $stmt->bindParam(':enlace', $enlace);
            
            return $stmt->execute();

        } catch (PDOException $e) {
            error_log("Error en Notificacion::crear: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Cuenta las notificaciones no leídas de un usuario.
     *
     * @param int $id_usuario ID del usuario.
     * @return int Número de notificaciones no leídas.
     */
    public function contarNoLeidas($id_usuario) {
        try {
            $query = "SELECT COUNT(*) as total FROM {$this->table} 
                      WHERE id_usuario = :id_usuario AND leido = 0";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
            $stmt->execute();
            
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado['total'] ?? 0;

        } catch (PDOException $e) {
            error_log("Error en Notificacion::contarNoLeidas: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Obtiene las últimas notificaciones de un usuario (leídas o no).
     *
     * @param int $id_usuario ID del usuario.
     * @param int $limite Número máximo de notificaciones a obtener.
     * @return array Lista de notificaciones.
     */
    public function obtenerUltimas($id_usuario, $limite = 15) {
        try {
            $query = "SELECT id_notificacion as id, tipo, mensaje, enlace, leido, fecha_creacion as fecha
                    FROM {$this->table}
                    WHERE id_usuario = :id_usuario
                    ORDER BY fecha_creacion DESC
                    OFFSET 0 ROWS
                    FETCH NEXT :limite ROWS ONLY";

            $stmt = $this->db->prepare($query);

            $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
            $stmt->bindParam(':limite', $limite, PDO::PARAM_INT);

            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Error en Notificacion::obtenerUltimas: " . $e->getMessage());
            return [];
        }
    }


    /**
     * Marca una notificación específica como leída.
     *
     * @param int $id_notificacion ID de la notificación.
     * @param int $id_usuario ID del usuario (para seguridad, asegurar que la notif le pertenece).
     * @return bool True si se marcó como leída, false en caso contrario.
     */
    public function marcarLeida($id_notificacion, $id_usuario) {
        try {
            $query = "UPDATE {$this->table} 
                      SET leido = 1
                      WHERE id = :id_notificacion AND id_usuario = :id_usuario";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id_notificacion', $id_notificacion, PDO::PARAM_INT);
            $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);

            return $stmt->execute();

        } catch (PDOException $e) {
            error_log("Error en Notificacion::marcarLeida: " . $e->getMessage());
            return false;
        }
    }

     /**
     * Obtiene una notificación por su ID para verificar el propietario y obtener el enlace.
     *
     * @param int $id_notificacion
     * @return array|null
     */
    public function obtenerPorId($id_notificacion) {
        try {
            $query = "SELECT id, id_usuario, enlace FROM {$this->table} WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id_notificacion, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en Notificacion::obtenerPorId: " . $e->getMessage());
            return null;
        }
    }
}
?>