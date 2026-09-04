CREATE DATABASE IF NOT EXISTS productos;
USE productos;

CREATE TABLE IF NOT EXISTS productos (
    id INT NOT NULL AUTO_INCREMENT,
    nombre VARCHAR(255) NOT NULL,
    descripcion TEXT NOT NULL,
    precio DECIMAL(10, 2) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO productos (nombre, descripcion, precio) VALUES
    ('Laptop Pro 14', 'Notebook ultraligera con 16GB RAM y SSD de 512GB', 1850000.00),
    ('Auriculares Wireless', 'Auriculares Bluetooth con cancelacion de ruido', 95000.00),
    ('Teclado Mecanico', 'Teclado mecanico RGB switch red', 78000.00);
