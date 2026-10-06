import 'dart:developer';

import 'package:jailaoi/model/cashfreeordermodel.dart';
import 'package:jailaoi/model/cashfreesubscriptionmodel.dart';
import 'package:jailaoi/model/paymentoptionmodel.dart';
import 'package:jailaoi/model/paytmmodel.dart';
import 'package:jailaoi/model/successmodel.dart';
import 'package:jailaoi/utils/constant.dart';
import 'package:jailaoi/webservice/apiservices.dart';
import 'package:flutter/material.dart';
import 'package:jailaoi/utils/utils.dart';

class PaymentProvider extends ChangeNotifier {
  PaymentProvider({ApiService? apiService}) : _api = apiService ?? ApiService();
  final ApiService _api;
  PaymentOptionModel paymentOptionModel = PaymentOptionModel();
  PayTmModel payTmModel = PayTmModel();
  SuccessModel successModel = SuccessModel();

  bool loading = false, payLoading = false, couponLoading = false;
  String? currentPayment = "", finalAmount = "";
  // Surfaces the *actual* failure reason on screen (network error, timeout,
  // JSON parse error, etc.) instead of silently rendering a blank payment
  // list — getPaymentOption() had no error handling at all, so any thrown
  // exception here left `loading` stuck and the real cause invisible.
  String? paymentOptionError;

  Future<void> getPaymentOption() async {
    loading = true;
    paymentOptionError = null;
    notifyListeners();
    try {
      paymentOptionModel = await _api.getPaymentOption();
      printLog("getPaymentOption status :==> ${paymentOptionModel.status}");
      printLog("getPaymentOption message :==> ${paymentOptionModel.message}");
      if (paymentOptionModel.status != 200) {
        paymentOptionError =
            "status=${paymentOptionModel.status} msg=${paymentOptionModel.message}";
      }
    } catch (e, stack) {
      printLog("getPaymentOption EXCEPTION ============> $e");
      printLog("getPaymentOption STACK ============> $stack");
      paymentOptionError = e.toString();
      paymentOptionModel = PaymentOptionModel();
    }
    loading = false;
    notifyListeners();
  }

  void setFinalAmount(String? amount) {
    finalAmount = amount;
    printLog("setFinalAmount finalAmount :==> $finalAmount");
    notifyListeners();
  }

  Future<void> getPaytmToken(dynamic merchantID, orderId, custmoreID, channelID,
      txnAmount, website, callbackURL, industryTypeID) async {
    printLog("getPaytmToken merchantID :=======> $merchantID");
    printLog("getPaytmToken orderId :==========> $orderId");
    printLog("getPaytmToken custmoreID :=======> $custmoreID");
    printLog("getPaytmToken channelID :========> $channelID");
    printLog("getPaytmToken txnAmount :========> $txnAmount");
    printLog("getPaytmToken website :==========> $merchantID");
    printLog("getPaytmToken callbackURL :======> $merchantID");
    printLog("getPaytmToken industryTypeID :===> $industryTypeID");
    loading = true;

    payTmModel = await _api.getPaytmToken(merchantID, orderId, custmoreID,
        channelID, txnAmount, website, callbackURL, industryTypeID);
    printLog("77777");
    printLog("getPaytmToken status :===> ${payTmModel.status}");
    printLog("getPaytmToken message :==> ${payTmModel.message}");
    loading = false;
    notifyListeners();
  }

  CashfreeOrderModel cashfreeOrderModel = CashfreeOrderModel();
  CashfreeOrderModel cashfreeVerifyModel = CashfreeOrderModel();

  Future<CashfreeOrderModel> createCashfreeOrder(
      dynamic packageId, amount, orderId, email, phone,
      {String? returnUrl}) async {
    payLoading = true;
    notifyListeners();
    try {
      cashfreeOrderModel = await _api.createCashfreeOrder(
          packageId, amount, orderId, email, phone,
          returnUrl: returnUrl);
      printLog("createCashfreeOrder status :==> ${cashfreeOrderModel.status}");
      printLog(
          "createCashfreeOrder message :==> ${cashfreeOrderModel.message}");

      return cashfreeOrderModel;
    } catch (_) {
      cashfreeOrderModel = CashfreeOrderModel(
          status: 503,
          message: 'Unable to contact the payment service. Please try again.');
      return cashfreeOrderModel;
    } finally {
      payLoading = false;
      notifyListeners();
    }
  }

