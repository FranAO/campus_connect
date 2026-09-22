/**
 * Campus Connect - Web Administrative Client
 * Centralized API & UI helper
 */

(function () {
    // CSRF Token reader
    function getCsrfToken() {
        const meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : '';
    }

    // Toast notification manager
    function showToast(message, type = 'info') {
        let container = document.getElementById('toastContainer');
        if (!container) {
            container = document.createElement('div');
            container.id = 'toastContainer';
            container.className = 'toast-container';
            document.body.appendChild(container);
        }

        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        
        let iconSvg = '';
        if (type === 'success') {
            iconSvg = `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>`;
        } else if (type === 'error') {
            iconSvg = `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>`;
        } else {
            iconSvg = `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#3b82f6" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>`;
        }

        toast.innerHTML = `${iconSvg}<span>${message}</span>`;
        container.appendChild(toast);

        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateY(10px)';
            toast.style.transition = 'all 0.3s ease';
            setTimeout(() => toast.remove(), 300);
        }, 4000);
    }

    // Centralized Fetch wrapper for Sanctum Session / API
    async function campusFetch(url, options = {}) {
        const defaultHeaders = {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': getCsrfToken()
        };

        if (!(options.body instanceof FormData)) {
            defaultHeaders['Content-Type'] = 'application/json';
        }

        const mergedOptions = {
            ...options,
            credentials: 'same-origin',
            headers: {
                ...defaultHeaders,
                ...(options.headers || {})
            }
        };

        try {
            const response = await fetch(url, mergedOptions);
            const data = await response.json().catch(() => null);

            if (response.status === 401) {
                showToast('Sesión caducada. Redirigiendo...', 'error');
                setTimeout(() => {
                    window.location.href = '/login';
                }, 1000);
                throw new Error('No autenticado');
            }

            if (response.status === 403) {
                showToast(data?.message || 'Acceso denegado: acción no permitida.', 'error');
                throw new Error(data?.message || 'Acceso denegado');
            }

            if (!response.ok) {
                const errorMsg = data?.message || (data?.errors ? Object.values(data.errors).flat().join(' ') : 'Error en la solicitud');
                throw new Error(errorMsg);
            }

            return data;
        } catch (error) {
            console.error('campusFetch error:', error);
            throw error;
        }
    }

    // Badges UI helpers
    function statusBadge(estado) {
        if (!estado) return '<span class="badge badge-estado-PENDIENTE">PENDIENTE</span>';
        const label = estado.replace('_', ' ');
        return `<span class="badge badge-estado-${estado}">${label}</span>`;
    }

    function priorityBadge(prioridad) {
        if (!prioridad) return '<span class="badge badge-prioridad-MEDIA">MEDIA</span>';
        return `<span class="badge badge-prioridad-${prioridad}">${prioridad}</span>`;
    }

    function tipoBadge(tipo) {
        if (!tipo) return '<span class="badge badge-tipo">GENERAL</span>';
        const label = tipo.replace('_', ' ');
        return `<span class="badge badge-tipo">${label}</span>`;
    }

    function recursoBadge(estado) {
        if (!estado) return '<span class="badge badge-recurso-DISPONIBLE">DISPONIBLE</span>';
        const label = estado.replace('_', ' ');
        return `<span class="badge badge-recurso-${estado}">${label}</span>`;
    }

    // Formatting date
    function formatDateTime(dateStr) {
        if (!dateStr) return '—';
        const d = new Date(dateStr);
        if (isNaN(d.getTime())) return dateStr;
        return d.toLocaleDateString('es-ES', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    }

    function formatDate(dateStr) {
        if (!dateStr) return '—';
        const d = new Date(dateStr);
        if (isNaN(d.getTime())) return dateStr;
        return d.toLocaleDateString('es-ES', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric'
        });
    }

    // Setup global logout buttons
    document.addEventListener('DOMContentLoaded', () => {
        const logoutBtns = document.querySelectorAll('.btn-logout');
        logoutBtns.forEach(btn => {
            btn.addEventListener('click', async (e) => {
                e.preventDefault();
                btn.disabled = true;
                btn.innerHTML = `<span class="spinner"></span> Saliendo...`;
                try {
                    await campusFetch('/logout', { method: 'POST' });
                    window.location.href = '/login';
                } catch (err) {
                    window.location.href = '/login';
                }
            });
        });
    });

    // Expose helpers globally
    window.campus = {
        fetch: campusFetch,
        toast: showToast,
        statusBadge,
        priorityBadge,
        tipoBadge,
        recursoBadge,
        formatDateTime,
        formatDate
    };
})();
