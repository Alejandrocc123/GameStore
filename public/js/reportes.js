const SECCIONES_CON_DATOS = ['section-dashboard', 'section-clients', 'section-sales', 'section-reports'];

const fmtCOP = (n) => '$ ' + Number(n || 0).toLocaleString('es-CO') + ' COP';

const esc = (s) => String(s ?? '').replace(/[&<>"']/g, (c) => ({
    '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
}[c]));

const fmtFecha = (f) => {
    if (!f) return '';
    const d = new Date(String(f).replace(' ', 'T'));
    return isNaN(d) ? esc(f) : d.toLocaleString('es-CO', { dateStyle: 'medium', timeStyle: 'short' });
};

const nombreCategoria = (c) => ({ videojuego: '🎮 Videojuegos', consola: '🕹️ Consolas', accesorio: '🎧 Accesorios' }[c] || esc(c));

function pintarFilas(tbodyId, filas, columnas, mensajeVacio) {
    const tbody = document.getElementById(tbodyId);
    if (!tbody) return;
    tbody.innerHTML = filas.length
        ? filas.join('')
        : `<tr><td colspan="${columnas}" style="text-align:center; color: var(--text-muted); padding: 24px;">${mensajeVacio}</td></tr>`;
}

async function cargarReportes() {
    try {
        const res = await fetch('app/controllers/backend_acciones.php?action=reportes', { cache: 'no-store' });
        const data = await res.json();
        if (!data.success) throw new Error(data.message || 'No se pudieron cargar los reportes.');

        const r = data.resumen;
        const setText = (id, txt) => { const el = document.getElementById(id); if (el) el.textContent = txt; };
        setText('m-ventas', fmtCOP(r.ventas_netas));
        setText('m-pedidos', Number(r.pedidos).toLocaleString('es-CO'));
        setText('m-stock', Number(r.stock_total).toLocaleString('es-CO'));
        setText('m-critico', `${r.stock_critico} artículos con stock crítico`);
        setText('m-ticket', fmtCOP(r.ticket_medio));

        pintarFilas('sales-body', data.ventas.map((v) => `
            <tr>
                <td>#${esc(v.id)}</td>
                <td>${fmtFecha(v.creado_en)}</td>
                <td>${esc(v.email)}</td>
                <td>${esc(v.unidades)}</td>
                <td class="price-highlight">${fmtCOP(v.total)}</td>
            </tr>`), 5, 'Aún no hay ventas registradas.');

        pintarFilas('clients-body', data.clientes.map((c) => `
            <tr>
                <td>${esc(c.email)}</td>
                <td>${esc(c.compras)}</td>
                <td class="price-highlight">${fmtCOP(c.gastado)}</td>
                <td>${fmtFecha(c.creado_en)}</td>
            </tr>`), 4, 'Aún no hay clientes registrados.');

        pintarFilas('reports-cat-body', data.por_categoria.map((c) => `
            <tr>
                <td>${nombreCategoria(c.categoria)}</td>
                <td>${esc(c.unidades)}</td>
                <td class="price-highlight">${fmtCOP(c.ingresos)}</td>
            </tr>`), 3, 'Sin datos todavía.');

        pintarFilas('reports-top-body', data.top_productos.map((p) => `
            <tr>
                <td>${esc(p.titulo)}</td>
                <td>${esc(p.unidades)}</td>
                <td class="price-highlight">${fmtCOP(p.ingresos)}</td>
            </tr>`), 3, 'Sin datos todavía.');
    } catch (err) {
        console.error('Error cargando reportes:', err);
    }
}

function adminVisible() {
    const admin = document.getElementById('admin-screen');
    return admin && !admin.classList.contains('hidden');
}

document.addEventListener('DOMContentLoaded', () => {
    const tick = () => { if (adminVisible()) cargarReportes(); };

    document.querySelectorAll('.sidebar-nav .nav-item').forEach((item) => {
        item.addEventListener('click', () => {
            if (SECCIONES_CON_DATOS.includes(item.getAttribute('data-target'))) tick();
        });
    });

    const loginForm = document.getElementById('login-form');
    if (loginForm) loginForm.addEventListener('submit', () => setTimeout(tick, 300));
    setTimeout(tick, 300);

    setInterval(tick, 10000);
});