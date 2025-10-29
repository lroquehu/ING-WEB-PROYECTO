-- ============================
-- CREACIÓN DE BASE DE DATOS
-- ============================
DROP DATABASE IF EXISTS uniemprende;
CREATE DATABASE uniemprende;
USE uniemprende;

-- ============================
-- TABLA: Usuarios
-- ============================
CREATE TABLE Usuarios (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nombres VARCHAR(100) NOT NULL,
    apellidos VARCHAR(100) NOT NULL,
    dni CHAR(8) UNIQUE NOT NULL,
    telefono VARCHAR(15),
    correo_institucional VARCHAR(150) UNIQUE NOT NULL,
    codigo_univ VARCHAR(20) UNIQUE NOT NULL,
    facultad VARCHAR(100),
    escuela VARCHAR(100),
    contrasena VARCHAR(255) NOT NULL,
    estado TINYINT DEFAULT 1, -- 1=activo, 0=inactivo
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_correo (correo_institucional),
    INDEX idx_estado (estado)
);

-- ============================
-- TABLA: Categorías
-- ============================
CREATE TABLE Categorias (
    id_categoria INT AUTO_INCREMENT PRIMARY KEY,
    nombre_categoria VARCHAR(100) NOT NULL UNIQUE,
    descripcion TEXT,
    estado TINYINT DEFAULT 1
);

-- ============================
-- TABLA: Publicaciones
-- ============================
CREATE TABLE Publicaciones (
    id_publicacion INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    id_categoria INT NOT NULL,
    titulo VARCHAR(150) NOT NULL,
    descripcion TEXT,
    tipo ENUM('Producto','Servicio') NOT NULL,
    condicion ENUM('Nuevo','Usado','Como nuevo'), 
    precio DECIMAL(10,2) DEFAULT 0.00,
    telefono_contacto VARCHAR(15),
    correo_contacto VARCHAR(150),
    ubicacion VARCHAR(100),
    fecha_publicacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    estado TINYINT DEFAULT 1, -- 1=activo, 2=pausado, 3=eliminado
    FOREIGN KEY (id_usuario) REFERENCES Usuarios(id_usuario) ON DELETE CASCADE,
    FOREIGN KEY (id_categoria) REFERENCES Categorias(id_categoria) ON DELETE RESTRICT,
    INDEX idx_usuario (id_usuario),
    INDEX idx_categoria (id_categoria),
    INDEX idx_estado (estado),
    INDEX idx_fecha (fecha_publicacion)
);

-- ============================
-- TABLA: ImagenesPublicacion
-- ============================
CREATE TABLE ImagenesPublicacion (
    id_imagen INT AUTO_INCREMENT PRIMARY KEY,
    id_publicacion INT NOT NULL,
    url_imagen VARCHAR(255) NOT NULL,
    es_principal TINYINT DEFAULT 0, -- 0=no, 1=imagen principal
    FOREIGN KEY (id_publicacion) REFERENCES Publicaciones(id_publicacion) ON DELETE CASCADE,
    INDEX idx_publicacion (id_publicacion)
);

-- ============================
-- TABLA: Movimientos (historial)
-- ============================
CREATE TABLE Movimientos (
    id_movimiento INT AUTO_INCREMENT PRIMARY KEY,
    id_publicacion INT NOT NULL,
    id_usuario INT,
    tipo_movimiento ENUM('Alta','Edición','Eliminación','Pausa','Reactivación', 'Vista') NOT NULL,
    descripcion TEXT,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_publicacion) REFERENCES Publicaciones(id_publicacion) ON DELETE CASCADE,
    FOREIGN KEY (id_usuario) REFERENCES Usuarios(id_usuario) ON DELETE SET NULL,
    INDEX idx_fecha (fecha),
    INDEX idx_tipo (tipo_movimiento)
);

-- ============================
-- TABLA: Busquedas
-- ============================
CREATE TABLE Busquedas (
    id_busqueda INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT,
    termino VARCHAR(150) NOT NULL,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    contador INT DEFAULT 1,
    FOREIGN KEY (id_usuario) REFERENCES Usuarios(id_usuario) ON DELETE SET NULL,
    INDEX idx_termino (termino),
    INDEX idx_fecha (fecha)
);

-- Considerar agregar esta tabla para favoritos
CREATE TABLE Favoritos (
    id_favorito INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    id_publicacion INT NOT NULL,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_usuario) REFERENCES Usuarios(id_usuario) ON DELETE CASCADE,
    FOREIGN KEY (id_publicacion) REFERENCES Publicaciones(id_publicacion) ON DELETE CASCADE,
    UNIQUE KEY idx_usuario_publicacion (id_usuario, id_publicacion)
);

