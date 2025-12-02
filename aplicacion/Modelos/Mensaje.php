<?php
class Mensaje {
    private $db;
    private $table = 'Mensajes';

    public function __construct() {
        require_once 'aplicacion/Configuracion/conexion.php';
        $conexion = new Conexion();
        $this->db = $conexion->conectar();
    }

    /**
     * Crea un nuevo mensaje en una conversación.
     *
     * @param int $id_conversacion ID de la conversación.
     * @param int $id_remitente ID del usuario que envía el mensaje.
     * @param int $id_destinatario ID del usuario que recibe el mensaje.
     * @param string $contenido Contenido del mensaje.
     * @return array|false El mensaje creado o false si falla.
     */
    public function crear($id_conversacion, $id_remitente, $id_destinatario, $contenido, $es_sistema = 0) {
        try {
            $this->db->beginTransaction();

            // Insertar el mensaje
            $query = "INSERT INTO {$this->table} (id_conversacion, id_remitente, id_destinatario, contenido, es_sistema) 
                      VALUES (:id_conversacion, :id_remitente, :id_destinatario, :contenido, :es_sistema)";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id_conversacion', $id_conversacion, PDO::PARAM_INT);
            $stmt->bindParam(':id_remitente', $id_remitente, PDO::PARAM_INT);
            $stmt->bindParam(':id_destinatario', $id_destinatario, PDO::PARAM_INT);
            $stmt->bindParam(':contenido', $contenido, PDO::PARAM_STR);
            $stmt->bindParam(':es_sistema', $es_sistema, PDO::PARAM_INT);
            
            if (!$stmt->execute()) {
                $this->db->rollBack();
                return false;
            }

            $id_mensaje = $this->db->lastInsertId();

            // Actualizar la fecha de actualización de la conversación
            $query_update_conv = "UPDATE Conversaciones SET fecha_actualizacion = GETDATE() WHERE id_conversacion = :id_conversacion";
            $stmt_update = $this->db->prepare($query_update_conv);
            $stmt_update->bindParam(':id_conversacion', $id_conversacion, PDO::PARAM_INT);
            $stmt_update->execute();

            $this->db->commit();

            return $this->obtenerPorId($id_mensaje);

        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log("Error en Mensaje::crear: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtiene todos los mensajes de una conversación.
     *
     * @param int $id_conversacion ID de la conversación.
     * @return array Lista de mensajes.
     */
    public function obtenerPorConversacion($id_conversacion) {
        try {
            $query = "SELECT * FROM {$this->table} WHERE id_conversacion = :id_conversacion ORDER BY fecha_envio ASC";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id_conversacion', $id_conversacion, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Error en Mensaje::obtenerPorConversacion: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtiene los mensajes no leídos de una conversación para un destinatario específico.
     *
     * @param int $id_conversacion ID de la conversación.
     * @param int $id_destinatario ID del usuario destinatario.
     * @return array Lista de mensajes no leídos.
     */
    public function obtenerNuevos($id_conversacion, $id_destinatario) {
        try {
            $query = "SELECT * FROM {$this->table} 
                      WHERE id_conversacion = :id_conversacion 
                      AND id_destinatario = :id_destinatario 
                      AND leido = 0 
                      ORDER BY fecha_envio ASC";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id_conversacion', $id_conversacion, PDO::PARAM_INT);
            $stmt->bindParam(':id_destinatario', $id_destinatario, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Error en Mensaje::obtenerNuevos: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Marca los mensajes de una conversación como leídos para un destinatario.
     *
     * @param int $id_conversacion ID de la conversación.
     * @param int $id_destinatario ID del usuario que ha leído los mensajes.
     * @return bool True si se actualizó, false si no.
     */
    public function marcarComoLeidos($id_conversacion, $id_destinatario) {
        try {
            $query = "UPDATE {$this->table} SET leido = 1 
                      WHERE id_conversacion = :id_conversacion 
                      AND id_destinatario = :id_destinatario 
                      AND leido = 0";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id_conversacion', $id_conversacion, PDO::PARAM_INT);
            $stmt->bindParam(':id_destinatario', $id_destinatario, PDO::PARAM_INT);
            
            return $stmt->execute();

        } catch (PDOException $e) {
            error_log("Error en Mensaje::marcarComoLeidos: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtiene un mensaje por su ID.
     *
     * @param int $id_mensaje ID del mensaje.
     * @return array|false El mensaje o false si no se encuentra.
     */
    private function obtenerPorId($id_mensaje) {
        try {
            $query = "SELECT * FROM {$this->table} WHERE id_mensaje = :id_mensaje";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id_mensaje', $id_mensaje, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Marca un mensaje como eliminado (soft delete).
     *
     * @param int $id_mensaje El ID del mensaje a eliminar.
     * @param int $id_usuario_actual El ID del usuario que solicita la eliminación.
     * @return bool True si se eliminó, false en caso contrario.
     */
    public function eliminarMensaje($id_mensaje, $id_usuario_actual) {
        try {
            // La cláusula "AND id_remitente = :id_usuario_actual" es una medida de seguridad CRUCIAL
            // para asegurar que un usuario solo pueda eliminar sus propios mensajes.
            $query = "UPDATE {$this->table} SET estado = 1 
                      WHERE id_mensaje = :id_mensaje 
                      AND id_remitente = :id_usuario_actual";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id_mensaje', $id_mensaje, PDO::PARAM_INT);
            $stmt->bindParam(':id_usuario_actual', $id_usuario_actual, PDO::PARAM_INT);
            
            return $stmt->execute() && $stmt->rowCount() > 0;

        } catch (PDOException $e) {
            error_log("Error en Mensaje::eliminarMensaje: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Cuenta el total de mensajes no leídos para un usuario en todas las conversaciones.
     *
     * @param int $id_usuario ID del usuario destinatario.
     * @return int Total de mensajes no leídos.
     */
    public function contarNoLeidosGlobal($id_usuario) {
        try {
            $query = "SELECT COUNT(*) as total FROM {$this->table} 
                      WHERE id_destinatario = :id_usuario AND leido = 0";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
            $stmt->execute();
            
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado['total'] ?? 0;

        } catch (PDOException $e) {
            error_log("Error en Mensaje::contarNoLeidosGlobal: " . $e->getMessage());
            return 0;
        }
    }
}
?>