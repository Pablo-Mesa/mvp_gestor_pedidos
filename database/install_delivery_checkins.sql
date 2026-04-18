-- 1. Asegurar que 'users' use el motor InnoDB (necesario para llaves foráneas)
ALTER TABLE users ENGINE=InnoDB;

-- 2. Desactivar temporalmente la revisión de llaves para evitar el error #1824
SET FOREIGN_KEY_CHECKS = 0;

-- 3. Crear la tabla con definiciones explícitas de compatibilidad
CREATE TABLE IF NOT EXISTS delivery_checkins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    checkin_time DATETIME NOT NULL,
    lat DECIMAL(10, 7) NOT NULL,
    lng DECIMAL(10, 7) NOT NULL,
    distance_meters FLOAT NOT NULL,
    CONSTRAINT fk_user_checkin FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;