CREATE TABLE Sesiones (
    id_sesion VARCHAR(128) PRIMARY KEY,
    id_usuario INT NOT NULL,
    fecha_inicio TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_ultima_actividad TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    user_agent TEXT,
    ip_address VARCHAR(45),
    FOREIGN KEY (id_usuario) REFERENCES Usuarios(id_usuario) ON DELETE CASCADE
);

CREATE TABLE TokensRecuperacion (
    id_token INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    token VARCHAR(64) NOT NULL UNIQUE,
    expiracion DATETIME NOT NULL,
    utilizado TINYINT DEFAULT 0,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_usuario) REFERENCES Usuarios(id_usuario) ON DELETE CASCADE,
    INDEX idx_token (token),
    INDEX idx_expiracion (expiracion)
);

-- ============================
-- INSERCIÓN DE DATOS BÁSICOS
-- ============================

-- Insertar categorías por defecto
INSERT INTO Categorias (nombre_categoria, descripcion) VALUES
('Libros y Apuntes', 'Material de estudio, libros universitarios, apuntes'),
('Electrónica', 'Dispositivos electrónicos, laptops, tablets, smartphones'),
('Ropa y Accesorios', 'Ropa universitaria, accesorios, uniformes'),
('Hogar y Muebles', 'Artículos para el hogar, muebles, decoración'),
('Deportes', 'Artículos deportivos, equipamiento, ropa deportiva'),
('Transporte', 'Bicicletas, patinetas, accesorios de transporte'),
('Servicios', 'Tutorías, reparaciones, diseño, traducciones'),
('Otros', 'Otros productos y servicios');

