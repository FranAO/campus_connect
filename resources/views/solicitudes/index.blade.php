@extends('layouts.app')

@section('title', 'Solicitudes')
@section('page_title', 'Gestión de Solicitudes')

@section('content')
<div class="page-header">
    <div class="page-header-text">
        <h1>Solicitudes Institucionales</h1>
        <p>Administración y seguimiento de incidencias reportadas por la comunidad universitaria.</p>
    </div>
</div>

<!-- Filters Toolbar -->
<div class="filter-bar">
    <div class="search-input-group">
        <svg class="search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="11" cy="11" r="8"></circle>
            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
        </svg>
        <input type="text" id="inputBuscar" class="search-input" placeholder="Buscar por título, descripción o ubicación...">
    </div>

    <select id="selectEstado" class="select-filter">
        <option value="">Estado: Todos</option>
        <option value="PENDIENTE">PENDIENTE</option>
        <option value="ASIGNADA">ASIGNADA</option>
        <option value="EN_PROCESO">EN PROCESO</option>
        <option value="RESUELTA">RESUELTA</option>
        <option value="CERRADA">CERRADA</option>
    </select>

    <select id="selectTipo" class="select-filter">
        <option value="">Tipo: Todos</option>
        <option value="MANTENIMIENTO">Mantenimiento</option>
        <option value="SOPORTE_TECNOLOGICO">Soporte Tecnológico</option>
        <option value="INFRAESTRUCTURA">Infraestructura</option>
        <option value="EQUIPAMIENTO">Equipamiento</option>
        <option value="OTRO">Otro</option>
    </select>

    <select id="selectPrioridad" class="select-filter">
        <option value="">Prioridad: Todas</option>
        <option value="BAJA">Baja</option>
        <option value="MEDIA">Media</option>
        <option value="ALTA">Alta</option>
        <option value="URGENTE">Urgente</option>
    </select>

    <select id="selectResponsable" class="select-filter">
        <option value="">Responsable: Todos</option>
    </select>

    <button type="button" id="btnReset" class="btn btn-secondary btn-sm" onclick="resetFiltros()">Limpiar</button>
</div>

