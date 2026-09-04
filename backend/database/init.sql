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
    ('Teclado Mecanico', 'Teclado mecanico RGB switch red', 78000.00),
    ('Mouse Optico', 'Mouse ergonomico con sensor de alta precision', 25000.00),
    ('Monitor 27 4K', 'Monitor IPS 27 pulgadas resolucion 4K', 420000.00),
    ('Webcam HD', 'Camara web Full HD con microfono integrado', 55000.00),
    ('Silla Gamer', 'Silla ergonoma con soporte lumbar ajustable', 310000.00),
    ('Disco SSD 1TB', 'Unidad de estado solido NVMe 1TB', 145000.00),
    ('Hub USB-C', 'Hub multipuerto USB-C con HDMI y Ethernet', 68000.00),
    ('Lampara LED Desk', 'Lampara de escritorio LED con brazo flexible', 32000.00);