-- Insertar usuario administrador (contraseña: admin123)
INSERT INTO Usuarios (nombres, apellidos, dni, telefono, correo_institucional, codigo_univ, facultad, escuela, contrasena) VALUES
('Admin', 'Sistema', '12345678', '999888777', 'admin@university.edu', 'ADMIN001', 'Sistemas', 'Ingeniería', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- ============================
-- FUNCIONES (ACTUALIZADAS)
-- ============================

-- ELIMINAR función fn_login insegura y usar PHP en su lugar
-- Esta función fue removida por seguridad

-- Contar publicaciones activas por usuario
DELIMITER //
CREATE FUNCTION fn_total_publicaciones(uid INT)
RETURNS INT
READS SQL DATA
DETERMINISTIC
BEGIN
    DECLARE total INT;
    SELECT COUNT(*) INTO total
    FROM Publicaciones
    WHERE id_usuario = uid AND estado = 1;
    RETURN IFNULL(total, 0);
END //
DELIMITER ;

-- ============================
-- PROCEDURES (ACTUALIZADOS)
-- ============================

-- Registrar usuario (MEJORADO - sin contraseña en texto plano)
DELIMITER //
CREATE PROCEDURE sp_registrar_usuario(
    IN p_nombres VARCHAR(100),
    IN p_apellidos VARCHAR(100),
    IN p_dni CHAR(8),
    IN p_telefono VARCHAR(15),
    IN p_correo VARCHAR(150),
    IN p_codigo VARCHAR(20),
    IN p_facultad VARCHAR(100),
    IN p_escuela VARCHAR(100),
    IN p_contrasena_hash VARCHAR(255)
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;
    
    START TRANSACTION;
    
    IF EXISTS (SELECT 1 FROM Usuarios WHERE correo_institucional = p_correo 
               OR dni = p_dni OR codigo_univ = p_codigo) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El usuario ya existe';
    END IF;
    
    INSERT INTO Usuarios(nombres, apellidos, dni, telefono, correo_institucional,
    codigo_univ, facultad, escuela, contraseña)
    VALUES(p_nombres, p_apellidos, p_dni, p_telefono, p_correo,
    p_codigo, p_facultad, p_escuela, p_contrasena_hash);
    
    COMMIT;
END //
DELIMITER ;

-- Crear publicación (MEJORADO)
DELIMITER //
CREATE PROCEDURE sp_crear_publicacion(
    IN p_id_usuario INT,
    IN p_id_categoria INT,
    IN p_titulo VARCHAR(150),
    IN p_descripcion TEXT,
    IN p_tipo ENUM('Producto','Servicio'),
    IN p_precio DECIMAL(10,2),
    IN p_telefono VARCHAR(15),
    IN p_correo VARCHAR(150)
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;
    
    START TRANSACTION;
    
    -- Verificar que el usuario existe y está activo
    IF NOT EXISTS (SELECT 1 FROM Usuarios WHERE id_usuario = p_id_usuario AND estado = 1) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Usuario no válido';
    END IF;
    
    -- Verificar que la categoría existe
    IF NOT EXISTS (SELECT 1 FROM Categorias WHERE id_categoria = p_id_categoria AND estado = 1) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Categoría no válida';
    END IF;
    
    INSERT INTO Publicaciones(id_usuario, id_categoria, titulo, descripcion,
                              tipo, precio, telefono_contacto, correo_contacto)
    VALUES(p_id_usuario, p_id_categoria, p_titulo, p_descripcion, p_tipo,
           p_precio, p_telefono, p_correo);
    
    COMMIT;
END //
DELIMITER ;

-- Buscar publicaciones (MEJORADO)
DELIMITER //
CREATE PROCEDURE sp_buscar_publicaciones(IN p_termino VARCHAR(150))
BEGIN
    SELECT p.id_publicacion, p.titulo, p.descripcion, p.tipo, p.precio,
           p.fecha_publicacion, u.nombres, u.apellidos, c.nombre_categoria,
           (SELECT url_imagen FROM ImagenesPublicacion 
            WHERE id_publicacion = p.id_publicacion 
            AND es_principal = 1 LIMIT 1) as imagen_principal
    FROM Publicaciones p
    JOIN Usuarios u ON p.id_usuario = u.id_usuario
    JOIN Categorias c ON p.id_categoria = c.id_categoria
    WHERE p.estado = 1 
      AND (p.titulo LIKE CONCAT('%', p_termino, '%') 
           OR p.descripcion LIKE CONCAT('%', p_termino, '%')
           OR c.nombre_categoria LIKE CONCAT('%', p_termino, '%'))
    ORDER BY p.fecha_publicacion DESC
    LIMIT 50;
END //
DELIMITER ;

-- ============================
-- TRIGGERS (ACTUALIZADOS)
-- ============================

-- Registrar movimiento cuando se inserta una publicación
DELIMITER //
CREATE TRIGGER trg_publicacion_insert
AFTER INSERT ON Publicaciones
FOR EACH ROW
BEGIN
    INSERT INTO Movimientos(id_publicacion, id_usuario, tipo_movimiento, descripcion)
    VALUES(NEW.id_publicacion, NEW.id_usuario, 'Alta', CONCAT('Publicación creada: ', NEW.titulo));
END //
DELIMITER ;

-- Registrar movimiento cuando se actualiza una publicación
DELIMITER //
CREATE TRIGGER trg_publicacion_update
AFTER UPDATE ON Publicaciones
FOR EACH ROW
BEGIN
    IF OLD.estado != NEW.estado THEN
        CASE NEW.estado
            WHEN 2 THEN
                INSERT INTO Movimientos(id_publicacion, id_usuario, tipo_movimiento, descripcion)
                VALUES(NEW.id_publicacion, NEW.id_usuario, 'Pausa', 'Publicación pausada');
            WHEN 1 THEN
                INSERT INTO Movimientos(id_publicacion, id_usuario, tipo_movimiento, descripcion)
                VALUES(NEW.id_publicacion, NEW.id_usuario, 'Reactivación', 'Publicación reactivada');
            WHEN 3 THEN
                INSERT INTO Movimientos(id_publicacion, id_usuario, tipo_movimiento, descripcion)
                VALUES(NEW.id_publicacion, NEW.id_usuario, 'Eliminación', 'Publicación eliminada');
        END CASE;
    ELSE
        INSERT INTO Movimientos(id_publicacion, id_usuario, tipo_movimiento, descripcion)
        VALUES(NEW.id_publicacion, NEW.id_usuario, 'Edición', CONCAT('Publicación editada: ', NEW.titulo));
    END IF;
END //
DELIMITER ;

-- Actualizar contador en búsquedas (MEJORADO)
DELIMITER //
CREATE TRIGGER trg_busqueda_insert
BEFORE INSERT ON Busquedas
FOR EACH ROW
BEGIN
    DECLARE v_id_busqueda INT;
    
    SELECT id_busqueda INTO v_id_busqueda
    FROM Busquedas
    WHERE termino = NEW.termino 
      AND (id_usuario = NEW.id_usuario OR (id_usuario IS NULL AND NEW.id_usuario IS NULL))
    LIMIT 1;

    IF v_id_busqueda IS NOT NULL THEN
        UPDATE Busquedas
        SET contador = contador + 1, fecha = NOW()
        WHERE id_busqueda = v_id_busqueda;
        SET NEW.id_busqueda = NULL; -- Evitar inserción duplicada
    END IF;
END //
DELIMITER ;