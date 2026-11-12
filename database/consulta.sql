USE gamestore_db;

SELECT v.id AS venta_id, cl.nombre AS cliente, v.fecha, v.total 
FROM venta v
JOIN cliente cl ON cl.id = v.cliente_id;
INSERT INTO categoria (nombre, descripcion) VALUES 
('Consolas', 'Videoconsolas de última generación'),
('Videojuegos', 'Juegos físicos y digitales'),
('Accesorios', 'Controles, diademas y periféricos');

INSERT INTO producto (categoria_id, nombre, precio, stock, plataforma_o_modelo) VALUES 
(1, 'PlayStation 5', 2499000.00, 10, 'Consola'),
(2, 'God of War Ragnarok', 279900.00, 15, 'PS5'),
(3, 'Control DualSense', 349900.00, 20, 'PS5');

-- 3. Insertar clientes
INSERT INTO cliente (nombre, correo, telefono) VALUES 
('Carlos Pérez', 'carlos.perez@correo.com', '3001234567'),
('Ana Gómez', 'ana.gomez@correo.com', '3109876543');

-- 4. Insertar ventas
INSERT INTO venta (cliente_id, fecha, total) VALUES 
(1, '2026-11-01 14:30:00', 249900.00),
(2, '2026-11-02 10:15:00', 2799900.00);

-- 5. Insertar detalles de venta
INSERT INTO detalle_venta (venta_id, producto_id, cantidad, subtotal) VALUES 
(1, 1, 1, 249900.00),
(2, 2, 1, 2799900.00);

-- 6. Insertar recomendaciones
INSERT INTO recomendacion (cliente_id, producto_id, mensaje) VALUES 
(1, 2, 'Te recomendamos este juego basado en tus compras recientes.');


-- ==========================================
-- CONSULTAS PARA TUS CAPTURAS (Ejecuta una a una)
-- ==========================================

-- 1. Cuadro de Categorías
SELECT * FROM categoria;

-- 2. Cuadro de Ventas con Nombre de Cliente (¡Este es el que muestra la compra detallada!)
SELECT v.id AS venta_id, cl.nombre AS cliente, v.fecha, v.total 
FROM venta v
JOIN cliente cl ON cl.id = v.cliente_id;

-- 3. Cuadro de Detalle de Venta con Productos
SELECT dv.id AS detalle_id, dv.venta_id, p.nombre AS producto, dv.cantidad, dv.subtotal 
FROM detalle_venta dv
JOIN producto p ON p.id = dv.producto_id;

-- 4. Cuadro de Recomendaciones
SELECT * FROM recomendacion;