import 'package:flutter/material.dart';

import '../models/request_model.dart';
import '../widgets/status_chip.dart';

class RequestDetailScreen extends StatelessWidget {
  final RequestModel request;

  const RequestDetailScreen({super.key, required this.request});

  String _date(DateTime value) {
    String two(int n) => n.toString().padLeft(2, '0');
    return '${two(value.day)}/${two(value.month)}/${value.year} ${two(value.hour)}:${two(value.minute)}';
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: Text('Solicitud #${request.id}')),
      body: ListView(
        padding: const EdgeInsets.all(16),
        children: [
          Text(
            request.title,
            style: Theme.of(context).textTheme.headlineSmall?.copyWith(
                  fontWeight: FontWeight.bold,
                ),
          ),
          const SizedBox(height: 12),
          Align(
            alignment: Alignment.centerLeft,
            child: StatusChip(status: request.status),
          ),
          const SizedBox(height: 16),
          _Info(label: 'Categoría', value: request.category),
          _Info(label: 'Prioridad', value: request.priority),
          _Info(label: 'Fecha de registro', value: _date(request.createdAt)),
          const SizedBox(height: 12),
          Text('Descripción', style: Theme.of(context).textTheme.titleMedium),
          const SizedBox(height: 6),
          Text(request.description),
          const SizedBox(height: 20),
          ListTile(
            contentPadding: EdgeInsets.zero,
            leading: const Icon(Icons.attachment),
            title: const Text('Evidencia'),
            subtitle: Text(
              request.evidencePath == null
                  ? 'No se adjuntó evidencia.'
                  : 'Archivo adjunto registrado.',
            ),
          ),
          const Divider(height: 32),
          Text('Seguimiento y comentarios', style: Theme.of(context).textTheme.titleMedium),
          const SizedBox(height: 10),
          if (request.comments.isEmpty)
            const Text('Todavía no hay comentarios.'),
          ...request.comments.asMap().entries.map(
                (entry) => Card(
                  child: ListTile(
                    leading: CircleAvatar(child: Text('${entry.key + 1}')),
                    title: Text(entry.value),
                  ),
                ),
              ),
        ],
      ),
    );
  }
}

class _Info extends StatelessWidget {
  final String label;
  final String value;

  const _Info({required this.label, required this.value});

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 4),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          SizedBox(
            width: 140,
            child: Text(label, style: const TextStyle(fontWeight: FontWeight.w600)),
          ),
          Expanded(child: Text(value)),
        ],
      ),
    );
  }
}
