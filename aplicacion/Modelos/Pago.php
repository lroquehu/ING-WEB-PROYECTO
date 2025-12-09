<?php
    class Pago {
        private $db;

        public function __construct($db) {
            $this->db = $db;
        }

        public function registrarPago($id_usuario, $id_publicacion, $mp_payment_id, $monto, $metodo_pago, $estado, $detalle_estado) {
            try {
                $sql = "INSERT INTO Pagos (id_usuario, id_publicacion, mp_payment_id, monto, metodo_pago, estado, detalle_estado, fecha_pago) 
                        VALUES (:id_usuario, :id_publicacion, :mp_payment_id, :monto, :metodo_pago, :estado, :detalle_estado, GETDATE())";
                
                $stmt = $this->db->prepare($sql);
                
                $stmt->bindParam(':id_usuario', $id_usuario);
                $stmt->bindParam(':id_publicacion', $id_publicacion);
                $stmt->bindParam(':mp_payment_id', $mp_payment_id);
                $stmt->bindParam(':monto', $monto);
                $stmt->bindParam(':metodo_pago', $metodo_pago);
                $stmt->bindParam(':estado', $estado);
                $stmt->bindParam(':detalle_estado', $detalle_estado);

                if ($stmt->execute()) {
                    return $this->db->lastInsertId(); // Devolvemos el ID en lugar de true
                } else {
                    return 0; // Devolvemos 0 o false en caso de error
                }
            } catch (PDOException $e) {
                // Puedes loguear el error aquí si quieres
                return 0;
            }
        }

        public function obtenerVentasPorUsuario($id_usuario) {
            $sql = "SELECT p.*, pub.titulo, u.id_usuario as id_comprador, u.nombres as comprador_nombre, u.apellidos as comprador_apellido 
                    FROM Pagos p
                    INNER JOIN Publicaciones pub ON p.id_publicacion = pub.id_publicacion
                    INNER JOIN Usuarios u ON p.id_usuario = u.id_usuario -- Quien pagó (comprador)
                    WHERE pub.id_usuario = :id_vendedor
                    ORDER BY p.fecha_pago DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id_vendedor', $id_usuario);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        public function obtenerComprasPorUsuario($id_usuario) {
            $sql = "SELECT p.*, pub.titulo, u.id_usuario as id_vendedor, u.nombres as vendedor_nombre, u.apellidos as vendedor_apellido 
                    FROM Pagos p
                    INNER JOIN Publicaciones pub ON p.id_publicacion = pub.id_publicacion
                    INNER JOIN Usuarios u ON pub.id_usuario = u.id_usuario -- Quien vendió (vendedor)
                    WHERE p.id_usuario = :id_comprador
                    ORDER BY p.fecha_pago DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id_comprador', $id_usuario);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }


        public function obtenerPagoPorId($id_pago) {
            $sql = "SELECT p.*, pub.titulo, pub.id_usuario as id_vendedor, 
                    uc.nombres as comprador_nombre, uc.apellidos as comprador_apellido, uc.correo_institucional as comprador_email,
                    uv.nombres as vendedor_nombre, uv.apellidos as vendedor_apellido
                    FROM Pagos p
                    INNER JOIN Publicaciones pub ON p.id_publicacion = pub.id_publicacion
                    INNER JOIN Usuarios uc ON p.id_usuario = uc.id_usuario -- Comprador
                    INNER JOIN Usuarios uv ON pub.id_usuario = uv.id_usuario -- Vendedor
                    WHERE p.id_pago = :id_pago";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id_pago', $id_pago);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }

        public function obtenerIngresosMensuales($meses = 12) {
            try {
                // Consulta para SQL Server
                $sql = "SELECT 
                    FORMAT(fecha_pago, 'yyyy-MM') as mes,
                    SUM(monto) as total
                    FROM Pagos
                    WHERE fecha_pago >= DATEADD(month, -:meses, GETDATE())
                    AND estado = 'approved'
                    GROUP BY FORMAT(fecha_pago, 'yyyy-MM')
                    ORDER BY mes ASC";
                
                $stmt = $this->db->prepare($sql);
                $stmt->bindParam(':meses', $meses, PDO::PARAM_INT);
                $stmt->execute();
                
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                return [];
            }
        }
    }
?>