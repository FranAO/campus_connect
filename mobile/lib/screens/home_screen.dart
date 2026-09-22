import 'package:flutter/material.dart';

import '../models/request_model.dart';
import '../services/auth_service.dart';
import '../services/request_store.dart';
import '../widgets/status_chip.dart';
import 'create_request_screen.dart';
import 'login_screen.dart';
import 'request_detail_screen.dart';

class HomeScreen extends StatelessWidget {
  const HomeScreen({super.key});

  Future<void> _logout(BuildContext context) async {
    await AuthService.logout();
    if (!context.mounted) return;
    Navigator.of(context).pushAndRemoveUntil(
      MaterialPageRoute(builder: (_) => const LoginScreen()),
      (_) => false,
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Mis solicitudes'),
        actions: [
          IconButton(
            tooltip: 'Cerrar sesión',
            onPressed: () => _logout(context),
            icon: const Icon(Icons.logout),
          ),
        ],
      ),
      floatingActionButton: FloatingActionButton.extended(
        onPressed: () => Navigator.of(context).push(
          MaterialPageRoute(builder: (_) => const CreateRequestScreen()),
        ),
        icon: const Icon(Icons.add),
        label: const Text('Nueva solicitud'),
      ),
      body: ValueListenableBuilder<List<RequestModel>>(
        valueListenable: RequestStore.instance.requests,
        builder: (context, requests, _) {
          if (requests.isEmpty) {
            return const Center(child: Text('Aún no tienes solicitudes.'));
          }
          return RefreshIndicator(
            onRefresh: () async => Future<void>.delayed(const Duration(milliseconds: 400)),
            child: ListView.separated(
              padding: const EdgeInsets.fromLTRB(16, 16, 16, 96),
              itemCount: requests.length,
              separatorBuilder: (_, __) => const SizedBox(height: 12),
              itemBuilder: (context, index) {
                final item = requests[index];
                return Card(
                  child: ListTile(
                    contentPadding: const EdgeInsets.all(16),
                    title: Text(
                      item.title,
                      style: const TextStyle(fontWeight: FontWeight.w700),
                    ),
                    subtitle: Padding(
                      padding: const EdgeInsets.only(top: 8),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text('${item.category} · Prioridad ${item.priority}'),
                          const SizedBox(height: 8),
                          StatusChip(status: item.status),
                        ],
                      ),
                    ),
                    trailing: const Icon(Icons.chevron_right),
                    onTap: () => Navigator.of(context).push(
                      MaterialPageRoute(
                        builder: (_) => RequestDetailScreen(request: item),
                      ),
                    ),
                  ),
                );
              },
            ),
          );
        },
      ),
    );
  }
}
