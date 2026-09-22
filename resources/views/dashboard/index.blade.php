@extends('layouts.app')

@section('title', 'Dashboard')
@section('page_title', 'Dashboard General')

@section('content')
<div class="page-header">
    <div class="page-header-text">
        <h1>Resumen Administrativo</h1>
        <p>Monitoreo en tiempo real del estado de atención de solicitudes y recursos del campus.</p>
    </div>
    <div class="page-actions">
        <button id="btnRefresh" class="btn btn-secondary btn-sm" onclick="cargarDashboard()">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="23 4 23 10 17 10"></polyline>
                <polyline points="1 20 1 14 7 14"></polyline>
                <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path>
            </svg>
            <span>Actualizar</span>
        </button>
    </div>
</div>

<!-- Metrics Cards -->
<div class="metrics-grid">
    <div class="metric-card" style="--card-accent: #2563eb; --icon-bg: #eff6ff; --icon-color: #2563eb;">
        <div class="metric-header">
            <span class="metric-title">Total Solicitudes</span>
            <div class="metric-icon-wrap">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
            </div>
        </div>
        <div class="metric-value" id="valTotal">—</div>
        <div class="metric-subtitle">Registradas en el sistema</div>
    </div>

    <div class="metric-card" style="--card-accent: #f59e0b; --icon-bg: #fef3c7; --icon-color: #d97706;">
        <div class="metric-header">
            <span class="metric-title">Pendientes</span>
            <div class="metric-icon-wrap">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
            </div>
        </div>
        <div class="metric-value" id="valPendientes">—</div>
        <div class="metric-subtitle">Requieren asignación</div>
    </div>

    <div class="metric-card" style="--card-accent: #0284c7; --icon-bg: #e0f2fe; --icon-color: #0284c7;">
        <div class="metric-header">
            <span class="metric-title">Asignadas</span>
            <div class="metric-icon-wrap">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><polyline points="16 11 18 13 22 9"></polyline></svg>
            </div>
        </div>
        <div class="metric-value" id="valAsignadas">—</div>
        <div class="metric-subtitle">Con técnico designado</div>
    </div>

    <div class="metric-card" style="--card-accent: #4f46e5; --icon-bg: #e0e7ff; --icon-color: #4f46e5;">
        <div class="metric-header">
            <span class="metric-title">En Proceso</span>
            <div class="metric-icon-wrap">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline></svg>
            </div>
        </div>
        <div class="metric-value" id="valEnProceso">—</div>
        <div class="metric-subtitle">En etapa de atención</div>
    </div>

    <div class="metric-card" style="--card-accent: #10b981; --icon-bg: #d1fae5; --icon-color: #059669;">
        <div class="metric-header">
            <span class="metric-title">Resueltas</span>
            <div class="metric-icon-wrap">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
            </div>
        </div>
        <div class="metric-value" id="valResueltas">—</div>
        <div class="metric-subtitle">Solución implementada</div>
    </div>

    <div class="metric-card" style="--card-accent: #64748b; --icon-bg: #f1f5f9; --icon-color: #475569;">
        <div class="metric-header">
            <span class="metric-title">Cerradas</span>
            <div class="metric-icon-wrap">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
            </div>
        </div>
        <div class="metric-value" id="valCerradas">—</div>
        <div class="metric-subtitle">Ciclo finalizado</div>
    </div>
</div>

<!-- Distributions Row -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 24px; margin-bottom: 24px;">
    <!-- By Type -->
    <div class="card">
        <div class="card-header">
            <span class="card-title">Solicitudes por Tipo</span>
        </div>
        <div class="card-body" id="containerPorTipo">
            <div class="empty-state">Cargando distribución...</div>
        </div>
    </div>

    <!-- By Priority -->
    <div class="card">
        <div class="card-header">
            <span class="card-title">Solicitudes por Prioridad</span>
        </div>
        <div class="card-body" id="containerPorPrioridad">
            <div class="empty-state">Cargando distribución...</div>
        </div>
    </div>
</div>

