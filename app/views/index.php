<?php
require_once __DIR__ . '/../config/conexion.php';

function obtenerProductos($pdo) {
    $productos = [];
    try {
        if ($pdo) {
            $stmt = $pdo->query("SELECT * FROM videojuegos ORDER BY id ASC");
            $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    } catch (Exception $e) {
        $productos = [];
    }   

    // Catálogo maestro completo con imágenes garantizadas
    $catalogoCompleto = [
        ['id' => 1, 'title' => 'Elden Ring', 'sku' => 'ER-PS5-001', 'genre' => 'RPG de acción', 'platform' => 'PS5', 'price' => 280000, 'stock' => 15, 'status' => 'Disponible', 'category' => 'videojuego', 'image' => 'https://images.unsplash.com/photo-1550745165-9bc0b252726f?auto=format&fit=crop&w=500&q=80'],
        ['id' => 2, 'title' => 'God of War Ragnarök', 'sku' => 'GS-GOW-02', 'genre' => 'Aventura', 'platform' => 'PS5', 'price' => 320000, 'stock' => 8, 'status' => 'Disponible', 'category' => 'videojuego', 'image' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTxaomLlQURcy-ZeigFIcgLqQFjbAERdlT023c9RNKBMn81OmLkXcZbcxA&s=10'],
        ['id' => 3, 'title' => 'Zelda: Tears of the Kingdom', 'sku' => 'ZTK-SW-003', 'genre' => 'Aventura', 'platform' => 'Nintendo Switch', 'price' => 250000, 'stock' => 3, 'status' => 'Stock Bajo', 'category' => 'videojuego', 'image' => 'https://images.unsplash.com/photo-1618336753974-aae8e04506aa?auto=format&fit=crop&w=500&q=80'],
        ['id' => 4, 'title' => 'Hades', 'sku' => 'HAD-SW-005', 'genre' => 'Roguelike', 'platform' => 'PC', 'price' => 100000, 'stock' => 4, 'status' => 'Stock Bajo', 'category' => 'videojuego', 'image' => 'https://images.unsplash.com/photo-1538481199705-c710c4e965fc?auto=format&fit=crop&w=500&q=80'],
        ['id' => 5, 'title' => 'Hollow Knight', 'sku' => 'HK-SW-006', 'genre' => 'Metroidvania', 'platform' => 'PC', 'price' => 60000, 'stock' => 0, 'status' => 'Agotado', 'category' => 'videojuego', 'image' => 'https://images.unsplash.com/photo-1511512578047-dfb367046420?auto=format&fit=crop&w=500&q=80'],
        ['id' => 6, 'title' => 'Cyberpunk 2077', 'sku' => 'GS-CPK-06', 'genre' => 'RPG', 'platform' => 'Xbox Series X', 'price' => 200000, 'stock' => 12, 'status' => 'Disponible', 'category' => 'videojuego', 'image' => 'https://images.unsplash.com/photo-1542751371-adc38448a05e?auto=format&fit=crop&w=500&q=80'],
        
        ['id' => 7, 'title' => 'PlayStation 5 Slim 1TB', 'sku' => 'PS5-SLIM-01', 'genre' => 'Consola de Sobremesa', 'platform' => 'PlayStation', 'price' => 2699000, 'stock' => 7, 'status' => 'Disponible', 'category' => 'consola', 'image' => 'https://images.unsplash.com/photo-1606813907291-d86efa9b94db?auto=format&fit=crop&w=500&q=80'],
        ['id' => 8, 'title' => 'Nintendo Switch OLED Model', 'sku' => 'NSW-OLED-02', 'genre' => 'Consola Híbrida', 'platform' => 'Nintendo', 'price' => 1650000, 'stock' => 4, 'status' => 'Stock Bajo', 'category' => 'consola', 'image' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRa9d0VZUQjG5WRX137N4eI3_nkP-YhiHLyz60A8NChhw&s=10'],
        ['id' => 9, 'title' => 'Xbox Series X 1TB', 'sku' => 'XBSX-1TB-03', 'genre' => 'Consola de Sobremesa', 'platform' => 'Xbox', 'price' => 2799000, 'stock' => 0, 'status' => 'Agotado', 'category' => 'consola', 'image' => 'https://images.unsplash.com/photo-1621259182978-fbf93132d53d?auto=format&fit=crop&w=500&q=80'],
        
        ['id' => 10, 'title' => 'Control DualSense Wireless PS5', 'sku' => 'ACC-DS5-01', 'genre' => 'Mando Inalámbrico', 'platform' => 'PS5', 'price' => 349000, 'stock' => 20, 'status' => 'Disponible', 'category' => 'accesorio', 'image' => 'https://images.unsplash.com/photo-1600080972464-8e5f35f63d08?auto=format&fit=crop&w=500&q=80'],
        ['id' => 11, 'title' => 'Diadema Gamer Razer BlackShark V2', 'sku' => 'ACC-RZ-02', 'genre' => 'Audio / Diadema', 'platform' => 'Multiplataforma', 'price' => 380000, 'stock' => 11, 'status' => 'Disponible', 'category' => 'accesorio', 'image' => 'https://images.unsplash.com/photo-1546435770-a3e426bf472b?auto=format&fit=crop&w=500&q=80'],
        ['id' => 12, 'title' => 'Pro Controller Nintendo Switch', 'sku' => 'ACC-NSW-03', 'genre' => 'Mando Pro', 'platform' => 'Nintendo Switch', 'price' => 310000, 'stock' => 2, 'status' => 'Stock Bajo', 'category' => 'accesorio', 'image' => 'https://images.unsplash.com/photo-1531525645387-7f14be1bdbbd?auto=format&fit=crop&w=500&q=80']
    ];

    // Mapa robusto indexado por ID (entero y string) y por SKU normalizado
    $mapaImagenes = [];
    foreach ($catalogoCompleto as $item) {
        $mapaImagenes[(int)$item['id']] = $item['image'];
        $mapaImagenes[(string)$item['id']] = $item['image'];
        $mapaImagenes[strtolower(trim($item['sku']))] = $item['image'];
    }

    if (empty($productos)) {
        $productos = $catalogoCompleto;
    } else {
        foreach ($productos as &$p) {
            $idInt = (int)($p['id'] ?? 0);
            $skuKey = strtolower(trim($p['sku'] ?? ''));
            $titleKey = strtolower(trim($p['title'] ?? ''));

            // Clasificación limpia de categoría
            $cat = strtolower(trim($p['category'] ?? ''));
            if ($cat === 'consola' || strpos($titleKey, 'switch') !== false || strpos($titleKey, 'slim') !== false || strpos($titleKey, 'xbox') !== false || strpos($titleKey, 'playstation 5') !== false) {
                $p['category'] = 'consola';
            } elseif ($cat === 'accesorio' || strpos($titleKey, 'control') !== false || strpos($titleKey, 'diadema') !== false || strpos($titleKey, 'controller') !== false) {
                $p['category'] = 'accesorio';
            } else {
                $p['category'] = 'videojuego';
            }

            // Asignación con prioridad absoluta por ID numérico y SKU
            if (isset($mapaImagenes[$idInt])) {
                $p['image'] = $mapaImagenes[$idInt];
            } elseif (isset($mapaImagenes[$skuKey])) {
                $p['image'] = $mapaImagenes[$skuKey];
            } else {
                // Respaldo por ID directo o palabras clave
                if ($idInt === 2) {
                    $p['image'] = 'https://images.unsplash.com/photo-1612287233306-69255a408717?auto=format&fit=crop&w=500&q=80';
                } elseif ($idInt === 8) {
                    $p['image'] = 'https://images.unsplash.com/photo-1578303512597-81e6cc155b2e?auto=format&fit=crop&w=500&q=80';
                } elseif (strpos($titleKey, 'god of war') !== false) {
                    $p['image'] = 'https://images.unsplash.com/photo-1612287233306-69255a408717?auto=format&fit=crop&w=500&q=80';
                } elseif (strpos($titleKey, 'switch') !== false) {
                    $p['image'] = 'https://images.unsplash.com/photo-1578303512597-81e6cc155b2e?auto=format&fit=crop&w=500&q=80';
                } else {
                    $p['image'] = 'https://images.unsplash.com/photo-1550745165-9bc0b252726f?auto=format&fit=crop&w=500&q=80';
                }
            }
        }
        unset($p);
    }

    // Asegurar que estén todos los elementos del catálogo
    $skusExistentes = array_column($productos, 'sku');
    foreach ($catalogoCompleto as $itemDefault) {
        if (!in_array($itemDefault['sku'], $skusExistentes)) {
            $productos[] = $itemDefault;
        }
    }

    return $productos;
}

$todosLosProductos = obtenerProductos($pdo);

// Separación exacta para administrador y tienda
$games = array_filter($todosLosProductos, fn($p) => $p['category'] === 'videojuego');
$consolas = array_filter($todosLosProductos, fn($p) => $p['category'] === 'consola');
$accesorios = array_filter($todosLosProductos, fn($p) => $p['category'] === 'accesorio');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GameStore - Consola de Gestión y Tienda</title>
    <link rel="stylesheet" href="public/css/app.css">
</head>
<body>

    <!-- PANTALLA DE LOGIN -->
    <div id="login-screen" class="login-wrapper">
        <div class="login-left">
            <div class="brand">🎮 GameStore</div>
            <div class="hero-card">
                <span class="badge">EN TU CATÁLOGO</span>
                <h2>Elden Ring</h2>
                <p>PS5 · RPG de acción · Bandai Namco</p>
            </div>
            <div class="login-footer-left">
                <span>GameStore 2026 · Acceso privado de la tienda</span>
            </div>
        </div>
        <div class="login-right">
            <div class="login-card">
                <div class="login-header">
                    <span class="subtitle">ACCESO DE PERSONAL</span>
                    <h1>Bienvenida de nuevo</h1>
                    <p>Inicia sesión para gestionar tu tienda GameStore.</p>
                </div>

                <div id="login-alert" class="alert error hidden">
                    ⚠ El email o la contraseña no coinciden. Revisa tus datos.
                </div>

                <form id="login-form">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" placeholder="admin@gamestore.es o cliente" required>
                    </div>
                    <div class="form-group">
                        <div class="label-row">
                            <label for="password">Contraseña</label>
                            <a href="#" class="link-sm">Recuperar acceso</a>
                        </div>
                        <div class="password-input">
                            <input type="password" id="password" placeholder="Ingresa tu contraseña" required>
                            <button type="button" id="toggle-password">Mostrar</button>
                        </div>
                    </div>
                    <div class="form-row">
                        <label class="checkbox-label">
                            <input type="checkbox" checked> Recordar sesión
                        </label>
                    </div>
                    <button type="submit" class="btn btn-primary btn-full">→ Acceder a la tienda</button>
                </form>
                <div class="login-footer">
                    <span>🔒 Conexión segura · Personal y Clientes</span>
                    <p>Admin: <strong>admin@gamestore.es</strong> | Cliente: cualquier otro correo</p>
                </div>
            </div>
        </div>
    </div>

    <!-- PANEL DE ADMINISTRADOR -->
    <div id="admin-screen" class="app-container hidden">
        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="logo">🎮 GameStore</div>
                <div class="workspace-tag">ESPACIO DE TRABAJO<br><strong>Tienda Bogotá - COL</strong></div>
            </div>
            <nav class="sidebar-nav">
                <a href="#" class="nav-item active" data-target="section-dashboard">📊 Dashboard</a>
                <a href="#" class="nav-item" data-target="section-games">🎮 Videojuegos <span class="badge-count" id="total-games-badge"><?php echo count($games); ?></span></a>
                <a href="#" class="nav-item" data-target="section-consoles">🕹️ Consolas <span class="badge-count" id="total-consoles-badge"><?php echo count($consolas); ?></span></a>
                <a href="#" class="nav-item" data-target="section-accessories">🎧 Accesorios <span class="badge-count" id="total-accessories-badge"><?php echo count($accesorios); ?></span></a>
                <a href="#" class="nav-item" data-target="section-clients">👥 Clientes</a>
                <a href="#" class="nav-item" data-target="section-sales">💰 Ventas</a>
                <a href="#" class="nav-item" data-target="section-reports">📈 Reportes</a>
            </nav>
            <div class="sidebar-footer">
                <a href="#" class="nav-item">❓ Centro de ayuda</a>
                <a href="#" id="logout-admin" class="nav-item text-danger">→ Salir</a>
                <div class="app-version">GameStore Gestión v2.4</div>
            </div>
        </aside>

        <main class="main-content">
            <header class="main-header">
                <div class="search-box">
                    <span>🔍</span>
                    <input type="text" placeholder="Buscar productos o clientes...">
                </div>
                <div class="user-profile">
                    <div class="avatar">AC</div>
                    <div class="user-info">
                        <span class="user-name">Alejandro Castro</span>
                        <span class="user-role">Administrador</span>
                    </div>
                </div>
            </header>

            <div class="content-body">
                <!-- DASHBOARD -->
                <section id="section-dashboard" class="content-section active">
                    <div class="section-title-row">
                        <div>
                            <h1>Dashboard</h1>
                            <p>Hola, Alejandro. Este es el resumen de tu tienda. <span>26 oct - 1 nov 2026</span></p>
                        </div>
                    </div>

                    <div class="gamestore-feature-card">
                        <div class="feature-icon">🎮</div>
                        <div class="feature-content">
                            <h3>GameStore – Tienda de Videojuegos</h3>
                            <p><strong>Administración de videojuegos, consolas, accesorios, clientes y ventas.</strong></p>
                            <p class="feature-sub">Recomendaciones de productos y análisis de ventas por categoría.</p>
                        </div>
                    </div>

<div class="metrics-grid">
    <div class="metric-card">
        <span>Ventas netas</span>
        <h2 id="m-ventas">$ 0 COP</h2>
        <span class="trend">Acumulado de todas las ventas</span>
    </div>
    <div class="metric-card">
        <span>Ventas / Pedidos</span>
        <h2 id="m-pedidos">0</h2>
        <span class="trend">Pedidos registrados</span>
    </div>
    <div class="metric-card">
        <span>Unidades en stock</span>
        <h2 id="m-stock">0</h2>
        <span class="trend alert-text" id="m-critico"></span>
    </div>
    <div class="metric-card">
        <span>Ticket medio</span>
        <h2 id="m-ticket">$ 0 COP</h2>
        <span class="trend">Promedio por pedido</span>
    </div>
</div>
                </section>

                <!-- VIDEOJUEGOS -->
                <section id="section-games" class="content-section">
                    <div class="section-title-row">
                        <div>
                            <h1>Videojuegos</h1>
                            <p>Controla tu catálogo de videojuegos.</p>
                        </div>
                        <button class="btn btn-primary" onclick="openCreateModal('videojuego')">+ Nuevo videojuego</button>
                    </div>

                    <div class="games-admin-list" id="games-table-body">
                        <?php foreach($games as $game): ?>
                        <div class="game-admin-card" data-id="<?php echo $game['id']; ?>">
                            <div class="game-admin-thumb" style="background: #131722; display: flex; align-items: center; justify-content: center;">
                                <span style="font-size: 0.65rem; color: var(--text-muted);">[ Imagen ]</span>
                            </div>
                            <div class="game-admin-info">
                                <h3 class="g-title"><?php echo htmlspecialchars($game['title']); ?></h3>
                                <span class="sku g-sku"><?php echo htmlspecialchars($game['sku']); ?> · <span class="g-genre"><?php echo htmlspecialchars($game['genre']); ?></span> (<span class="g-platform"><?php echo htmlspecialchars($game['platform']); ?></span>)</span>
                            </div>
                            <div class="game-admin-price-stock">
                                <span class="g-price price-highlight">$ <?php echo number_format($game['price'], 0, ',', '.'); ?> COP</span>
                                <span class="stock-text">Stock: <strong class="g-stock"><?php echo $game['stock']; ?> uds.</strong></span>
                            </div>
                            <div class="game-admin-status">
                                <span class="badge-status <?php echo strtolower(str_replace(' ', '-', $game['status'])); ?> g-status"><?php echo htmlspecialchars($game['status']); ?></span>
                            </div>
                            <div class="game-admin-actions">
                                <button class="btn-action-link text-primary" onclick="editGame(<?php echo $game['id']; ?>, '<?php echo addslashes($game['title']); ?>', '<?php echo $game['sku']; ?>', '<?php echo addslashes($game['genre']); ?>', '<?php echo addslashes($game['platform']); ?>', <?php echo $game['price']; ?>, <?php echo $game['stock']; ?>, '<?php echo $game['status']; ?>')">Editar</button>
                                <button class="btn-action-link text-danger" onclick="confirmDelete(<?php echo $game['id']; ?>, '<?php echo addslashes($game['title']); ?>', '<?php echo $game['sku']; ?>')">Eliminar</button>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </section>

                <!-- CONSOLAS -->
                <section id="section-consoles" class="content-section">
                    <div class="section-title-row">
                        <div>
                            <h1>Consolas</h1>
                            <p>Administración de consolas de última generación.</p>
                        </div>
                        <button class="btn btn-primary" onclick="openCreateModal('consola')">+ Nueva consola</button>
                    </div>

                    <div class="games-admin-list" id="consoles-table-body">
                        <?php foreach($consolas as $consola): ?>
                        <div class="game-admin-card" data-id="<?php echo $consola['id']; ?>">
                            <div class="game-admin-thumb" style="background: #131722; display: flex; align-items: center; justify-content: center;">
                                <span style="font-size: 0.65rem; color: var(--text-muted);">[ Consola ]</span>
                            </div>
                            <div class="game-admin-info">
                                <h3 class="g-title"><?php echo htmlspecialchars($consola['title']); ?></h3>
                                <span class="sku g-sku"><?php echo htmlspecialchars($consola['sku']); ?> · <span class="g-genre"><?php echo htmlspecialchars($consola['genre']); ?></span> (<span class="g-platform"><?php echo htmlspecialchars($consola['platform']); ?></span>)</span>
                            </div>
                            <div class="game-admin-price-stock">
                                <span class="g-price price-highlight">$ <?php echo number_format($consola['price'], 0, ',', '.'); ?> COP</span>
                                <span class="stock-text">Stock: <strong class="g-stock"><?php echo $consola['stock']; ?> uds.</strong></span>
                            </div>
                            <div class="game-admin-status">
                                <span class="badge-status <?php echo strtolower(str_replace(' ', '-', $consola['status'])); ?> g-status"><?php echo htmlspecialchars($consola['status']); ?></span>
                            </div>
                            <div class="game-admin-actions">
                                <button class="btn-action-link text-primary" onclick="editGame(<?php echo $consola['id']; ?>, '<?php echo addslashes($consola['title']); ?>', '<?php echo $consola['sku']; ?>', '<?php echo addslashes($consola['genre']); ?>', '<?php echo addslashes($consola['platform']); ?>', <?php echo $consola['price']; ?>, <?php echo $consola['stock']; ?>, '<?php echo $consola['status']; ?>')">Editar</button>
                                <button class="btn-action-link text-danger" onclick="confirmDelete(<?php echo $consola['id']; ?>, '<?php echo addslashes($consola['title']); ?>', '<?php echo $consola['sku']; ?>')">Eliminar</button>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </section>

                <!-- ACCESORIOS -->
                <section id="section-accessories" class="content-section">
                    <div class="section-title-row">
                        <div>
                            <h1>Accesorios</h1>
                            <p>Gestión de controles, diademas y accesorios gamer.</p>
                        </div>
                        <button class="btn btn-primary" onclick="openCreateModal('accesorio')">+ Nuevo accesorio</button>
                    </div>

                    <div class="games-admin-list" id="accessories-table-body">
                        <?php foreach($accesorios as $acc): ?>
                        <div class="game-admin-card" data-id="<?php echo $acc['id']; ?>">
                            <div class="game-admin-thumb" style="background: #131722; display: flex; align-items: center; justify-content: center;">
                                <span style="font-size: 0.65rem; color: var(--text-muted);">[ Accesorio ]</span>
                            </div>
                            <div class="game-admin-info">
                                <h3 class="g-title"><?php echo htmlspecialchars($acc['title']); ?></h3>
                                <span class="sku g-sku"><?php echo htmlspecialchars($acc['sku']); ?> · <span class="g-genre"><?php echo htmlspecialchars($acc['genre']); ?></span> (<span class="g-platform"><?php echo htmlspecialchars($acc['platform']); ?></span>)</span>
                            </div>
                            <div class="game-admin-price-stock">
                                <span class="g-price price-highlight">$ <?php echo number_format($acc['price'], 0, ',', '.'); ?> COP</span>
                                <span class="stock-text">Stock: <strong class="g-stock"><?php echo $acc['stock']; ?> uds.</strong></span>
                            </div>
                            <div class="game-admin-status">
                                <span class="badge-status <?php echo strtolower(str_replace(' ', '-', $acc['status'])); ?> g-status"><?php echo htmlspecialchars($acc['status']); ?></span>
                            </div>
                            <div class="game-admin-actions">
                                <button class="btn-action-link text-primary" onclick="editGame(<?php echo $acc['id']; ?>, '<?php echo addslashes($acc['title']); ?>', '<?php echo $acc['sku']; ?>', '<?php echo addslashes($acc['genre']); ?>', '<?php echo addslashes($acc['platform']); ?>', <?php echo $acc['price']; ?>, <?php echo $acc['stock']; ?>, '<?php echo $acc['status']; ?>')">Editar</button>
                                <button class="btn-action-link text-danger" onclick="confirmDelete(<?php echo $acc['id']; ?>, '<?php echo addslashes($acc['title']); ?>', '<?php echo $acc['sku']; ?>')">Eliminar</button>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </section>

<section id="section-clients" class="content-section">
    <div class="section-title-row">
        <div><h1>Clientes</h1><p>Base de datos de compradores.</p></div>
    </div>
    <div class="table-wrap">
        <table class="data-table">
            <thead><tr><th>Email</th><th>Compras</th><th>Total gastado</th><th>Cliente desde</th></tr></thead>
            <tbody id="clients-body"><tr><td colspan="4">Cargando...</td></tr></tbody>
        </table>
    </div>
</section>

<section id="section-sales" class="content-section">
    <div class="section-title-row">
        <div><h1>Ventas</h1><p>Control de transacciones.</p></div>
    </div>
    <div class="table-wrap">
        <table class="data-table">
            <thead><tr><th>Pedido</th><th>Fecha</th><th>Cliente</th><th>Unidades</th><th>Total</th></tr></thead>
            <tbody id="sales-body"><tr><td colspan="5">Cargando...</td></tr></tbody>
        </table>
    </div>
</section>

<section id="section-reports" class="content-section">
    <div class="section-title-row">
        <div><h1>Reportes</h1><p>Analíticas de ventas.</p></div>
    </div>

    <h3 class="table-title">Ventas por categoría</h3>
    <div class="table-wrap">
        <table class="data-table">
            <thead><tr><th>Categoría</th><th>Unidades vendidas</th><th>Ingresos</th></tr></thead>
            <tbody id="reports-cat-body"><tr><td colspan="3">Cargando...</td></tr></tbody>
        </table>
    </div>

    <h3 class="table-title">Top 5 productos más vendidos</h3>
    <div class="table-wrap">
        <table class="data-table">
            <thead><tr><th>Producto</th><th>Unidades vendidas</th><th>Ingresos</th></tr></thead>
            <tbody id="reports-top-body"><tr><td colspan="3">Cargando...</td></tr></tbody>
        </table>    
    </div>
</section>
            </div>
        </main>
    </div>

<!-- VISTA DE TIENDA PÚBLICA (CLIENTE E-COMMERCE CON FILTROS POR CATEGORÍA) -->
    <div id="store-screen" class="app-container hidden" style="flex-direction: column;">
        <header class="store-header">
            <div class="store-brand">🎮 GameStore <span>Colombia</span></div>
            <div class="store-nav-right">
                <span id="user-display" class="user-pill"></span>
                <button id="cart-btn" class="btn btn-primary cart-trigger">🛒 Carrito <span id="cart-count" class="cart-badge">0</span></button>
                <button id="logout-store" class="btn btn-danger-soft">Salir</button>
            </div>
        </header>

        <main class="store-main-content">
            <div class="store-hero-banner">
                <h1>Explora el universo Gamer</h1>
                <p>Videojuegos, consolas y accesorios con entrega inmediata en Colombia.</p>
            </div>

            <!-- BARRA DE FILTROS POR CATEGORÍA PARA EL COMPRADOR -->
            <div class="store-categories-filter" style="display: flex; gap: 12px; margin: 20px 0; justify-content: center;">
                <button class="btn btn-primary btn-sm store-filter-btn active" data-filter="all" onclick="filterStoreCategory('all', this)">Todos</button>
                <button class="btn btn-secondary btn-sm store-filter-btn" data-filter="videojuego" onclick="filterStoreCategory('videojuego', this)">🎮 Videojuegos</button>
                <button class="btn btn-secondary btn-sm store-filter-btn" data-filter="consola" onclick="filterStoreCategory('consola', this)">🕹️ Consolas</button>
                <button class="btn btn-secondary btn-sm store-filter-btn" data-filter="accesorio" onclick="filterStoreCategory('accesorio', this)">🎧 Accesorios</button>
            </div>

<div class="store-grid" id="store-products-grid">
    <?php foreach($todosLosProductos as $producto): ?>
    <div class="product-card" data-category="<?php echo $producto['category']; ?>" data-store-id="<?php echo $producto['id']; ?>">
        <div class="product-image-container" style="background: #131722; height: 150px; position: relative; overflow: hidden;">
            <img src="<?php echo htmlspecialchars($producto['image']); ?>" alt="<?php echo htmlspecialchars($producto['title']); ?>" style="width: 100%; height: 100%; object-fit: cover;">
            <span class="product-platform-badge s-platform"><?php echo htmlspecialchars($producto['platform']); ?></span>
        </div>
        <div class="product-details">
            <span class="product-genre s-genre"><?php echo htmlspecialchars($producto['genre']); ?></span>
            <h3 class="product-title s-title"><?php echo htmlspecialchars($producto['title']); ?></h3>
            <p class="product-sku-stock">SKU: <span class="s-sku"><?php echo htmlspecialchars($producto['sku']); ?></span> | Stock: <strong class="s-stock"><?php echo $producto['stock']; ?></strong></p>
            <div class="product-footer-card">
                <span class="product-price s-price" data-raw-price="<?php echo $producto['price']; ?>">$ <?php echo number_format($producto['price'], 0, ',', '.'); ?> COP</span>
                <button class="btn btn-primary btn-sm" onclick="addToCart(<?php echo $producto['id']; ?>, '<?php echo addslashes($producto['title']); ?>', <?php echo $producto['price']; ?>)">Añadir</button>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>
        </main>
    </div>
    <!-- FIN DE LA VISTA DE TIENDA: los modales van fuera para que también se vean desde el panel admin -->

    <!-- MODAL DE CREACIÓN DE PRODUCTO -->
    <div id="create-modal" class="modal-overlay hidden">
        <div class="modal-card" style="max-width: 480px; text-align: left;">
            <h3 id="create-modal-title">➕ Nuevo Producto</h3>
            <p>Agrega un artículo al catálogo de la base de datos.</p>
            <form id="create-game-form">
                <input type="hidden" id="create-category" value="videojuego">
                <div class="form-group">
                    <label>Título / Nombre</label>
                    <input type="text" id="create-title" required>
                </div>
                <div class="form-group">
                    <label>SKU</label>
                    <input type="text" id="create-sku" required>
                </div>
                <div class="form-group">
                    <label>Género / Tipo</label>
                    <input type="text" id="create-genre" required>
                </div>
                <div class="form-group">
                    <label>Plataforma / Marca</label>
                    <input type="text" id="create-platform" required>
                </div>
                <div class="form-group">
                    <label>Precio (COP)</label>
                    <input type="number" id="create-price" required>
                </div>
                <div class="form-group">
                    <label>Stock (uds.)</label>
                    <input type="number" id="create-stock" required>
                </div>
                <div class="form-group">
                    <label>Estado</label>
                    <select id="create-status" style="width: 100%; padding: 12px; background: var(--bg-main); border: 1px solid var(--border); border-radius: 8px; color: #fff;">
                        <option value="Disponible">Disponible</option>
                        <option value="Stock Bajo">Stock Bajo</option>
                        <option value="Agotado">Agotado</option>
                    </select>
                </div>
                <div class="modal-actions" style="margin-top: 20px;">
                    <button type="button" class="btn btn-secondary" onclick="closeCreateModal()">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar producto</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL DE EDICIÓN DE PRODUCTO -->
    <div id="edit-modal" class="modal-overlay hidden">
        <div class="modal-card" style="max-width: 480px; text-align: left;">
            <h3>✏️ Editar Producto</h3>
            <p>Modifica los datos del producto. Se guardarán en la base de datos.</p>
            <form id="edit-game-form">
                <input type="hidden" id="edit-id">
                <div class="form-group">
                    <label>Título</label>
                    <input type="text" id="edit-title" required>
                </div>
                <div class="form-group">
                    <label>SKU</label>
                    <input type="text" id="edit-sku" required>
                </div>
                <div class="form-group">
                    <label>Género / Tipo</label>
                    <input type="text" id="edit-genre" required>
                </div>
                <div class="form-group">
                    <label>Plataforma / Marca</label>
                    <input type="text" id="edit-platform" required>
                </div>
                <div class="form-group">
                    <label>Precio (COP)</label>
                    <input type="number" id="edit-price" required>
                </div>
                <div class="form-group">
                    <label>Stock (uds.)</label>
                    <input type="number" id="edit-stock" required>
                </div>
                <div class="form-group">
                    <label>Estado</label>
                    <select id="edit-status" style="width: 100%; padding: 12px; background: var(--bg-main); border: 1px solid var(--border); border-radius: 8px; color: #fff;">
                        <option value="Disponible">Disponible</option>
                        <option value="Stock Bajo">Stock Bajo</option>
                        <option value="Agotado">Agotado</option>
                    </select>
                </div>
                <div class="modal-actions" style="margin-top: 20px;">
                    <button type="button" class="btn btn-secondary" onclick="closeEditModal()">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar cambios</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL DE ELIMINACIÓN -->
    <div id="delete-modal" class="modal-overlay hidden">
        <div class="modal-card">
            <h3 id="delete-title">¿Eliminar producto?</h3>
            <p id="delete-desc">Se eliminará del catálogo de forma permanente en la base de datos.</p>
            <div class="modal-actions">
                <button class="btn btn-secondary" onclick="closeModal()">Cancelar</button>
                <button class="btn btn-danger" onclick="executeDelete()">Eliminar</button>
            </div>
        </div>
    </div>

    <!-- MODAL DE CARRITO -->
    <div id="cart-modal" class="modal-overlay hidden">
        <div class="modal-card" style="max-width: 480px; text-align: left;">
            <h3 style="margin-bottom: 15px;">🛒 Tu Carrito de Compras</h3>
            <div id="cart-items" style="margin: 15px 0; max-height: 280px; overflow-y: auto; color: #a0aec0;">
                <p style="text-align: center; padding: 20px;">El carrito está vacío.</p>
            </div>
            <div style="display: flex; justify-content: space-between; font-weight: bold; color: #fff; margin: 20px 0; font-size: 1.1rem; border-top: 1px solid var(--border); padding-top: 15px;">
                <span>Total a pagar:</span>
                <span id="cart-total">$ 0 COP</span>
            </div>
            <div class="modal-actions">
                <button class="btn btn-secondary" onclick="closeCart()">Seguir comprando</button>
                <button class="btn btn-primary" onclick="checkout()">Finalizar compra</button>
            </div>
        </div>
    </div>

 <script src="public/js/app.js"></script>
 <script src="public/js/reportes.js"></script>

</body>
</html>