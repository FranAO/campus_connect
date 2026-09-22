@extends('layouts.app')

@section('title', 'Reportes Institucionales')
@section('page_title', 'Reportes y Estadísticas')

@section('content')
<div class="page-header">
    <div class="page-header-text">
        <h1>Reportes y Estadísticas</h1>
        <p>Análisis consolidado de solicitudes recibidas, tiempos de resolución e indicadores de gestión.</p>
    </div>
</div>

<!-- Report Filters Card -->
<div class="card">
    <div class="card-header">
        <span class="card-title">Parámetros de Consulta</span>
    </div>
    <div class="card-body">
        <form id="formReportes" style="display:grid; grid-template-columns:repeat(auto-fit, minmax(180px, 1fr)); gap:14px; align-items:end;">
            <div>
                <label for="repDesde" class="form-label">Fecha Desde</label>
                <input type="date" id="repDesde" class="form-input">
            </div>

            <div>
                <label for="repHasta" class="form-label">Fecha Hasta</label>
                <input type="date" id="repHasta" class="form-input">
            </div>

            <div>
                <label for="repTipo" class="form-label">Tipo de Solicitud</label>
                <select id="repTipo" class="form-select">
                    <option value="">Todos los tipos</option>
                    <option value="MANTENIMIENTO">Mantenimiento</option>
                    <option value="SOPORTE_TECNOLOGICO">Soporte Tecnológico</option>
                    <option value="INFRAESTRUCTURA">Infraestructura</option>
                    <option value="EQUIPAMIENTO">Equipamiento</option>
                    <option value="OTRO">Otro</option>
                </select>
            </div>

            <div>
                <label for="repEstado" class="form-label">Estado</label>
                <select id="repEstado" class="form-select">
                    <option value="">Todos los estados</option>
                    <option value="PENDIENTE">PENDIENTE</option>
                    <option value="ASIGNADA">ASIGNADA</option>
                    <option value="EN_PROCESO">EN PROCESO</option>
                    <option value="RESUELTA">RESUELTA</option>
                    <option value="CERRADA">CERRADA</option>
                </select>
            </div>

            <div>
                <label for="repPrioridad" class="form-label">Prioridad</label>
                <select id="repPrioridad" class="form-select">
                    <option value="">Todas las prioridades</option>
                    <option value="BAJA">BAJA</option>
                    <option value="MEDIA">MEDIA</option>
                    <option value="ALTA">ALTA</option>
                    <option value="URGENTE">URGENTE</option>
                </select>
            </div>

            <div>
                <label for="repResponsable" class="form-label">Responsable</label>
                <select id="repResponsable" class="form-select">
                    <option value="">Todos los responsables</option>
                </select>
            </div>

            <div style="display:flex; gap:8px;">
                <button type="submit" id="btnGenerarReporte" class="btn btn-primary" style="flex:1;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    <span>Consultar</span>
                </button>
                <button type="button" class="btn btn-secondary" onclick="limpiarFiltrosReporte()">Limpiar</button>
            </div>
        </form>
    </div>
</div>

<!-- Summary Metrics (KPIs) -->
<div class="metrics-grid">
    <div class="metric-card" style="--card-accent: #2563eb; --icon-bg: #eff6ff; --icon-color: #2563eb;">
        <div class="metric-header">
            <span class="metric-title">Solicitudes Recibidas</span>
            <div class="metric-icon-wrap">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
            </div>
        </div>
        <div class="metric-value" id="kpiTotal">—</div>
        <div class="metric-subtitle">Total en el período</div>
    </div>

    <div class="metric-card" style="--card-accent: #f59e0b; --icon-bg: #fef3c7; --icon-color: #d97706;">
        <div class="metric-header">
            <span class="metric-title">Pendientes de Atención</span>
            <div class="metric-icon-wrap">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
            </div>
        </div>
        <div class="metric-value" id="kpiPendientes">—</div>
        <div class="metric-subtitle">Sin resolver aún</div>
    </div>

    <div class="metric-card" style="--card-accent: #10b981; --icon-bg: #d1fae5; --icon-color: #059669;">
        <div class="metric-header">
            <span class="metric-title">Solicitudes Cerradas</span>
            <div class="metric-icon-wrap">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
            </div>
        </div>
        <div class="metric-value" id="kpiCerradas">—</div>
        <div class="metric-subtitle">Con ciclo completado</div>
    </div>

    <div class="metric-card" style="--card-accent: #8b5cf6; --icon-bg: #f5f3ff; --icon-color: #7c3aed;">
        <div class="metric-header">
            <span class="metric-title">Tiempo Promedio Atención</span>
            <div class="metric-icon-wrap">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
            </div>
        </div>
        <div class="metric-value" id="kpiTiempo">—</div>
        <div class="metric-subtitle" id="kpiTiempoSub">Promedio en solicitudes cerradas</div>
    </div>
</div>

