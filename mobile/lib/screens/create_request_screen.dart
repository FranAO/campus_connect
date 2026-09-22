import 'package:flutter/material.dart';
import 'package:image_picker/image_picker.dart';

import '../services/request_service.dart';
import '../services/validators.dart';

class CreateRequestScreen extends StatefulWidget {
  const CreateRequestScreen({super.key});

  @override
  State<CreateRequestScreen> createState() => _CreateRequestScreenState();
}

class _CreateRequestScreenState extends State<CreateRequestScreen> {
  final _formKey = GlobalKey<FormState>();
  final _titleController = TextEditingController();
  final _descriptionController = TextEditingController();
  final _picker = ImagePicker();

  String _category = 'Mantenimiento';
  String _priority = 'Media';
  XFile? _evidence;
  bool _saving = false;

  static const _categories = [
    'Mantenimiento',
    'Soporte tecnológico',
    'Infraestructura',
    'Equipamiento',
    'Otros',
  ];

  static const _priorities = ['Baja', 'Media', 'Alta'];

  @override
  void dispose() {
    _titleController.dispose();
    _descriptionController.dispose();
    super.dispose();
  }

  Future<void> _pickEvidence() async {
    final file = await _picker.pickImage(
      source: ImageSource.gallery,
      imageQuality: 80,
    );
    if (file != null) setState(() => _evidence = file);
  }

  Future<void> _save() async {
    if (!_formKey.currentState!.validate()) return;
    setState(() => _saving = true);
    try {
      await RequestService.create(
        title: _titleController.text.trim(),
        description: _descriptionController.text.trim(),
        category: _category,
        priority: _priority,
        evidencePath: _evidence?.path,
      );
      if (!mounted) return;
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Solicitud registrada correctamente')),
      );
      Navigator.of(context).pop();
    } catch (e) {
      if (!mounted) return;
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text(e.toString().replaceFirst('Exception: ', ''))),
      );
    } finally {
      if (mounted) setState(() => _saving = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Nueva solicitud')),
      body: Form(
        key: _formKey,
        child: ListView(
          padding: const EdgeInsets.all(16),
          children: [
            TextFormField(
              controller: _titleController,
              validator: (v) => Validators.requiredField(v, label: 'El título'),
              decoration: const InputDecoration(
                labelText: 'Título',
                hintText: 'Ej.: Aire acondicionado no funciona',
              ),
            ),
            const SizedBox(height: 16),
            DropdownButtonFormField<String>(
              value: _category,
              decoration: const InputDecoration(labelText: 'Categoría'),
              items: _categories
                  .map((e) => DropdownMenuItem(value: e, child: Text(e)))
                  .toList(),
              onChanged: (v) => setState(() => _category = v!),
            ),
            const SizedBox(height: 16),
            DropdownButtonFormField<String>(
              value: _priority,
              decoration: const InputDecoration(labelText: 'Prioridad'),
              items: _priorities
                  .map((e) => DropdownMenuItem(value: e, child: Text(e)))
                  .toList(),
              onChanged: (v) => setState(() => _priority = v!),
            ),
            const SizedBox(height: 16),
            TextFormField(
              controller: _descriptionController,
              minLines: 4,
              maxLines: 7,
              validator: (v) => Validators.requiredField(v, label: 'La descripción'),
              decoration: const InputDecoration(
                labelText: 'Descripción',
                hintText: 'Indica qué sucede y dónde ocurre.',
                alignLabelWithHint: true,
              ),
            ),
            const SizedBox(height: 16),
            OutlinedButton.icon(
              onPressed: _pickEvidence,
              icon: const Icon(Icons.attach_file),
              label: Text(
                _evidence == null
                    ? 'Adjuntar evidencia'
                    : 'Evidencia: ${_evidence!.name}',
                overflow: TextOverflow.ellipsis,
              ),
            ),
            if (_evidence != null)
              Align(
                alignment: Alignment.centerRight,
                child: TextButton.icon(
                  onPressed: () => setState(() => _evidence = null),
                  icon: const Icon(Icons.delete_outline),
                  label: const Text('Quitar evidencia'),
                ),
              ),
            const SizedBox(height: 20),
            FilledButton.icon(
              onPressed: _saving ? null : _save,
              icon: _saving
                  ? const SizedBox(
                      width: 18,
                      height: 18,
                      child: CircularProgressIndicator(strokeWidth: 2),
                    )
                  : const Icon(Icons.send_outlined),
              label: Text(_saving ? 'Registrando...' : 'Registrar solicitud'),
            ),
          ],
        ),
      ),
    );
  }
}
