<?php
class Conversacion {
    private $db;
    private $table = 'Conversaciones';

    public function __construct() {
        require_once 'aplicacion/Configuracion/conexion.php';
        $conexion = new Conexion();
        $this->db = $conexion->conectar();
    }

    /**
     * Inicia una nueva conversación o recupera una existente.
     * Si estaba oculta para alguno, la reactiva.
     *
     * @param int $id_usuario1 ID del primer usuario.
     * @param int $id_usuario2 ID del segundo usuario.
     * @return array|false Los datos de la conversación o false si falla.
     */
    public function iniciarOObtener($id_usuario1, $id_usuario2) {
        try {
            $user1 = min($id_usuario1, $id_usuario2);
            $user2 = max($id_usuario1, $id_usuario2);

            $query = "SELECT * FROM {$this->table} WHERE id_usuario1 = :user1 AND id_usuario2 = :user2";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':user1', $user1, PDO::PARAM_INT);
            $stmt->bindParam(':user2', $user2, PDO::PARAM_INT);
            $stmt->execute();
            
            $conversacion = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($conversacion) {
                // LOGICA DE REACTIVACIÓN: Si existe pero está oculta, la hacemos visible nuevamente
                // porque se está intentando acceder o enviar un mensaje nuevo.
                if ($conversacion['visible_usuario1'] == 0 || $conversacion['visible_usuario2'] == 0) {
                    $update = "UPDATE {$this->table} SET visible_usuario1 = 1, visible_usuario2 = 1 WHERE id_conversacion = :id";
                    $stmtUp = $this->db->prepare($update);
                    $stmtUp->bindParam(':id', $conversacion['id_conversacion']);
                    $stmtUp->execute();
                }
                return $conversacion;
            }

            // Crear nueva
            $query_insert = "INSERT INTO {$this->table} (id_usuario1, id_usuario2, visible_usuario1, visible_usuario2) VALUES (:user1, :user2, 1, 1)";
            $stmt_insert = $this->db->prepare($query_insert);
            $stmt_insert->bindParam(':user1', $user1, PDO::PARAM_INT);
            $stmt_insert->bindParam(':user2', $user2, PDO::PARAM_INT);
            
            if ($stmt_insert->execute()) {
                $id_conversacion = $this->db->lastInsertId();
                return $this->obtenerPorId($id_conversacion);
            }

            return false;

        } catch (PDOException $e) {
            error_log("Error en Conversacion::iniciarOObtener: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtiene todas las conversaciones VISIBLES de un usuario.
     *
     * @param int $id_usuario ID del usuario.
     * @return array Lista de conversaciones.
     */
    public function obtenerPorUsuario($id_usuario) {
        try {
            $query = "
                SELECT 
                    c.id_conversacion,
                    c.fecha_actualizacion,
                    u.id_usuario AS id_otro_usuario,
                    u.nombres,
                    u.apellidos,
                    u.foto_perfil,
                    lm.contenido AS ultimo_mensaje,
                    lm.estado AS ultimo_mensaje_estado,
                    lm.fecha_envio AS fecha_ultimo_mensaje,
                    (SELECT COUNT(*) FROM Mensajes m 
                     WHERE m.id_conversacion = c.id_conversacion 
                     AND m.id_destinatario = :id_destinatario
                     AND m.leido = 0) AS no_leidos
                FROM {$this->table} c
                JOIN Usuarios u ON u.id_usuario = CASE 
                                                    WHEN c.id_usuario1 = :id_case THEN c.id_usuario2 
                                                    ELSE c.id_usuario1 
                                                END
                OUTER APPLY (
                    SELECT TOP 1 contenido, fecha_envio, estado
                    FROM Mensajes
                    WHERE id_conversacion = c.id_conversacion
                    ORDER BY fecha_envio DESC
                ) AS lm
                WHERE (
                    (c.id_usuario1 = :id_where1 AND c.visible_usuario1 = 1) 
                    OR 
                    (c.id_usuario2 = :id_where2 AND c.visible_usuario2 = 1)
                )
                ORDER BY COALESCE(lm.fecha_envio, c.fecha_actualizacion) DESC
            ";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id_destinatario', $id_usuario, PDO::PARAM_INT);
            $stmt->bindParam(':id_case', $id_usuario, PDO::PARAM_INT);
            $stmt->bindParam(':id_where1', $id_usuario, PDO::PARAM_INT);
            $stmt->bindParam(':id_where2', $id_usuario, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Error en Conversacion::obtenerPorUsuario: " . $e->getMessage());
            return [];
        }
    }


    /**
     * Obtiene una conversación por su ID, verificando que el usuario sea participante.
     *
     * @param int $id_conversacion ID de la conversación.
     * @param int $id_usuario_actual ID del usuario que solicita.
     * @return array|false Los datos de la conversación o false si no se encuentra o no tiene permiso.
     */
    public function obtenerPorId($id_conversacion, $id_usuario_actual = null) {
        try {
            $query = "SELECT * FROM {$this->table} WHERE id_conversacion = :id_conversacion";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id_conversacion', $id_conversacion, PDO::PARAM_INT);
            $stmt->execute();
            $conversacion = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($id_usuario_actual && $conversacion) {
                if ($conversacion['id_usuario1'] != $id_usuario_actual && $conversacion['id_usuario2'] != $id_usuario_actual) {
                    return false;
                }
            }
            return $conversacion;

        } catch (PDOException $e) {
            error_log("Error en Conversacion::obtenerPorId: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Oculta la conversación para el usuario específico.
     * Si ambos usuarios la han eliminado, se podría borrar físicamente (opcional),
     * aquí solo la ocultamos.
     */
    public function eliminarParaUsuario($id_conversacion, $id_usuario) {
        try {
            // 1. Obtener la conversación para saber si soy usuario1 o usuario2
            $conv = $this->obtenerPorId($id_conversacion, $id_usuario);
            if (!$conv) return false;

            $campo = ($conv['id_usuario1'] == $id_usuario) ? 'visible_usuario1' : 'visible_usuario2';

            $query = "UPDATE {$this->table} SET {$campo} = 0 WHERE id_conversacion = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id_conversacion, PDO::PARAM_INT);
            
            return $stmt->execute();

        } catch (PDOException $e) {
            error_log("Error en Conversacion::eliminarParaUsuario: " . $e->getMessage());
            return false;
        }
    }
}
?>