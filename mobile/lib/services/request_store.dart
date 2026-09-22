import 'package:flutter/foundation.dart';

import '../models/request_model.dart';

class RequestStore {
  RequestStore._();

  static final RequestStore instance = RequestStore._();

  final ValueNotifier<List<RequestModel>> requests = ValueNotifier([
    RequestModel(
      id: 1001,
      title: 'Proyector sin señal',
      description: 'El proyector del aula B-204 no muestra imagen.',
      category: 'Soporte tecnológico',
      priority: 'Media',
      status: 'En proceso',
      createdAt: DateTime.now().subtract(const Duration(days: 1)),
      comments: const [
        'Solicitud recibida por soporte.',
        'Se programó revisión del equipo para hoy.',
      ],
    ),
    RequestModel(
      id: 1000,
      title: 'Luminaria dañada',
      description: 'La luminaria del pasillo del bloque C no enciende.',
      category: 'Mantenimiento',
      priority: 'Baja',
      status: 'Pendiente',
      createdAt: DateTime.now().subtract(const Duration(days: 3)),
      comments: const ['Solicitud registrada correctamente.'],
    ),
  ]);

  int _nextId = 1002;

  void add({
    required String title,
    required String description,
    required String category,
    required String priority,
    String? evidencePath,
  }) {
    final item = RequestModel(
      id: _nextId++,
      title: title,
      description: description,
      category: category,
      priority: priority,
      status: 'Pendiente',
      createdAt: DateTime.now(),
      evidencePath: evidencePath,
      comments: const ['Solicitud registrada correctamente.'],
    );
    requests.value = [item, ...requests.value];
  }
}
