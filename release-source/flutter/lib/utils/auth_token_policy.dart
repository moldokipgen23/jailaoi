class AuthTokenPolicy {
  static bool isSessionToken(dynamic value) =>
      value is String && RegExp(r'^\d+\|[A-Za-z0-9]{40}$').hasMatch(value);

  static bool shouldSave(String requestPath, dynamic body) {
    final endpoint = Uri.parse(requestPath).path.split('/').last;
    return ['login', 'register'].contains(endpoint) &&
        body is Map &&
        body['status'] == 200 &&
        isSessionToken(body['token']);
  }
}
