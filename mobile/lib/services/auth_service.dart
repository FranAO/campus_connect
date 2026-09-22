import 'dart:convert';

import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';

import '../core/api_config.dart';

class AuthService {
  static Future<void> login(String email, String password) async {
    if (ApiConfig.mockMode) {
      await Future<void>.delayed(const Duration(milliseconds: 500));
      final prefs = await SharedPreferences.getInstance();
      await prefs.setString('token', 'token-demo-campus-connect');
      await prefs.setString('email', email);
      return;
    }

    final response = await http.post(
      Uri.parse('${ApiConfig.baseUrl}/login'),
      headers: {'Content-Type': 'application/json'},
      body: jsonEncode({'email': email, 'password': password}),
    );

    if (response.statusCode < 200 || response.statusCode >= 300) {
      throw Exception('No se pudo iniciar sesión');
    }

    final data = jsonDecode(response.body) as Map<String, dynamic>;
    final token = (data['token'] ?? data['access_token'])?.toString();
    if (token == null || token.isEmpty) {
      throw Exception('La API no devolvió un token');
    }

    final prefs = await SharedPreferences.getInstance();
    await prefs.setString('token', token);
    await prefs.setString('email', email);
  }

  static Future<String?> token() async {
    final prefs = await SharedPreferences.getInstance();
    return prefs.getString('token');
  }

  static Future<void> logout() async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.remove('token');
    await prefs.remove('email');
  }
}
