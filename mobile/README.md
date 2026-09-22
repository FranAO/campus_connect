# Campus Connect Mobile

Primera base funcional de la aplicación móvil solicitada para el caso de universidad.

## Tecnología
- Flutter
- Consumo de API REST con `http`
- Selección de evidencia con `image_picker`
- Token local con `shared_preferences`

## Funciones incluidas
1. Inicio de sesión.
2. Listado de solicitudes del estudiante.
3. Crear solicitud.
4. Seleccionar categoría y prioridad.
5. Adjuntar una imagen como evidencia.
6. Consultar detalle, estado y comentarios.
7. Manejo básico de errores y validación de formularios.
8. 5 pruebas unitarias de validación.

## Modo demostración
`lib/core/api_config.dart` contiene:

```dart
static const bool mockMode = true;
```

Con `true`, la app funciona sin backend. Para conectar la API real:
1. Cambiar `mockMode` a `false`.
2. Ajustar `baseUrl`.
3. Revisar los endpoints de `AuthService` y `RequestService`.
4. Adaptar nombres JSON al contrato real.

## Crear las carpetas nativas y ejecutar

Como este paquete contiene el código fuente inicial, en la carpeta del proyecto ejecute una sola vez:

```bash
flutter create .
flutter pub get
flutter run
```

`flutter create .` genera las carpetas nativas de Android/iOS/Windows sin cambiar el diseño funcional planteado.

Para Android Emulator, el ejemplo usa `http://10.0.2.2:8000/api` para acceder al localhost del equipo.

## Próximo desarrollo recomendado
- Endpoint real de login.
- Endpoint real de solicitudes.
- Historial de estados con fechas.
- Comentarios cargados desde backend.
- Cámara además de galería.
- Notificaciones cuando cambie el estado.
- Pruebas de widgets e integración API.