  Future<CashfreeOrderModel> verifyCashfreeOrder(dynamic orderId) async {
    try {
      cashfreeVerifyModel = await _api.verifyCashfreeOrder(orderId);
    } catch (_) {
      cashfreeVerifyModel = CashfreeOrderModel(
          status: 503,
          message:
              'Payment confirmation is temporarily unavailable. Please try again.');
    }
    notifyListeners();
    return cashfreeVerifyModel;
  }

  Future<CashfreeOrderModel> cashfreeSubscriptionStatus(String id) async {
    try {
      return await _api.cashfreeSubscriptionStatus(id);
    } catch (_) {
      return CashfreeOrderModel(
          status: 503,
          message: 'Payment confirmation is temporarily unavailable.');
    }
  }

  CashfreeSubscriptionModel cashfreeSubscriptionModel =
      CashfreeSubscriptionModel();

  Future<CashfreeSubscriptionModel> createCashfreeSubscription(
      dynamic packageId, subscriptionId, email, phone, name,
      {String? returnUrl}) async {
    payLoading = true;
    notifyListeners();
    try {
      cashfreeSubscriptionModel = await _api.createCashfreeSubscription(
          packageId, subscriptionId, email, phone, name,
          returnUrl: returnUrl);
      printLog(
          "createCashfreeSubscription status :==> ${cashfreeSubscriptionModel.status}");
      printLog(
          "createCashfreeSubscription message :==> ${cashfreeSubscriptionModel.message}");

      return cashfreeSubscriptionModel;
    } catch (_) {
      cashfreeSubscriptionModel = CashfreeSubscriptionModel(
          status: 503,
          message: 'Unable to contact the payment service. Please try again.');
      return cashfreeSubscriptionModel;
    } finally {
      payLoading = false;
      notifyListeners();
    }
  }

  Future<SuccessModel> cancelCashfreeSubscription(
      dynamic subscriptionId) async {
    payLoading = true;
    notifyListeners();
    try {
      successModel = await _api.cancelCashfreeSubscription(subscriptionId);

      return successModel;
    } catch (_) {
      successModel = SuccessModel(
          status: 503,
          message: 'Unable to contact the payment service. Please try again.');
      return successModel;
    } finally {
      payLoading = false;
      notifyListeners();
    }
  }

  Future<void> addTransaction(
      dynamic packageId, description, amount, paymentId) async {
    printLog("addTransaction userID :==> ${Constant.userID}");
    printLog("addTransaction packageId :==> $packageId");
    payLoading = true;
    notifyListeners();
    try {
      successModel =
          await _api.addTransaction(packageId, description, amount, paymentId);
    } catch (_) {
      successModel = SuccessModel(
          status: 503,
          message:
              'Payment confirmation is temporarily unavailable. Please try again.');
    } finally {
      payLoading = false;
      notifyListeners();
    }
  }

  Future<void> joinLiveEventTransaction(
      dynamic eventId, type, amount, transectionId, discription) async {
    printLog("addTransaction userID :==> ${Constant.userID}");
    payLoading = true;
    successModel = await _api.addLiveEventTransaction(
        eventId, type, amount, transectionId, discription);
    printLog("addTransaction status :==> ${successModel.status}");
    printLog("addTransaction message :==> ${successModel.message}");
    payLoading = false;
    notifyListeners();
  }

  void setCurrentPayment(String? payment) {
    currentPayment = payment;
    notifyListeners();
  }

  void clearProvider() {
    log("<================ clearProvider ================>");
    currentPayment = "";
    finalAmount = "";
    paymentOptionModel = PaymentOptionModel();
    successModel = SuccessModel();
    cashfreeOrderModel = CashfreeOrderModel();
    cashfreeVerifyModel = CashfreeOrderModel();
    cashfreeSubscriptionModel = CashfreeSubscriptionModel();
  }
}
