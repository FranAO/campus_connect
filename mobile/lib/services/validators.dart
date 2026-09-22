class Validators {
  static String? requiredField(String? value, {String label = 'Este campo'}) {
    if (value == null || value.trim().isEmpty) {
      return '$label es obligatorio';
    }
    return null;
  }

  static String? email(String? value) {
    if (value == null || value.trim().isEmpty) {
      return 'El correo es obligatorio';
    }
    final regex = RegExp(r'^[^@\s]+@[^@\s]+\.[^@\s]+$');
    if (!regex.hasMatch(value.trim())) {
      return 'Ingrese un correo válido';
    }
    return null;
  }

  static String? password(String? value) {
    if (value == null || value.isEmpty) {
      return 'La contraseña es obligatoria';
    }
    if (value.length < 6) {
      return 'Debe tener al menos 6 caracteres';
    }
    return null;
  }
}