<!-- Recent Solicitudes Table -->
<div class="card">
    <div class="card-header">
        <span class="card-title">Solicitudes Recientes</span>
        <a href="{{ route('solicitudes.index') }}" class="btn btn-secondary btn-sm">Ver todas las solicitudes &rarr;</a>
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
                    <th>Fecha</th>
                    <th style="text-align: right;">Acción</th>
                </tr>
            </thead>
            <tbody id="tablaRecientesBody">
                <tr>
                    <td colspan="9" class="empty-state">Cargando solicitudes recientes...</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
    async function cargarDashboard() {
        try {
            // 1. Fetch Dashboard Stats
            const dashRes = await campus.fetch('/api/dashboard');
            if (dashRes && dashRes.success) {
                const data = dashRes.data;
                document.getElementById('valTotal').textContent = data.total ?? 0;
                
                const porEstado = data.por_estado || {};
                document.getElementById('valPendientes').textContent = porEstado.PENDIENTE ?? 0;
                document.getElementById('valAsignadas').textContent = porEstado.ASIGNADA ?? 0;
                document.getElementById('valEnProceso').textContent = porEstado.EN_PROCESO ?? 0;
                document.getElementById('valResueltas').textContent = porEstado.RESUELTA ?? 0;
                document.getElementById('valCerradas').textContent = porEstado.CERRADA ?? 0;

                // Render Por Tipo
                const porTipo = data.por_tipo || {};
                const total = data.total || 1;
                const containerTipo = document.getElementById('containerPorTipo');
                containerTipo.innerHTML = '';
                
                const tipos = Object.keys(porTipo);
                if (tipos.length === 0) {
                    containerTipo.innerHTML = '<div class="empty-state">No hay datos registrados.</div>';
                } else {
                    tipos.forEach(tipo => {
                        const count = porTipo[tipo] || 0;
                        const pct = Math.round((count / total) * 100);
                        const row = document.createElement('div');
                        row.style.marginBottom = '14px';
                        row.innerHTML = `
                            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:4px; font-size:13px;">
                                <span style="font-weight:600;">${campus.tipoBadge(tipo)}</span>
                                <span style="font-weight:700; color:var(--text-muted);">${count} (${pct}%)</span>
                            </div>
                            <div style="height:8px; background:var(--bg-muted); border-radius:4px; overflow:hidden;">
                                <div style="width:${pct}%; height:100%; background:var(--primary); border-radius:4px;"></div>
                            </div>
                        `;
                        containerTipo.appendChild(row);
                    });
                }

                // Render Por Prioridad
                const porPrioridad = data.por_prioridad || {};
                const containerPrio = document.getElementById('containerPorPrioridad');
                containerPrio.innerHTML = '';
                
                const prioridades = ['URGENTE', 'ALTA', 'MEDIA', 'BAJA'];
                prioridades.forEach(prio => {
                    const count = porPrioridad[prio] || 0;
                    const pct = Math.round((count / total) * 100);
                    const row = document.createElement('div');
                    row.style.marginBottom = '14px';
                    row.innerHTML = `
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:4px; font-size:13px;">
                            <span>${campus.priorityBadge(prio)}</span>
                            <span style="font-weight:700; color:var(--text-muted);">${count} (${pct}%)</span>
                        </div>
                        <div style="height:8px; background:var(--bg-muted); border-radius:4px; overflow:hidden;">
                            <div style="width:${pct}%; height:100%; background:${
                                prio === 'URGENTE' ? '#ef4444' : (prio === 'ALTA' ? '#f97316' : (prio === 'MEDIA' ? '#eab308' : '#22c55e'))
                            }; border-radius:4px;"></div>
                        </div>
                    `;
                    containerPrio.appendChild(row);
                });
            }

            // 2. Fetch Recent Solicitudes
            const recRes = await campus.fetch('/api/solicitudes?per_page=5');
            const tbody = document.getElementById('tablaRecientesBody');
            tbody.innerHTML = '';

            const items = recRes?.data?.data || [];
            if (items.length === 0) {
                tbody.innerHTML = `<tr><td colspan="9" class="empty-state">No hay solicitudes registradas aún.</td></tr>`;
            } else {
                items.forEach(sol => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td style="font-weight:700; color:var(--primary);">#${sol.id}</td>
                        <td style="font-weight:600; max-width:240px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                            ${sol.titulo}
                        </td>
                        <td>${sol.estudiante?.name || '—'}</td>
                        <td>${campus.tipoBadge(sol.tipo)}</td>
                        <td>${campus.priorityBadge(sol.prioridad)}</td>
                        <td>${campus.statusBadge(sol.estado)}</td>
                        <td>${sol.responsable?.name || '<span style="color:var(--text-light)">Sin asignar</span>'}</td>
                        <td style="color:var(--text-muted); font-size:12px;">${campus.formatDate(sol.created_at)}</td>
                        <td style="text-align:right;">
                            <a href="/solicitudes/${sol.id}" class="btn btn-secondary btn-sm">Ver</a>
                        </td>
                    `;
                    tbody.appendChild(tr);
                });
            }
        } catch (error) {
            console.error('Error al cargar dashboard:', error);
            campus.toast('Error al consultar datos del dashboard', 'error');
        }
    }

    document.addEventListener('DOMContentLoaded', cargarDashboard);
</script>
@endpush
