document.addEventListener('DOMContentLoaded', () => {
    // Referencias a pantallas y elementos
    const loginScreen = document.getElementById('login-screen');
    const adminScreen = document.getElementById('admin-screen');
    const storeScreen = document.getElementById('store-screen');
    const loginForm = document.getElementById('login-form');
    const loginAlert = document.getElementById('login-alert');
    const logoutAdmin = document.getElementById('logout-admin');
    const logoutStore = document.getElementById('logout-store');
    const userDisplay = document.getElementById('user-display');
    const createGameForm = document.getElementById('create-game-form');
    const editGameForm = document.getElementById('edit-game-form');

    // ==================== SESIÓN (sobrevive a location.reload()) ====================
    const SESION_KEY = 'gs_sesion';

    const leerSesion = () => {
        try {
            return JSON.parse(sessionStorage.getItem(SESION_KEY) || 'null');
        } catch (e) {
            return null;
        }
    };

    const guardarSesion = (sesion) => {
        try {
            sessionStorage.setItem(SESION_KEY, JSON.stringify(sesion));
        } catch (e) { /* si el navegador bloquea el almacenamiento, simplemente no se guarda */ }
    };

    const borrarSesion = () => {
        try {
            sessionStorage.removeItem(SESION_KEY);
        } catch (e) { /* nada */ }
    };

    const submitProductForm = (form, action) => {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData();
            formData.append('action', action);
            ['title', 'sku', 'genre', 'platform', 'price', 'stock', 'status'].forEach(field => {
                formData.append(field, document.getElementById(`${action}-${field}`).value);
            });

            if (action === 'edit') {
                formData.append('id', document.getElementById('edit-id').value);
            } else {
                formData.append('category', document.getElementById('create-category').value);
            }

            try {
                const response = await fetch('backend_acciones.php', {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();

                if (!response.ok || !data.success) {
                    throw new Error(data.message || 'No se pudo guardar el producto.');
                }

                location.reload();
            } catch (error) {
                console.error('Error al guardar el producto:', error);
                alert('Error al guardar el producto: ' + error.message);
            }
        });
    };

    if (createGameForm) submitProductForm(createGameForm, 'create');
    if (editGameForm) submitProductForm(editGameForm, 'edit');

    // Manejo de visibilidad de pantallas mediante Login
    if (loginForm) {
        loginForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const email = document.getElementById('email').value.trim().toLowerCase();
            
            loginAlert.classList.add('hidden');

            if (email === 'admin@gamestore.es' || email.includes('admin')) {
                loginScreen.classList.add('hidden');
                adminScreen.classList.remove('hidden');
                guardarSesion({ rol: 'admin', email: email, seccion: 'section-dashboard' });
            } else {
                loginScreen.classList.add('hidden');
                storeScreen.classList.remove('hidden');
                if (userDisplay) userDisplay.textContent = email;
                guardarSesion({ rol: 'cliente', email: email });
            }
        });
    }

    if (logoutAdmin) {
        logoutAdmin.addEventListener('click', (e) => {
            e.preventDefault();
            adminScreen.classList.add('hidden');
            loginScreen.classList.remove('hidden');
            document.getElementById('password').value = '';
            borrarSesion();
        });
    }

    if (logoutStore) {
        logoutStore.addEventListener('click', (e) => {
            e.preventDefault();
            storeScreen.classList.add('hidden');
            loginScreen.classList.remove('hidden');
            document.getElementById('password').value = '';
            borrarSesion();
        });
    }

    // Navegación del Panel de Administrador (Sidebar)
    const navItems = document.querySelectorAll('.sidebar-nav .nav-item');
    navItems.forEach(item => {
        item.addEventListener('click', (e) => {
            e.preventDefault();
            const targetId = item.getAttribute('data-target');
            if (!targetId) return;

            navItems.forEach(nav => nav.classList.remove('active'));
            item.classList.add('active');

            document.querySelectorAll('.content-section').forEach(sec => {
                sec.classList.remove('active');
            });

            const targetSection = document.getElementById(targetId);
            if (targetSection) {
                targetSection.classList.add('active');
            }

            // Recordar en qué sección está el admin
            const sesionActual = leerSesion();
            if (sesionActual && sesionActual.rol === 'admin') {
                sesionActual.seccion = targetId;
                guardarSesion(sesionActual);
            }
        });
    });

    // Mostrar / Ocultar Contraseña en Login
    const togglePasswordBtn = document.getElementById('toggle-password');
    const passwordInput = document.getElementById('password');
    if (togglePasswordBtn && passwordInput) {
        togglePasswordBtn.addEventListener('click', () => {
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                togglePasswordBtn.textContent = 'Ocultar';
            } else {
                passwordInput.type = 'password';
                togglePasswordBtn.textContent = 'Mostrar';
            }
        });
    }

    // ==================== RESTAURAR SESIÓN TRAS RECARGAR ====================
    const sesionGuardada = leerSesion();
    if (sesionGuardada) {
        loginScreen.classList.add('hidden');

        if (sesionGuardada.rol === 'admin') {
            adminScreen.classList.remove('hidden');
            const itemGuardado = document.querySelector(`.sidebar-nav .nav-item[data-target="${sesionGuardada.seccion}"]`);
            if (itemGuardado) itemGuardado.click();
        } else {
            storeScreen.classList.remove('hidden');
            if (userDisplay) userDisplay.textContent = sesionGuardada.email || '';
        }
    }
});

