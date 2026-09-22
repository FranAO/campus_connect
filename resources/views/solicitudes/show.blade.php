@extends('layouts.app')

@section('title', 'Detalle de Solicitud #' . $solicitudId)
@section('page_title', 'Gestión de Solicitud')

@section('content')
<div style="margin-bottom: 16px;">
    <a href="{{ route('solicitudes.index') }}" style="color:var(--text-muted); text-decoration:none; font-size:13px; font-weight:600; display:inline-flex; align-items:center; gap:6px;">
        &larr; Volver al listado de solicitudes
    </a>
</div>

<div class="page-header" style="align-items:flex-start;">
    <div class="page-header-text">
        <div style="display:flex; align-items:center; gap:10px; margin-bottom:6px;">
            <span style="font-size:22px; font-weight:800; color:var(--primary);">#<span id="solId">{{ $solicitudId }}</span></span>
            <span id="badgeEstadoHeader"></span>
            <span id="badgePrioridadHeader"></span>
        </div>
        <h1 id="solTitulo" style="font-size:22px; font-weight:700;">Cargando solicitud...</h1>
        <p id="solSubtitulo" style="font-size:13px; color:var(--text-muted);">Consultando datos al servidor...</p>
    </div>
</div>

<div class="detail-grid">
    <!-- Left Column: Details, Evidences, Comments -->
    <div>
        <!-- Main Details Card -->
        <div class="card">
            <div class="card-header">
                <span class="card-title">Información de la Solicitud</span>
                <span id="badgeTipoHeader"></span>
            </div>
            <div class="card-body">
                <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(200px, 1fr)); gap:18px; margin-bottom:20px;">
                    <div class="info-item">
                        <div class="info-label">Estudiante Solicitante</div>
                        <div class="info-value" id="infoEstudiante">—</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Ubicación / Espacio</div>
                        <div class="info-value" id="infoUbicacion">—</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Fecha de Registro</div>
                        <div class="info-value" id="infoFecha">—</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Responsable Asignado</div>
                        <div class="info-value" id="infoResponsable" style="font-weight:700;">—</div>
                    </div>
                </div>

                <div class="info-item">
                    <div class="info-label">Descripción de la Incidencia</div>
                    <div id="infoDescripcion" style="font-size:14px; line-height:1.6; color:var(--text-main); background:var(--bg-muted); padding:16px; border-radius:var(--radius-md); border:1px solid var(--border);">
                        Cargando...
                    </div>
                </div>
            </div>
        </div>

        <!-- Evidences Card -->
        <div class="card">
            <div class="card-header">
                <span class="card-title">Evidencias Adjuntas</span>
                <span id="evidenciasCount" class="badge badge-tipo">0 Archivos</span>
            </div>
            <div class="card-body">
                <div id="evidenciasContainer" class="evidences-grid">
                    <div class="empty-state" style="grid-column: 1 / -1;">No hay evidencias adjuntas para esta solicitud.</div>
                </div>
            </div>
        </div>

        <!-- Comments Card -->
        <div class="card">
            <div class="card-header">
                <span class="card-title">Comentarios de Seguimiento</span>
                <span id="comentariosCount" class="badge badge-tipo">0 Comentarios</span>
            </div>
            <div class="card-body">
                <div id="comentariosList" class="comments-list">
                    <div class="empty-state">No hay comentarios registrados.</div>
                </div>

                <form id="commentForm" style="margin-top: 16px; border-top:1px solid var(--border); padding-top:16px;">
                    <label for="txtComentario" class="form-label">Agregar Comentario Administrativo</label>
                    <textarea id="txtComentario" class="form-textarea" placeholder="Escriba un comentario o indicación visible para el estudiante..." required></textarea>
                    <div style="display:flex; justify-content:flex-end; margin-top:10px;">
                        <button type="submit" id="btnEnviarComentario" class="btn btn-primary btn-sm">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
                            <span>Enviar Comentario</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Right Column: Administrative Actions & Timeline -->
    <div>
        <!-- Management Actions Card -->
        <div class="card" style="border-top: 4px solid var(--primary);">
            <div class="card-header">
                <span class="card-title">Gestión Administrativa</span>
            </div>
            <div class="card-body">
                <!-- Change Status -->
                <div class="form-group">
                    <label for="selectAdminEstado" class="form-label">Estado de la Solicitud</label>
                    <div style="display:flex; gap:8px;">
                        <select id="selectAdminEstado" class="form-select">
                            <option value="PENDIENTE">PENDIENTE</option>
                            <option value="ASIGNADA">ASIGNADA</option>
                            <option value="EN_PROCESO">EN PROCESO</option>
                            <option value="RESUELTA">RESUELTA</option>
                            <option value="CERRADA">CERRADA</option>
                        </select>
                        <button type="button" id="btnGuardarEstado" class="btn btn-primary btn-sm" onclick="guardarEstado()">
                            Guardar
                        </button>
                    </div>
                </div>

                <!-- Change Priority -->
                <div class="form-group">
                    <label for="selectAdminPrioridad" class="form-label">Nivel de Prioridad</label>
                    <div style="display:flex; gap:8px;">
                        <select id="selectAdminPrioridad" class="form-select">
                            <option value="BAJA">BAJA</option>
                            <option value="MEDIA">MEDIA</option>
                            <option value="ALTA">ALTA</option>
                            <option value="URGENTE">URGENTE</option>
                        </select>
                        <button type="button" id="btnGuardarPrioridad" class="btn btn-primary btn-sm" onclick="guardarPrioridad()">
                            Guardar
                        </button>
                    </div>
                </div>

                <!-- Assign Responsible -->
                <div class="form-group" style="margin-bottom:0;">
                    <label for="selectAdminResponsable" class="form-label">Responsable Asignado</label>
                    <div style="display:flex; gap:8px;">
                        <select id="selectAdminResponsable" class="form-select">
                            <option value="">Seleccionar personal...</option>
                        </select>
                        <button type="button" id="btnGuardarResponsable" class="btn btn-primary btn-sm" onclick="guardarResponsable()">
                            Asignar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- History / Timeline Card -->
        <div class="card">
            <div class="card-header">
                <span class="card-title">Historial de Trazabilidad</span>
            </div>
            <div class="card-body">
                <div id="historialTimeline" class="timeline">
                    <div class="empty-state">Cargando historial...</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const solicitudId = {{ $solicitudId }};

    async function cargarAdministrativos(responsableActualId) {
        try {
            const res = await campus.fetch('/api/administrativos');
            if (res && res.success) {
                const select = document.getElementById('selectAdminResponsable');
                select.innerHTML = '<option value="">Seleccionar personal administrativo...</option>';
                res.data.forEach(user => {
                    const opt = document.createElement('option');
                    opt.value = user.id;
                    opt.textContent = `${user.name} (${user.email})`;
                    if (responsableActualId && user.id === responsableActualId) {
                        opt.selected = true;
                    }
                    select.appendChild(opt);
                });
            }
        } catch (e) {
            console.error('Error al cargar lista de administrativos:', e);
        }
    }

    async function cargarDetalle() {
        try {
            const res = await campus.fetch(`/api/solicitudes/${solicitudId}`);
            if (res && res.success) {
                const sol = res.data;

                // Header
                document.getElementById('solTitulo').textContent = sol.titulo;
                document.getElementById('solSubtitulo').textContent = `Registrada el ${campus.formatDateTime(sol.created_at)} por ${sol.estudiante?.name || 'Estudiante'}`;
                document.getElementById('badgeEstadoHeader').innerHTML = campus.statusBadge(sol.estado);
                document.getElementById('badgePrioridadHeader').innerHTML = campus.priorityBadge(sol.prioridad);
                document.getElementById('badgeTipoHeader').innerHTML = campus.tipoBadge(sol.tipo);

                // Information
                document.getElementById('infoEstudiante').textContent = sol.estudiante ? `${sol.estudiante.name} (${sol.estudiante.email})` : '—';
                document.getElementById('infoUbicacion').textContent = sol.ubicacion || 'No especificada';
                document.getElementById('infoFecha').textContent = campus.formatDateTime(sol.created_at);
                document.getElementById('infoResponsable').textContent = sol.responsable ? sol.responsable.name : 'Sin asignar';
                document.getElementById('infoDescripcion').textContent = sol.descripcion || 'Sin descripción';

                // Controls
                document.getElementById('selectAdminEstado').value = sol.estado;
                document.getElementById('selectAdminPrioridad').value = sol.prioridad;

                // Populate and select Responsable
                await cargarAdministrativos(sol.responsable?.id || null);

                // Evidences
                renderEvidencias(sol.evidencias || []);

                // Comments
                renderComentarios(sol.comentarios || []);

                // History
                renderHistorial(sol.historial || []);
            }
        } catch (error) {
            console.error('Error al cargar solicitud:', error);
            campus.toast('Error al consultar datos de la solicitud', 'error');
        }
    }

    function renderEvidencias(evidencias) {
        const container = document.getElementById('evidenciasContainer');
        document.getElementById('evidenciasCount').textContent = `${evidencias.length} Archivos`;
        container.innerHTML = '';

        if (evidencias.length === 0) {
            container.innerHTML = `<div class="empty-state" style="grid-column: 1 / -1;">No hay evidencias adjuntas para esta solicitud.</div>`;
            return;
        }

        evidencias.forEach(ev => {
            const isImage = ev.tipo_archivo && ev.tipo_archivo.startsWith('image/');
            const card = document.createElement('a');
            card.className = 'evidence-card';
            card.href = `/storage/${ev.ruta}`;
            card.target = '_blank';

            card.innerHTML = `
                <div class="evidence-preview">
                    ${isImage ? `<img src="/storage/${ev.ruta}" alt="${ev.nombre_original}">` : `
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14 2 14 8 20 8"></polyline>
                            <line x1="16" y1="13" x2="8" y2="13"></line>
                            <line x1="16" y1="17" x2="8" y2="17"></line>
                        </svg>
                    `}
                </div>
                <div class="evidence-name" title="${ev.nombre_original}">${ev.nombre_original}</div>
                <div style="font-size:11px; color:var(--text-muted);">${campus.formatDate(ev.created_at)}</div>
            `;
            container.appendChild(card);
        });
    }

    function renderComentarios(comentarios) {
        const list = document.getElementById('comentariosList');
        document.getElementById('comentariosCount').textContent = `${comentarios.length} Comentarios`;
        list.innerHTML = '';

        if (comentarios.length === 0) {
            list.innerHTML = `<div class="empty-state">No hay comentarios registrados aún.</div>`;
            return;
        }

        comentarios.forEach(c => {
            const bubble = document.createElement('div');
            bubble.className = 'comment-bubble';
            bubble.innerHTML = `
                <div class="comment-header">
                    <span class="comment-author">${c.usuario?.name || 'Usuario'}</span>
                    <span class="comment-date">${campus.formatDateTime(c.created_at)}</span>
                </div>
                <div class="comment-content">${c.contenido}</div>
            `;
            list.appendChild(bubble);
        });
    }

    function renderHistorial(historial) {
        const timeline = document.getElementById('historialTimeline');
        timeline.innerHTML = '';

        if (historial.length === 0) {
            timeline.innerHTML = `<div class="empty-state">No hay eventos en el historial.</div>`;
            return;
        }

        historial.forEach(h => {
            const item = document.createElement('div');
            item.className = 'timeline-item';
            
            let detalleTexto = h.descripcion || '';
            if (h.estado_anterior && h.estado_nuevo) {
                detalleTexto = `${h.estado_anterior} &rarr; ${h.estado_nuevo}`;
            }

            item.innerHTML = `
                <div class="timeline-dot"></div>
                <div class="timeline-time">${campus.formatDateTime(h.created_at)} &bull; ${h.usuario?.name || 'Sistema'}</div>
                <div class="timeline-action">${h.accion.replace(/_/g, ' ')}</div>
                ${detalleTexto ? `<div class="timeline-desc">${detalleTexto}</div>` : ''}
            `;
            timeline.appendChild(item);
        });
    }

    async function guardarEstado() {
        const estado = document.getElementById('selectAdminEstado').value;
        const btn = document.getElementById('btnGuardarEstado');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner"></span>';

        try {
            const res = await campus.fetch(`/api/solicitudes/${solicitudId}/estado`, {
                method: 'PATCH',
                body: JSON.stringify({ estado })
            });

            if (res && res.success) {
                campus.toast('Estado actualizado correctamente', 'success');
                await cargarDetalle();
            }
        } catch (err) {
            campus.toast(err.message || 'Error al actualizar estado', 'error');
        } finally {
            btn.disabled = false;
            btn.textContent = 'Guardar';
        }
    }

    async function guardarPrioridad() {
        const prioridad = document.getElementById('selectAdminPrioridad').value;
        const btn = document.getElementById('btnGuardarPrioridad');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner"></span>';

        try {
            const res = await campus.fetch(`/api/solicitudes/${solicitudId}/prioridad`, {
                method: 'PATCH',
                body: JSON.stringify({ prioridad })
            });

            if (res && res.success) {
                campus.toast('Prioridad actualizada correctamente', 'success');
                await cargarDetalle();
            }
        } catch (err) {
            campus.toast(err.message || 'Error al actualizar prioridad', 'error');
        } finally {
            btn.disabled = false;
            btn.textContent = 'Guardar';
        }
    }

    async function guardarResponsable() {
        const responsable_id = document.getElementById('selectAdminResponsable').value;
        if (!responsable_id) {
            campus.toast('Debe seleccionar un responsable', 'warning');
            return;
        }

        const btn = document.getElementById('btnGuardarResponsable');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner"></span>';

        try {
            const res = await campus.fetch(`/api/solicitudes/${solicitudId}/responsable`, {
                method: 'PATCH',
                body: JSON.stringify({ responsable_id: parseInt(responsable_id) })
            });

            if (res && res.success) {
                campus.toast('Responsable asignado correctamente', 'success');
                await cargarDetalle();
            }
        } catch (err) {
            campus.toast(err.message || 'Error al asignar responsable', 'error');
        } finally {
            btn.disabled = false;
            btn.textContent = 'Asignar';
        }
    }

    document.getElementById('commentForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        const textarea = document.getElementById('txtComentario');
        const contenido = textarea.value.trim();
        if (!contenido) return;

        const btn = document.getElementById('btnEnviarComentario');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner"></span> Enviando...';

        try {
            const res = await campus.fetch(`/api/solicitudes/${solicitudId}/comentarios`, {
                method: 'POST',
                body: JSON.stringify({ contenido })
            });

            if (res && res.success) {
                campus.toast('Comentario agregado correctamente', 'success');
                textarea.value = '';
                await cargarDetalle();
            }
        } catch (err) {
            campus.toast(err.message || 'Error al agregar comentario', 'error');
        } finally {
            btn.disabled = false;
            btn.innerHTML = `
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
                <span>Enviar Comentario</span>
            `;
        }
    });

    document.addEventListener('DOMContentLoaded', cargarDetalle);
</script>
@endpush
