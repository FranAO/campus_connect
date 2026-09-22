import 'dart:convert';
import 'dart:io';

import 'package:http/http.dart' as http;

import '../core/api_config.dart';
import '../models/request_model.dart';
import 'auth_service.dart';
import 'request_store.dart';

class RequestService {
  static Future<List<RequestModel>> list() async {
    if (ApiConfig.mockMode) {
      return RequestStore.instance.requests.value;
    }

    final token = await AuthService.token();
    final response = await http.get(
      Uri.parse('${ApiConfig.baseUrl}/solicitudes'),
      headers: {
        'Accept': 'application/json',
        if (token != null) 'Authorization': 'Bearer $token',
      },
    );

    if (response.statusCode != 200) {
      throw Exception('No se pudieron cargar las solicitudes');
    }

    final decoded = jsonDecode(response.body);
    final list = decoded is List ? decoded : (decoded['data'] as List<dynamic>);
    return list
        .map((e) => RequestModel.fromJson(e as Map<String, dynamic>))
        .toList();
  }

  static Future<void> create({
    required String title,
    required String description,
    required String category,
    required String priority,
    String? evidencePath,
  }) async {
    if (ApiConfig.mockMode) {
      await Future<void>.delayed(const Duration(milliseconds: 450));
      RequestStore.instance.add(
        title: title,
        description: description,
        category: category,
        priority: priority,
        evidencePath: evidencePath,
      );
      return;
    }

    final token = await AuthService.token();
    final request = http.MultipartRequest(
      'POST',
      Uri.parse('${ApiConfig.baseUrl}/solicitudes'),
    );
    request.headers.addAll({
      'Accept': 'application/json',
      if (token != null) 'Authorization': 'Bearer $token',
    });
    request.fields.addAll({
      'titulo': title,
      'descripcion': description,
      'categoria': category,
      'prioridad': priority,
    });

    if (evidencePath != null && File(evidencePath).existsSync()) {
      request.files.add(await http.MultipartFile.fromPath('evidencia', evidencePath));
    }

    final response = await request.send();
    if (response.statusCode < 200 || response.statusCode >= 300) {
      throw Exception('No se pudo crear la solicitud');
    }
  }
}
