# Campus Connect API

Backend REST para solicitudes universitarias. Laravel 12, Sanctum y PostgreSQL.

## Requisitos

- PHP 8.2 o superior con `pdo_pgsql`, `mbstring`, `openssl`, `fileinfo` y `zip`
- Composer 2
- PostgreSQL

## Configuración

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
php artisan serve
```

Configurar conexión PostgreSQL en `.env`:

```dotenv
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=campus_connect
DB_USERNAME=root
DB_PASSWORD=
```

## Usuarios de prueba

| Rol | Email | Contraseña |
|---|---|---|
| Administrativo | `admin@campusconnect.com` | `12345678` |
| Estudiante | `estudiante@campusconnect.com` | `12345678` |

## Autenticación

Enviar token devuelto por `POST /api/login`:

```http
Authorization: Bearer TOKEN
Accept: application/json
```

## Endpoints

### Auth

```text
POST   /api/login
GET    /api/me
POST   /api/logout
```

### Solicitudes

```text
GET    /api/solicitudes
POST   /api/solicitudes
GET    /api/solicitudes/{id}
PUT    /api/solicitudes/{id}
DELETE /api/solicitudes/{id}
GET    /api/solicitudes/{id}/historial
GET    /api/solicitudes/{id}/comentarios
POST   /api/solicitudes/{id}/evidencias
DELETE /api/evidencias/{id}
```

Filtros de listado: `estado`, `tipo`, `prioridad`, `responsable_id`, `per_page`.

Tipos: `MANTENIMIENTO`, `SOPORTE_TECNOLOGICO`, `INFRAESTRUCTURA`, `EQUIPAMIENTO`, `OTRO`.

### Administración

```text
POST  /api/solicitudes/{id}/comentarios
PATCH /api/solicitudes/{id}/estado
PATCH /api/solicitudes/{id}/prioridad
PATCH /api/solicitudes/{id}/responsable
GET   /api/dashboard
GET   /api/reportes/solicitudes
```

Filtros de reporte: `estado`, `tipo`, `prioridad`, `responsable_id`, `desde`, `hasta`.

### Recursos

```text
GET    /api/recursos
POST   /api/recursos
GET    /api/recursos/{id}
PUT    /api/recursos/{id}
DELETE /api/recursos/{id}
```

## Evidencias

Usar `multipart/form-data`, campo `archivo`. Formatos: JPG, PNG o PDF. Máximo 10 MB.

## Pruebas

```bash
php artisan test
```

Colección lista para importar: `docs/Campus_Connect.postman_collection.json`.
