import 'package:flutter_test/flutter_test.dart';
import 'package:campus_connect_mobile/services/validators.dart';

void main() {
  group('Validators', () {
    test('rechaza correo inválido', () {
      expect(Validators.email('correo-malo'), isNotNull);
    });

    test('acepta correo válido', () {
      expect(Validators.email('estudiante@universidad.edu'), isNull);
    });

    test('rechaza contraseña corta', () {
      expect(Validators.password('123'), isNotNull);
    });

    test('acepta contraseña de 6 o más caracteres', () {
      expect(Validators.password('123456'), isNull);
    });

    test('rechaza campo obligatorio vacío', () {
      expect(Validators.requiredField(''), isNotNull);
    });
  });
}
