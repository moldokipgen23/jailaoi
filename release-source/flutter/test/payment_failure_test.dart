import 'package:flutter_test/flutter_test.dart';
import 'package:jailaoi/model/cashfreeordermodel.dart';
import 'package:jailaoi/model/cashfreesubscriptionmodel.dart';
import 'package:jailaoi/model/successmodel.dart';
import 'package:jailaoi/provider/paymentprovider.dart';
import 'package:jailaoi/webservice/apiservices.dart';

class OfflinePaymentApi extends ApiService {
  @override
  Future<CashfreeOrderModel> createCashfreeOrder(
          dynamic packageId, amount, orderId, email, phone,
          {String? returnUrl}) async =>
      throw Exception('offline');
  @override
  Future<CashfreeSubscriptionModel> createCashfreeSubscription(
          dynamic packageId, subscriptionId, email, phone, name,
          {String? returnUrl}) async =>
      throw Exception('offline');
  @override
  Future<CashfreeOrderModel> verifyCashfreeOrder(dynamic orderId) async =>
      throw Exception('offline');
  @override
  Future<CashfreeOrderModel> cashfreeSubscriptionStatus(String id) async =>
      throw Exception('offline');
  @override
  Future<SuccessModel> cancelCashfreeSubscription(dynamic id) async =>
      throw Exception('offline');
  @override
  Future<SuccessModel> addTransaction(
          dynamic packageId, description, amount, paymentId) async =>
      throw Exception('offline');
}

void main() {
  test(
      'offline checkout and cancellation stop loading without claiming payment',
      () async {
    final provider = PaymentProvider(apiService: OfflinePaymentApi());
    expect((await provider.createCashfreeOrder(7, 99, 'order', '', '')).status,
        503);
    expect(provider.payLoading, false);
    expect(
        (await provider.createCashfreeSubscription(7, 'sub', '', '', ''))
            .status,
        503);
    expect(provider.payLoading, false);
    expect((await provider.cancelCashfreeSubscription('sub')).status, 503);
    expect(provider.payLoading, false);
    await provider.addTransaction(7, 'plan', 99, 'order');
    expect(provider.successModel.status, 503);
    expect(provider.payLoading, false);
  });
  test('failed payment confirmation never reports paid access', () async {
    final provider = PaymentProvider(apiService: OfflinePaymentApi());
    expect((await provider.verifyCashfreeOrder('order')).result?.paid,
        isNot(true));
    expect((await provider.cashfreeSubscriptionStatus('sub')).result?.paid,
        isNot(true));
  });
}
