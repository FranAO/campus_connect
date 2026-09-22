@extends('layouts.app')

@section('title', 'Recursos Institucionales')
@section('page_title', 'Gestión de Recursos')

@section('content')
<div class="page-header">
    <div class="page-header-text">
        <h1>Recursos Institucionales</h1>
        <p>Control de disponibilidad y estado de equipamiento y espacios universitarios.</p>
    </div>
    <div class="page-actions">
        <button type="button" class="btn btn-primary" onclick="abrirModalCrear()">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            <span>Nuevo Recurso</span>
        </button>
    </div>
</div>

<!-- Filters Bar -->
<div class="filter-bar">
    <div class="search-input-group">
        <svg class="search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="11" cy="11" r="8"></circle>
            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
        </svg>
        <input type="text" id="inputBuscarRecurso" class="search-input" placeholder="Buscar recurso por nombre o descripción...">
    </div>

    <select id="selectTipoRecurso" class="select-filter">
        <option value="">Tipo: Todos</option>
        <option value="EQUIPO">Equipo</option>
        <option value="ESPACIO">Espacio</option>
        <option value="OTRO">Otro</option>
    </select>

    <select id="selectEstadoRecurso" class="select-filter">
        <option value="">Estado: Todos</option>
        <option value="DISPONIBLE">DISPONIBLE</option>
        <option value="EN_USO">EN USO</option>
        <option value="MANTENIMIENTO">MANTENIMIENTO</option>
        <option value="NO_DISPONIBLE">NO DISPONIBLE</option>
    </select>

    <button type="button" class="btn btn-secondary btn-sm" onclick="resetFiltrosRecursos()">Limpiar</button>
</div>

<!-- Table Card -->
<div class="card">
    <div class="card-header">
        <span class="card-title">Inventario de Recursos</span>
        <span id="recursosBadge" class="badge badge-tipo">0 Recursos</span>
    </div>
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Tipo</th>
                    <th>Descripción</th>
                    <th>Estado</th>
                    <th>Última Actualización</th>
                    <th style="text-align: right;">Acciones</th>
                </tr>
            </thead>
            <tbody id="recursosBody">
                <tr>
                    <td colspan="7" class="empty-state">
                        <span class="spinner" style="border-top-color:var(--primary); margin-bottom:8px;"></span>
                        <div>Cargando recursos...</div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Pagination Footer -->
    <div class="card-header" style="background:var(--bg-muted); border-top:1px solid var(--border); border-bottom:none;">
        <span id="recursosPaginationInfo" style="font-size:12px; color:var(--text-muted);">Mostrando 0 de 0</span>
        <div class="page-actions">
            <button id="btnPrevRecurso" class="btn btn-secondary btn-sm" disabled onclick="cambiarPaginaRecurso(-1)">&larr; Anterior</button>
            <button id="btnNextRecurso" class="btn btn-secondary btn-sm" disabled onclick="cambiarPaginaRecurso(1)">Siguiente &rarr;</button>
        </div>
    </div>
</div>