<!-- Detailed Table of Filtered Solicitudes -->
<div class="card">
    <div class="card-header">
        <span class="card-title">Detalle de Solicitudes en el Reporte</span>
        <span id="reporteRowsBadge" class="badge badge-tipo">0 Registros</span>
    </div>
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Título</th>
                    <th>Solicitante</th>
                    <th>Tipo</th>
                    <th>Prioridad</th>
                    <th>Estado</th>
                    <th>Responsable</th>
                    <th>Fecha Registro</th>
                    <th>Fecha Cierre</th>
                    <th style="text-align: right;">Acción</th>
                </tr>
            </thead>
            <tbody id="reporteTableBody">
                <tr>
                    <td colspan="10" class="empty-state">
                        Seleccione los parámetros y presione "Consultar" para ver los resultados.
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
    async function cargarAdministrativosReporte() {
        try {
            const res = await campus.fetch('/api/administrativos');
            if (res && res.success) {
                const select = document.getElementById('repResponsable');
                res.data.forEach(u => {
                    const opt = document.createElement('option');
                    opt.value = u.id;
                    opt.textContent = u.name;
                    select.appendChild(opt);
                });
            }
        } catch (e) {
            console.warn('Error al cargar administrativos:', e);
        }
    }

    async function generarReporte() {
        const tbody = document.getElementById('reporteTableBody');
        const btn = document.getElementById('btnGenerarReporte');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner"></span> Consultando...';

        tbody.innerHTML = `
            <tr>
                <td colspan="10" class="empty-state">
                    <span class="spinner" style="border-top-color:var(--primary); margin-bottom:8px;"></span>
                    <div>Generando reporte...</div>
                </td>
            </tr>
        `;

        const params = new URLSearchParams();
        const desde = document.getElementById('repDesde').value;
        const hasta = document.getElementById('repHasta').value;
        const tipo = document.getElementById('repTipo').value;
        const estado = document.getElementById('repEstado').value;
        const prioridad = document.getElementById('repPrioridad').value;
        const responsable = document.getElementById('repResponsable').value;

        if (desde) params.append('desde', desde);
        if (hasta) params.append('hasta', hasta);
        if (tipo) params.append('tipo', tipo);
        if (estado) params.append('estado', estado);
        if (prioridad) params.append('prioridad', prioridad);
        if (responsable) params.append('responsable_id', responsable);

        try {
            const res = await campus.fetch(`/api/reportes/solicitudes?${params.toString()}`);
            if (res && res.success) {
                const data = res.data;
                const resumen = data.resumen || {};
                const solicitudes = data.solicitudes || [];

                // Update KPIs
                document.getElementById('kpiTotal').textContent = resumen.total ?? 0;
                document.getElementById('kpiPendientes').textContent = resumen.pendientes ?? 0;
                document.getElementById('kpiCerradas').textContent = resumen.cerradas ?? 0;

                const promHoras = resumen.promedio_horas_atencion;
                if (promHoras !== null && promHoras !== undefined) {
                    if (promHoras >= 24) {
                        const dias = (promHoras / 24).toFixed(1);
                        document.getElementById('kpiTiempo').textContent = `${dias} d`;
                        document.getElementById('kpiTiempoSub').textContent = `Equivalente a ${promHoras} horas`;
                    } else {
                        document.getElementById('kpiTiempo').textContent = `${promHoras} h`;
                        document.getElementById('kpiTiempoSub').textContent = `Promedio de atención`;
                    }
                } else {
                    document.getElementById('kpiTiempo').textContent = 'N/A';
                    document.getElementById('kpiTiempoSub').textContent = 'Sin solicitudes cerradas';
                }

                document.getElementById('reporteRowsBadge').textContent = `${solicitudes.length} Registros`;

                // Render Table
                tbody.innerHTML = '';
                if (solicitudes.length === 0) {
                    tbody.innerHTML = `<tr><td colspan="10" class="empty-state">No se encontraron solicitudes que coincidan con los filtros seleccionados.</td></tr>`;
                    return;
                }

                solicitudes.forEach(sol => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td style="font-weight:700; color:var(--primary);">#${sol.id}</td>
                        <td style="font-weight:600; max-width:200px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;" title="${sol.titulo}">
                            ${sol.titulo}
                        </td>
                        <td>${sol.estudiante?.name || '—'}</td>
                        <td>${campus.tipoBadge(sol.tipo)}</td>
                        <td>${campus.priorityBadge(sol.prioridad)}</td>
                        <td>${campus.statusBadge(sol.estado)}</td>
                        <td>${sol.responsable?.name || '<span style="color:var(--text-light)">Sin asignar</span>'}</td>
                        <td style="color:var(--text-muted); font-size:12px;">${campus.formatDate(sol.created_at)}</td>
                        <td style="color:var(--text-muted); font-size:12px;">${campus.formatDate(sol.fecha_cierre)}</td>
                        <td style="text-align:right;">
                            <a href="/solicitudes/${sol.id}" class="btn btn-secondary btn-sm">Ver</a>
                        </td>
                    `;
                    tbody.appendChild(tr);
                });
            }
        } catch (error) {
            console.error('Error al generar reporte:', error);
            tbody.innerHTML = `<tr><td colspan="10" class="empty-state" style="color:var(--danger)">Error al procesar el reporte.</td></tr>`;
        } finally {
            btn.disabled = false;
            btn.innerHTML = `
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                <span>Consultar</span>
            `;
        }
    }

    function limpiarFiltrosReporte() {
        document.getElementById('repDesde').value = '';
        document.getElementById('repHasta').value = '';
        document.getElementById('repTipo').value = '';
        document.getElementById('repEstado').value = '';
        document.getElementById('repPrioridad').value = '';
        document.getElementById('repResponsable').value = '';
        generarReporte();
    }

    document.addEventListener('DOMContentLoaded', () => {
        cargarAdministrativosReporte();
        generarReporte();

        document.getElementById('formReportes').addEventListener('submit', (e) => {
            e.preventDefault();
            generarReporte();
        });
    });
</script>
@endpush
