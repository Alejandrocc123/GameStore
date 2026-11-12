<?php
require_once __DIR__ . '/../config/conexion.php';

header('Content-Type: application/json; charset=utf-8');

// Soporte tanto para FormData tradicional como para peticiones JSON (fetch con application/json)
$inputJSON = file_get_contents('php://input');
$inputData = json_decode($inputJSON, true);
$inputData = is_array($inputData) ? $inputData : [];
$requestData = $_POST + $inputData;

$action = $requestData['action'] ?? ($_GET['action'] ?? '');

if (in_array($action, ['create', 'edit', 'delete', 'checkout', 'reportes'], true) && !$pdo) {
    http_response_code(503);
    echo json_encode(['success' => false, 'message' => 'No hay conexión con la base de datos.']);
    exit;
}

if ($action === 'create') {
    $title = $requestData['title'] ?? '';
    $sku = $requestData['sku'] ?? '';
    $genre = $requestData['genre'] ?? '';
    $platform = $requestData['platform'] ?? '';
    $price = $requestData['price'] ?? 0;
    $stock = $requestData['stock'] ?? 0;
    $status = $requestData['status'] ?? 'Disponible';
    $category = $requestData['category'] ?? 'videojuego';
    $image = 'img1.jpg';

    try {
        $stmt = $pdo->prepare("INSERT INTO videojuegos (title, sku, genre, platform, price, stock, status, category, image) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$title, $sku, $genre, $platform, $price, $stock, $status, $category, $image]);
        $newId = $pdo->lastInsertId();
        echo json_encode(['success' => true, 'id' => $newId, 'message' => 'Producto creado exitosamente.']);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

if ($action === 'edit') {
    $id = $requestData['id'] ?? 0;
    $title = $requestData['title'] ?? '';
    $sku = $requestData['sku'] ?? '';
    $genre = $requestData['genre'] ?? '';
    $platform = $requestData['platform'] ?? '';
    $price = $requestData['price'] ?? 0;
    $stock = $requestData['stock'] ?? 0;
    $status = $requestData['status'] ?? 'Disponible';

    try {
        $stmt = $pdo->prepare("UPDATE videojuegos SET title = ?, sku = ?, genre = ?, platform = ?, price = ?, stock = ?, status = ? WHERE id = ?");
        $stmt->execute([$title, $sku, $genre, $platform, $price, $stock, $status, $id]);
        echo json_encode(['success' => true, 'message' => 'Actualizado correctamente.']);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

if ($action === 'delete') {
    $id = $requestData['id'] ?? 0;

    try {
        $stmt = $pdo->prepare("DELETE FROM videojuegos WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode(['success' => true, 'message' => 'Eliminado de la base de datos.']);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

// ==================== CHECKOUT: registra cliente + venta + detalle y descuenta stock ====================
if ($action === 'checkout') {
    $clientEmail = strtolower(trim($inputData['client_email'] ?? ''));
    $items = $inputData['items'] ?? [];

    if (!filter_var($clientEmail, FILTER_VALIDATE_EMAIL)) {
        http_response_code(422);
        echo json_encode(['success' => false, 'message' => 'El correo del cliente no es válido.']);
        exit;
    }
    if (empty($items) || !is_array($items)) {
        http_response_code(422);
        echo json_encode(['success' => false, 'message' => 'El carrito está vacío.']);
        exit;
    }

    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("SELECT id FROM clientes WHERE email = ?");
        $stmt->execute([$clientEmail]);
        $clienteId = $stmt->fetchColumn();
        if (!$clienteId) {
            $stmt = $pdo->prepare("INSERT INTO clientes (email) VALUES (?)");
            $stmt->execute([$clientEmail]);
            $clienteId = (int)$pdo->lastInsertId();
        }

        $lineas = [];
        $total = 0;
        foreach ($items as $item) {
            $productId = (int)($item['id'] ?? 0);
            $qty = (int)($item['qty'] ?? 0);
            if ($productId <= 0 || $qty <= 0) {
                continue;
            }

            $stmt = $pdo->prepare("SELECT id, title, price, stock, category FROM videojuegos WHERE id = ? FOR UPDATE");
            $stmt->execute([$productId]);
            $p = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$p) {
                throw new Exception('El producto "' . ($item['title'] ?? $productId) . '" no existe en la base de datos.');
            }
            if ((int)$p['stock'] < $qty) {
                throw new Exception('Stock insuficiente para "' . $p['title'] . '" (disponible: ' . (int)$p['stock'] . ').');
            }

            $subtotal = (float)$p['price'] * $qty;
            $total += $subtotal;
            $lineas[] = [
                'id' => (int)$p['id'],
                'title' => $p['title'],
                'category' => $p['category'] ?: 'videojuego',
                'price' => (float)$p['price'],
                'qty' => $qty,
                'subtotal' => $subtotal,
                'new_stock' => (int)$p['stock'] - $qty,
            ];
        }

        if (empty($lineas)) {
            throw new Exception('No hay productos válidos en el carrito.');
        }

        $stmt = $pdo->prepare("INSERT INTO ventas (cliente_id, total) VALUES (?, ?)");
        $stmt->execute([$clienteId, $total]);
        $ventaId = (int)$pdo->lastInsertId();

        $stmtDetalle = $pdo->prepare("INSERT INTO detalle_ventas (venta_id, producto_id, titulo, categoria, precio_unit, cantidad, subtotal) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmtStock = $pdo->prepare("UPDATE videojuegos SET stock = ?, status = ? WHERE id = ?");

        foreach ($lineas as $l) {
            $stmtDetalle->execute([$ventaId, $l['id'], $l['title'], $l['category'], $l['price'], $l['qty'], $l['subtotal']]);
            $nuevoEstado = $l['new_stock'] <= 0 ? 'Agotado' : ($l['new_stock'] <= 4 ? 'Stock Bajo' : 'Disponible');
            $stmtStock->execute([$l['new_stock'], $nuevoEstado, $l['id']]);
        }

        $pdo->commit();
        echo json_encode(['success' => true, 'venta_id' => $ventaId, 'total' => $total, 'message' => 'Compra registrada correctamente.']);
    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

// ==================== REPORTES ====================
if ($action === 'reportes') {
    try {
        $resumen = $pdo->query("SELECT COUNT(*) AS pedidos, COALESCE(SUM(total), 0) AS ventas_netas FROM ventas")->fetch(PDO::FETCH_ASSOC);
        $pedidos = (int)$resumen['pedidos'];
        $ventasNetas = (float)$resumen['ventas_netas'];

        $stockTotal = (int)$pdo->query("SELECT COALESCE(SUM(stock), 0) FROM videojuegos")->fetchColumn();
        $stockCritico = (int)$pdo->query("SELECT COUNT(*) FROM videojuegos WHERE stock <= 4")->fetchColumn();

        $ventas = $pdo->query(
            "SELECT v.id, v.total, v.creado_en, c.email,
                    (SELECT COALESCE(SUM(d.cantidad), 0) FROM detalle_ventas d WHERE d.venta_id = v.id) AS unidades
             FROM ventas v
             JOIN clientes c ON c.id = v.cliente_id
             ORDER BY v.id DESC
             LIMIT 200"
        )->fetchAll(PDO::FETCH_ASSOC);

        $clientes = $pdo->query(
            "SELECT c.id, c.email, c.creado_en, COUNT(v.id) AS compras, COALESCE(SUM(v.total), 0) AS gastado
             FROM clientes c
             LEFT JOIN ventas v ON v.cliente_id = c.id
             GROUP BY c.id, c.email, c.creado_en
             ORDER BY gastado DESC, c.id DESC"
        )->fetchAll(PDO::FETCH_ASSOC);

        $porCategoria = $pdo->query(
            "SELECT categoria, SUM(cantidad) AS unidades, SUM(subtotal) AS ingresos
             FROM detalle_ventas
             GROUP BY categoria
             ORDER BY ingresos DESC"
        )->fetchAll(PDO::FETCH_ASSOC);

        $top = $pdo->query(
            "SELECT titulo, SUM(cantidad) AS unidades, SUM(subtotal) AS ingresos
             FROM detalle_ventas
             GROUP BY producto_id, titulo
             ORDER BY unidades DESC, ingresos DESC
             LIMIT 5"
        )->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            'success' => true,
            'resumen' => [
                'ventas_netas' => $ventasNetas,
                'pedidos' => $pedidos,
                'ticket_medio' => $pedidos > 0 ? round($ventasNetas / $pedidos) : 0,
                'stock_total' => $stockTotal,
                'stock_critico' => $stockCritico,
            ],
            'ventas' => $ventas,
            'clientes' => $clientes,
            'por_categoria' => $porCategoria,
            'top_productos' => $top,
        ]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

echo json_encode(['success' => false, 'message' => 'Acción no válida.']);