# Campus Connect - contrato API propuesto para móvil

Este archivo es una propuesta inicial para conectar la app Flutter con el backend. Debe ajustarse cuando el equipo defina los endpoints reales.

## Autenticación

### POST /api/login
Body JSON:
```json
{
  "email": "estudiante@universidad.edu",
  "password": "123456"
}
```
Respuesta esperada:
```json
{
  "token": "...",
  "user": {
    "id": 1,
    "name": "Estudiante",
    "email": "estudiante@universidad.edu"
  }
}
```

## Solicitudes

### GET /api/solicitudes
Header: `Authorization: Bearer {token}`

### POST /api/solicitudes
Tipo: `multipart/form-data`
Campos:
- titulo
- descripcion
- categoria
- prioridad
- evidencia (opcional)

### GET /api/solicitudes/{id}
Debe devolver estado actual, responsable si corresponde, historial y comentarios.

## Estados sugeridos
- Pendiente
- En proceso
- Resuelta
- Cerrada

## Categorías iniciales
- Mantenimiento
- Soporte tecnológico
- Infraestructura
- Equipamiento
- Otros