<!-- Solicitudes Table Card -->
<div class="card">
    <div class="card-header">
        <span class="card-title" id="tableTitle">Listado de Solicitudes</span>
        <span id="totalBadge" class="badge badge-tipo">0 Registros</span>
    </div>
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Título</th>
                    <th>Solicitante</th>
                    <th>Ubicación</th>
                    <th>Tipo</th>
                    <th>Prioridad</th>
                    <th>Estado</th>
                    <th>Responsable</th>
                    <th>Fecha</th>
                    <th style="text-align: right;">Acciones</th>
                </tr>
            </thead>
            <tbody id="solicitudesBody">
                <tr>
                    <td colspan="10" class="empty-state">
                        <span class="spinner" style="border-top-color:var(--primary); margin-bottom:8px;"></span>
                        <div>Cargando solicitudes...</div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    
    <!-- Pagination Footer -->
    <div class="card-header" style="background:var(--bg-muted); border-top:1px solid var(--border); border-bottom:none;">
        <span id="paginationInfo" style="font-size:12px; color:var(--text-muted);">Mostrando 0 de 0</span>
        <div class="page-actions" id="paginationButtons">
            <button id="btnPrevPage" class="btn btn-secondary btn-sm" disabled onclick="cambiarPagina(-1)">&larr; Anterior</button>
            <button id="btnNextPage" class="btn btn-secondary btn-sm" disabled onclick="cambiarPagina(1)">Siguiente &rarr;</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let currentPage = 1;
    let searchTimeout = null;

    async function cargarAdministrativos() {
        try {
            const res = await campus.fetch('/api/administrativos');
            if (res && res.success) {
                const select = document.getElementById('selectResponsable');
                res.data.forEach(user => {
                    const opt = document.createElement('option');
                    opt.value = user.id;
                    opt.textContent = user.name;
                    select.appendChild(opt);
                });
            }
        } catch (e) {
            console.warn('No se pudo cargar lista de administrativos:', e);
        }
    }

    async function cargarSolicitudes() {
        const tbody = document.getElementById('solicitudesBody');
        tbody.innerHTML = `
            <tr>
                <td colspan="10" class="empty-state">
                    <span class="spinner" style="border-top-color:var(--primary); margin-bottom:8px;"></span>
                    <div>Cargando solicitudes...</div>
                </td>
            </tr>
        `;

        const params = new URLSearchParams();
        params.append('page', currentPage);
        params.append('per_page', 15);

        const buscar = document.getElementById('inputBuscar').value.trim();
        const estado = document.getElementById('selectEstado').value;
        const tipo = document.getElementById('selectTipo').value;
        const prioridad = document.getElementById('selectPrioridad').value;
        const responsable = document.getElementById('selectResponsable').value;

        if (buscar) params.append('buscar', buscar);
        if (estado) params.append('estado', estado);
        if (tipo) params.append('tipo', tipo);
        if (prioridad) params.append('prioridad', prioridad);
        if (responsable) params.append('responsable_id', responsable);

        try {
            const res = await campus.fetch(`/api/solicitudes?${params.toString()}`);
            if (res && res.success) {
                const paginated = res.data;
                const items = paginated.data || [];

                document.getElementById('totalBadge').textContent = `${paginated.total || 0} Registros`;
                document.getElementById('paginationInfo').textContent = `Mostrando ${paginated.from || 0} a ${paginated.to || 0} de ${paginated.total || 0} solicitudes`;

                document.getElementById('btnPrevPage').disabled = !paginated.prev_page_url;
                document.getElementById('btnNextPage').disabled = !paginated.next_page_url;

                tbody.innerHTML = '';
                if (items.length === 0) {
                    tbody.innerHTML = `<tr><td colspan="10" class="empty-state">No se encontraron solicitudes con los filtros aplicados.</td></tr>`;
                    return;
                }

                items.forEach(sol => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td style="font-weight:700; color:var(--primary);">#${sol.id}</td>
                        <td style="font-weight:600; max-width:220px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;" title="${sol.titulo}">
                            ${sol.titulo}
                        </td>
                        <td>${sol.estudiante?.name || '—'}</td>
                        <td style="color:var(--text-muted); font-size:12px;">${sol.ubicacion || '—'}</td>
                        <td>${campus.tipoBadge(sol.tipo)}</td>
                        <td>${campus.priorityBadge(sol.prioridad)}</td>
                        <td>${campus.statusBadge(sol.estado)}</td>
                        <td>${sol.responsable?.name || '<span style="color:var(--text-light)">Sin asignar</span>'}</td>
                        <td style="color:var(--text-muted); font-size:12px;">${campus.formatDate(sol.created_at)}</td>
                        <td style="text-align:right;">
                            <a href="/solicitudes/${sol.id}" class="btn btn-primary btn-sm">Gestionar</a>
                        </td>
                    `;
                    tbody.appendChild(tr);
                });
            }
        } catch (error) {
            console.error('Error al cargar solicitudes:', error);
            tbody.innerHTML = `<tr><td colspan="10" class="empty-state" style="color:var(--danger)">Error al cargar solicitudes del servidor.</td></tr>`;
        }
    }

    function cambiarPagina(delta) {
        currentPage += delta;
        if (currentPage < 1) currentPage = 1;
        cargarSolicitudes();
    }

    function resetFiltros() {
        document.getElementById('inputBuscar').value = '';
        document.getElementById('selectEstado').value = '';
        document.getElementById('selectTipo').value = '';
        document.getElementById('selectPrioridad').value = '';
        document.getElementById('selectResponsable').value = '';
        currentPage = 1;
        cargarSolicitudes();
    }

    document.addEventListener('DOMContentLoaded', () => {
        cargarAdministrativos();
        cargarSolicitudes();

        // Bind filter change events
        ['selectEstado', 'selectTipo', 'selectPrioridad', 'selectResponsable'].forEach(id => {
            document.getElementById(id).addEventListener('change', () => {
                currentPage = 1;
                cargarSolicitudes();
            });
        });

        // Search with debounce
        document.getElementById('inputBuscar').addEventListener('input', () => {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                currentPage = 1;
                cargarSolicitudes();
            }, 300);
        });
    });
</script>
@endpush
