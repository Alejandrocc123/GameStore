<?php
require_once 'conexion.php';

header('Content-Type: application/json');

// Soporte tanto para FormData tradicional como para peticiones JSON (fetch con application/json)
$inputJSON = file_get_contents('php://input');
$inputData = json_decode($inputJSON, true);
$inputData = is_array($inputData) ? $inputData : [];
$requestData = $_POST + $inputData;

$action = $requestData['action'] ?? ($_GET['action'] ?? '');

if (in_array($action, ['create', 'edit', 'delete'], true) && !$pdo) {
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

if ($action === 'checkout') {
    try {
        $clientEmail = $inputData['client_email'] ?? 'cliente@gamestore.es';
        $items = $inputData['items'] ?? [];

        if (empty($items)) {
            echo json_encode(['success' => false, 'message' => 'El carrito está vacío.']);
            exit;
        }

        if ($pdo) {
            // Descontar el stock de los productos comprados en la base de datos
            foreach ($items as $item) {
                $productId = $item['id'];
                $qtyBought = $item['qty'];

                $stmt = $pdo->prepare("UPDATE videojuegos SET stock = GREATEST(0, stock - ?) WHERE id = ?");
                $stmt->execute([$qtyBought, $productId]);
            }
        }

        echo json_encode(['success' => true, 'message' => 'Compra procesada y stock actualizado correctamente.']);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

echo json_encode(['success' => false, 'message' => 'Acción no válida.']);