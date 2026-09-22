class RequestModel {
  final int id;
  final String title;
  final String description;
  final String category;
  final String priority;
  final String status;
  final DateTime createdAt;
  final String? evidencePath;
  final List<String> comments;

  const RequestModel({
    required this.id,
    required this.title,
    required this.description,
    required this.category,
    required this.priority,
    required this.status,
    required this.createdAt,
    this.evidencePath,
    this.comments = const [],
  });

  factory RequestModel.fromJson(Map<String, dynamic> json) {
    return RequestModel(
      id: json['id'] as int,
      title: (json['title'] ?? json['titulo'] ?? '') as String,
      description: (json['description'] ?? json['descripcion'] ?? '') as String,
      category: (json['category'] ?? json['categoria'] ?? '') as String,
      priority: (json['priority'] ?? json['prioridad'] ?? 'Media') as String,
      status: (json['status'] ?? json['estado'] ?? 'Pendiente') as String,
      createdAt: DateTime.tryParse((json['created_at'] ?? '').toString()) ?? DateTime.now(),
      evidencePath: json['evidence_path']?.toString(),
      comments: (json['comments'] as List<dynamic>? ?? const [])
          .map((e) => e.toString())
          .toList(),
    );
  }

  Map<String, dynamic> toJson() => {
        'id': id,
        'title': title,
        'description': description,
        'category': category,
        'priority': priority,
        'status': status,
        'created_at': createdAt.toIso8601String(),
        'evidence_path': evidencePath,
        'comments': comments,
      };
}
