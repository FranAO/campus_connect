import 'package:flutter/material.dart';

class StatusChip extends StatelessWidget {
  final String status;

  const StatusChip({super.key, required this.status});

  @override
  Widget build(BuildContext context) {
    IconData icon;
    switch (status.toLowerCase()) {
      case 'cerrada':
      case 'resuelta':
        icon = Icons.check_circle_outline;
        break;
      case 'en proceso':
        icon = Icons.sync;
        break;
      default:
        icon = Icons.schedule;
    }

    return Chip(
      avatar: Icon(icon, size: 18),
      label: Text(status),
      visualDensity: VisualDensity.compact,
    );
  }
}