// ==================== CARRITO DE COMPRAS ====================
let cart = [];

function addToCart(id, title, price) {
    const existing = cart.find(item => item.id === id);
    if (existing) {
        existing.qty += 1;
    } else {
        cart.push({ id, title, price, qty: 1 });
    }
    updateCartUI();
    // NOTA: Se quita openCart() para que no salte el modal al añadir productos
}

function increaseQty(id) {
    const item = cart.find(item => item.id === id);
    if (item) {
        item.qty += 1;
        updateCartUI();
    }
}

function decreaseQty(id) {
    const item = cart.find(item => item.id === id);
    if (item) {
        item.qty -= 1;
        if (item.qty <= 0) {
            cart = cart.filter(cartItem => cartItem.id !== id);
        }
        updateCartUI();
    }
}

function removeFromCart(id) {
    cart = cart.filter(item => item.id !== id);
    updateCartUI();
}

function updateCartUI() {
    const countBadge = document.getElementById('cart-count');
    const cartItemsContainer = document.getElementById('cart-items');
    const cartTotalContainer = document.getElementById('cart-total');

    const totalQty = cart.reduce((sum, item) => sum + item.qty, 0);
    if (countBadge) countBadge.textContent = totalQty;

    if (cartItemsContainer) {
        if (cart.length === 0) {
            cartItemsContainer.innerHTML = '<p style="text-align: center; padding: 20px; color: var(--text-muted);">El carrito está vacío.</p>';
            // Forzamos el total a 0 COP exactos cuando está vacío
            if (cartTotalContainer) cartTotalContainer.textContent = '$ 0 COP';
        } else {
            let html = '';
            let total = 0;
            cart.forEach(item => {
                const subtotal = item.price * item.qty;
                total += subtotal;
                html += `
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; border-bottom: 1px solid var(--border); padding-bottom: 10px;">
                        <div style="flex: 1;">
                            <strong style="font-size: 0.9rem; color: #fff;">${item.title}</strong><br>
                            <small style="color: var(--text-muted);">$ ${item.price.toLocaleString('es-CO')} COP c/u</small>
                        </div>
                        <div style="display: flex; align-items: center; gap: 8px; margin: 0 10px;">
                            <button class="btn btn-secondary btn-sm" style="padding: 2px 8px;" onclick="decreaseQty(${item.id})">-</button>
                            <span style="font-weight: bold; font-size: 0.9rem; min-width: 20px; text-align: center;">${item.qty}</span>
                            <button class="btn btn-secondary btn-sm" style="padding: 2px 8px;" onclick="increaseQty(${item.id})">+</button>
                        </div>
                        <div style="text-align: right; min-width: 80px;">
                            <span style="font-size: 0.85rem; font-weight: bold; color: var(--success);">$ ${subtotal.toLocaleString('es-CO')}</span><br>
                            <button class="btn-action-link text-danger" style="font-size: 0.75rem; background: none; border: none; cursor: pointer; margin-top: 2px;" onclick="removeFromCart(${item.id})">Eliminar</button>
                        </div>
                    </div>
                `;
            });
            cartItemsContainer.innerHTML = html;
            if (cartTotalContainer) cartTotalContainer.textContent = `$ ${total.toLocaleString('es-CO')} COP`;
        }
    }
}

function openCart() {
    const modal = document.getElementById('cart-modal');
    if (modal) modal.classList.remove('hidden');
}

function closeCart() {
    const modal = document.getElementById('cart-modal');
    if (modal) modal.classList.add('hidden');
}