<!-- Modal: Crear / Editar Recurso -->
<div id="modalRecurso" class="modal-backdrop">
    <div class="modal-box">
        <div class="modal-header">
            <h2 id="modalRecursoTitle" class="modal-title">Nuevo Recurso</h2>
            <button type="button" class="modal-close" onclick="cerrarModalRecurso()">&times;</button>
        </div>
        <form id="formRecurso">
            <div class="modal-body">
                <input type="hidden" id="recursoId">

                <div class="form-group">
                    <label for="nombreRecurso" class="form-label">Nombre del Recurso</label>
                    <input type="text" id="nombreRecurso" class="form-input" placeholder="Ej: Proyector EPSON Aula 305" required>
                </div>

                <div class="form-group">
                    <label for="tipoRecurso" class="form-label">Tipo de Recurso</label>
                    <input type="text" id="tipoRecurso" class="form-input" placeholder="Ej: EQUIPO, ESPACIO, TECNOLOGIA..." required>
                </div>

                <div class="form-group">
                    <label for="estadoRecurso" class="form-label">Estado de Disponibilidad</label>
                    <select id="estadoRecurso" class="form-select" required>
                        <option value="DISPONIBLE">DISPONIBLE</option>
                        <option value="EN_USO">EN USO</option>
                        <option value="MANTENIMIENTO">MANTENIMIENTO</option>
                        <option value="NO_DISPONIBLE">NO DISPONIBLE</option>
                    </select>
                </div>

                <div class="form-group" style="margin-bottom:0;">
                    <label for="descRecurso" class="form-label">Descripción</label>
                    <textarea id="descRecurso" class="form-textarea" placeholder="Detalles, número de serie o especificaciones..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="cerrarModalRecurso()">Cancelar</button>
                <button type="submit" id="btnGuardarRecurso" class="btn btn-primary">Guardar Recurso</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let recursoPage = 1;
    let recursoSearchTimeout = null;

    async function cargarRecursos() {
        const tbody = document.getElementById('recursosBody');
        tbody.innerHTML = `
            <tr>
                <td colspan="7" class="empty-state">
                    <span class="spinner" style="border-top-color:var(--primary); margin-bottom:8px;"></span>
                    <div>Cargando recursos...</div>
                </td>
            </tr>
        `;

        const params = new URLSearchParams();
        params.append('page', recursoPage);
        params.append('per_page', 20);

        const buscar = document.getElementById('inputBuscarRecurso').value.trim();
        const tipo = document.getElementById('selectTipoRecurso').value;
        const estado = document.getElementById('selectEstadoRecurso').value;

        if (buscar) params.append('buscar', buscar);
        if (tipo) params.append('tipo', tipo);
        if (estado) params.append('estado', estado);

        try {
            const res = await campus.fetch(`/api/recursos?${params.toString()}`);
            if (res && res.success) {
                const paginated = res.data;
                const items = paginated.data || [];

                document.getElementById('recursosBadge').textContent = `${paginated.total || 0} Recursos`;
                document.getElementById('recursosPaginationInfo').textContent = `Mostrando ${paginated.from || 0} a ${paginated.to || 0} de ${paginated.total || 0} recursos`;

                document.getElementById('btnPrevRecurso').disabled = !paginated.prev_page_url;
                document.getElementById('btnNextRecurso').disabled = !paginated.next_page_url;

                tbody.innerHTML = '';
                if (items.length === 0) {
                    tbody.innerHTML = `<tr><td colspan="7" class="empty-state">No se encontraron recursos registrados.</td></tr>`;
                    return;
                }

                items.forEach(rec => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td style="font-weight:700; color:var(--primary);">#${rec.id}</td>
                        <td style="font-weight:600;">${rec.nombre}</td>
                        <td>${campus.tipoBadge(rec.tipo)}</td>
                        <td style="color:var(--text-muted); font-size:13px; max-width:250px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                            ${rec.descripcion || '—'}
                        </td>
                        <td>${campus.recursoBadge(rec.estado)}</td>
                        <td style="color:var(--text-muted); font-size:12px;">${campus.formatDate(rec.updated_at || rec.created_at)}</td>
                        <td style="text-align:right; white-space:nowrap;">
                            <button type="button" class="btn btn-secondary btn-sm" onclick='abrirModalEditar(${JSON.stringify(rec)})'>Editar</button>
                            <button type="button" class="btn btn-danger btn-sm" onclick="eliminarRecurso(${rec.id}, '${rec.nombre}')">Eliminar</button>
                        </td>
                    `;
                    tbody.appendChild(tr);
                });
            }
        } catch (error) {
            console.error('Error al cargar recursos:', error);
            tbody.innerHTML = `<tr><td colspan="7" class="empty-state" style="color:var(--danger)">Error al consultar recursos.</td></tr>`;
        }
    }

    function cambiarPaginaRecurso(delta) {
        recursoPage += delta;
        if (recursoPage < 1) recursoPage = 1;
        cargarRecursos();
    }

    function resetFiltrosRecursos() {
        document.getElementById('inputBuscarRecurso').value = '';
        document.getElementById('selectTipoRecurso').value = '';
        document.getElementById('selectEstadoRecurso').value = '';
        recursoPage = 1;
        cargarRecursos();
    }

    function abrirModalCrear() {
        document.getElementById('modalRecursoTitle').textContent = 'Nuevo Recurso Institucional';
        document.getElementById('recursoId').value = '';
        document.getElementById('nombreRecurso').value = '';
        document.getElementById('tipoRecurso').value = '';
        document.getElementById('estadoRecurso').value = 'DISPONIBLE';
        document.getElementById('descRecurso').value = '';
        document.getElementById('modalRecurso').classList.add('active');
    }

    function abrirModalEditar(rec) {
        document.getElementById('modalRecursoTitle').textContent = `Editar Recurso #${rec.id}`;
        document.getElementById('recursoId').value = rec.id;
        document.getElementById('nombreRecurso').value = rec.nombre;
        document.getElementById('tipoRecurso').value = rec.tipo;
        document.getElementById('estadoRecurso').value = rec.estado;
        document.getElementById('descRecurso').value = rec.descripcion || '';
        document.getElementById('modalRecurso').classList.add('active');
    }

    function cerrarModalRecurso() {
        document.getElementById('modalRecurso').classList.remove('active');
    }

    document.getElementById('formRecurso').addEventListener('submit', async function(e) {
        e.preventDefault();
        const id = document.getElementById('recursoId').value;
        const nombre = document.getElementById('nombreRecurso').value.trim();
        const tipo = document.getElementById('tipoRecurso').value.trim();
        const estado = document.getElementById('estadoRecurso').value;
        const descripcion = document.getElementById('descRecurso').value.trim();

        const btn = document.getElementById('btnGuardarRecurso');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner"></span> Guardando...';

        try {
            const url = id ? `/api/recursos/${id}` : '/api/recursos';
            const method = id ? 'PUT' : 'POST';

            const res = await campus.fetch(url, {
                method,
                body: JSON.stringify({ nombre, tipo, estado, descripcion })
            });

            if (res && res.success) {
                campus.toast(id ? 'Recurso actualizado con éxito' : 'Recurso creado con éxito', 'success');
                cerrarModalRecurso();
                await cargarRecursos();
            }
        } catch (err) {
            campus.toast(err.message || 'Error al guardar recurso', 'error');
        } finally {
            btn.disabled = false;
            btn.textContent = 'Guardar Recurso';
        }
    });

    async function eliminarRecurso(id, nombre) {
        if (!confirm(`¿Está seguro de eliminar el recurso "${nombre}" (#${id})? Esta acción no se puede deshacer.`)) {
            return;
        }

        try {
            const res = await campus.fetch(`/api/recursos/${id}`, { method: 'DELETE' });
            if (res && res.success) {
                campus.toast('Recurso eliminado correctamente', 'success');
                await cargarRecursos();
            }
        } catch (err) {
            campus.toast(err.message || 'Error al eliminar recurso', 'error');
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        cargarRecursos();

        ['selectTipoRecurso', 'selectEstadoRecurso'].forEach(id => {
            document.getElementById(id).addEventListener('change', () => {
                recursoPage = 1;
                cargarRecursos();
            });
        });

        document.getElementById('inputBuscarRecurso').addEventListener('input', () => {
            clearTimeout(recursoSearchTimeout);
            recursoSearchTimeout = setTimeout(() => {
                recursoPage = 1;
                cargarRecursos();
            }, 300);
        });
    });
</script>
@endpush
