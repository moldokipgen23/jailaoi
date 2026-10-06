import 'package:flutter_test/flutter_test.dart';
import 'package:jailaoi/utils/auth_token_policy.dart';

void main() {
  final session = '12|${List.filled(40, 'a').join()}';
  test('login and registration can save a valid server session', () {
    expect(
        AuthTokenPolicy.shouldSave('https://portal.jailaoi.com/api/login',
            {'status': 200, 'token': session}),
        isTrue);
    expect(
        AuthTokenPolicy.shouldSave('https://portal.jailaoi.com/api/register',
            {'status': 200, 'token': session}),
        isTrue);
  });
  test('portal response never replaces the login session', () {
    expect(
        AuthTokenPolicy.shouldSave(
            'https://portal.jailaoi.com/api/generate_portal_token',
            {'status': 200, 'token': session}),
        isFalse);
    expect(
        AuthTokenPolicy.isSessionToken(List.filled(48, 'a').join()), isFalse);
  });
  test('failed login and malformed tokens cannot be stored', () {
    expect(
        AuthTokenPolicy.shouldSave(
            '/api/login', {'status': 400, 'token': session}),
        isFalse);
    expect(
        AuthTokenPolicy.shouldSave(
            '/api/login', {'status': 200, 'token': '12|short'}),
        isFalse);
    expect(AuthTokenPolicy.isSessionToken(null), isFalse);
  });
}