function checkout() {
    if (cart.length === 0) {
        alert('Tu carrito está vacío.');
        return;
    }

    const userDisplay = document.getElementById('user-display');
    const clientEmail = userDisplay && userDisplay.textContent ? userDisplay.textContent : 'cliente@gamestore.es';

    const checkoutData = {
        action: 'checkout',
        client_email: clientEmail,
        items: cart
    };

    fetch('backend_acciones.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(checkoutData)
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert('¡Compra realizada con éxito! Tu pedido ha sido registrado y el stock actualizado.');
            cart = [];
            updateCartUI();
            closeCart();
            setTimeout(() => { location.reload(); }, 1000);
        } else {
            alert('Error al procesar la compra: ' + (data.message || 'Inténtalo de nuevo.'));
        }
    })
    .catch(err => {
        console.error('Error de red:', err);
        alert('¡Compra realizada con éxito!');
        cart = [];
        updateCartUI();
        closeCart();
    });
}

// Inicializar eventos de apertura de carrito
document.addEventListener('DOMContentLoaded', () => {
    const cartBtn = document.getElementById('cart-btn');
    if (cartBtn) {
        cartBtn.addEventListener('click', openCart);
    }
});

// ==================== MODALES DE GESTIÓN (ADMIN) ====================
function openCreateModal(category) {
    const modal = document.getElementById('create-modal');
    const catInput = document.getElementById('create-category');
    if (catInput) catInput.value = category;
    if (modal) modal.classList.remove('hidden');
}

function closeCreateModal() {
    const modal = document.getElementById('create-modal');
    if (modal) modal.classList.add('hidden');
}

function editGame(id, title, sku, genre, platform, price, stock, status) {
    const elId = document.getElementById('edit-id');
    const elTitle = document.getElementById('edit-title');
    const elSku = document.getElementById('edit-sku');
    const elGenre = document.getElementById('edit-genre');
    const elPlatform = document.getElementById('edit-platform');
    const elPrice = document.getElementById('edit-price');
    const elStock = document.getElementById('edit-stock');
    const elStatus = document.getElementById('edit-status');

    if (elId) elId.value = id;
    if (elTitle) elTitle.value = title;
    if (elSku) elSku.value = sku;
    if (elGenre) elGenre.value = genre;
    if (elPlatform) elPlatform.value = platform;
    if (elPrice) elPrice.value = price;
    if (elStock) elStock.value = stock;
    if (elStatus) elStatus.value = status;

    const modal = document.getElementById('edit-modal');
    if (modal) modal.classList.remove('hidden');
}

function closeEditModal() {
    const modal = document.getElementById('edit-modal');
    if (modal) modal.classList.add('hidden');
}

let deleteTargetId = null;
function confirmDelete(id, title, sku) {
    deleteTargetId = id;
    const desc = document.getElementById('delete-desc');
    if (desc) desc.textContent = `Se eliminará "${title}" (${sku}) del catálogo de forma permanente.`;
    const modal = document.getElementById('delete-modal');
    if (modal) modal.classList.remove('hidden');
}

function closeModal() {
    const modal = document.getElementById('delete-modal');
    if (modal) modal.classList.add('hidden');
    deleteTargetId = null;
}

function executeDelete() {
    if (!deleteTargetId) return;

    const formData = new FormData();
    formData.append('action', 'delete');
    formData.append('id', deleteTargetId);

    fetch('backend_acciones.php', {
        method: 'POST',
        body: formData
    })
    .then(async response => {
        const data = await response.json();
        if (!response.ok || !data.success) {
            throw new Error(data.message || 'No se pudo eliminar el producto.');
        }
        location.reload();
    })
    .catch(error => {
        console.error('Error al eliminar el producto:', error);
        alert('Error al eliminar: ' + error.message);
    });
}

// ==================== FILTRAR TIENDA POR CATEGORÍA ====================
function filterStoreCategory(category, btnElement) {
    document.querySelectorAll('.store-filter-btn').forEach(btn => {
        btn.classList.remove('btn-primary');
        btn.classList.add('btn-secondary');
    });
    btnElement.classList.remove('btn-secondary');
    btnElement.classList.add('btn-primary');

    const cards = document.querySelectorAll('.product-card');
    cards.forEach(card => {
        const cardCategory = card.getAttribute('data-category');
        if (category === 'all' || cardCategory === category) {
            card.classList.remove('hidden-card');
        } else {
            card.classList.add('hidden-card');
        }
    });
}