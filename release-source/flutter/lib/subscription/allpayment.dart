import 'dart:async';
import 'dart:convert';
import 'dart:io';
// import 'package:flutterwave_standard/core/flutterwave.dart';
// import 'package:flutterwave_standard/models/requests/customer.dart';
// import 'package:flutterwave_standard/models/requests/customizations.dart';
// import 'package:flutterwave_standard/models/responses/charge_response.dart';
import 'package:flutterwave_standard_smart/core/flutterwave.dart';
import 'package:flutterwave_standard_smart/models/requests/customer.dart';
import 'package:flutterwave_standard_smart/models/requests/customizations.dart';
import 'package:flutterwave_standard_smart/models/responses/charge_response.dart';
import 'package:jailaoi/music/musicdetails.dart';
import 'package:jailaoi/pages/homemusic.dart';
import 'package:jailaoi/provider/liveeventsprovider.dart';
import 'package:jailaoi/pages/nodata.dart';
import 'package:jailaoi/provider/paymentprovider.dart';
import 'package:jailaoi/provider/profileprovider.dart';
import 'package:jailaoi/utils/color.dart';
import 'package:jailaoi/utils/constant.dart';
import 'package:jailaoi/utils/dimens.dart';
import 'package:jailaoi/utils/sharedpref.dart';
import 'package:jailaoi/utils/strings.dart';
import 'package:jailaoi/utils/utils.dart';
import 'package:jailaoi/widget/myimage.dart';
import 'package:jailaoi/widget/mytext.dart';
import 'package:flutter/foundation.dart';
import 'package:flutter/material.dart';
import 'package:flutter_cashfree_pg_sdk/api/cferrorresponse/cferrorresponse.dart';
import 'package:flutter_cashfree_pg_sdk/api/cfpayment/cfsubscriptioncheckoutpayment.dart';
import 'package:flutter_cashfree_pg_sdk/api/cfpayment/cfwebcheckoutpayment.dart';
import 'package:flutter_cashfree_pg_sdk/api/cfpaymentgateway/cfpaymentgatewayservice.dart';
import 'package:flutter_cashfree_pg_sdk/api/cfsession/cfsession.dart';
import 'package:flutter_cashfree_pg_sdk/api/cfsession/cfsubssession.dart';
import 'package:flutter_cashfree_pg_sdk/utils/cfenums.dart';
import 'package:flutter_paypal/flutter_paypal.dart';
import 'package:flutter_stripe/flutter_stripe.dart' as stripe;
import 'package:http/http.dart' as http;
import 'package:google_fonts/google_fonts.dart';
import 'package:progress_dialog_null_safe/progress_dialog_null_safe.dart';
import 'package:in_app_purchase/in_app_purchase.dart';
import 'package:in_app_purchase_android/in_app_purchase_android.dart';
import 'package:in_app_purchase_storekit/in_app_purchase_storekit.dart';
import 'package:in_app_purchase_storekit/store_kit_wrappers.dart';
import 'package:provider/provider.dart';
import 'package:uuid/uuid.dart';

import 'package:razorpay_flutter/razorpay_flutter.dart';

final bool _kAutoConsume = Platform.isIOS || true;

class AllPayment extends StatefulWidget {
  final String? payType,
      itemId,
      price,
      itemTitle,
      typeId,
      contentType,
      productPackage,
      currency;
  const AllPayment({
    super.key,
    required this.payType,
    required this.itemId,
    required this.price,
    required this.itemTitle,
    required this.typeId,
    required this.contentType,
    required this.productPackage,
    required this.currency,
  });

  @override
  State<AllPayment> createState() => AllPaymentState();
}

class AllPaymentState extends State<AllPayment> {
  final couponController = TextEditingController();
  late ProgressDialog prDialog;
  late PaymentProvider paymentProvider;
  SharedPref sharedPref = SharedPref();
  String? userName, userEmail, userMobileNo, paymentId;
  String? strCouponCode = "";
  bool isPaymentDone = false;

  /* InApp Purchase */
  final InAppPurchase _inAppPurchase = InAppPurchase.instance;
  late StreamSubscription<List<PurchaseDetails>> _subscription;
  late List<String> _kProductIds;
  final List<PurchaseDetails> _purchases = <PurchaseDetails>[];

  /* Razorpay */
  Razorpay? _razorpay;

  /* Cashfree */
  final CFPaymentGatewayService _cfPaymentGatewayService =
      CFPaymentGatewayService();
  // The SDK's success/error callbacks are shared between the one-time order
  // flow and the subscription flow (same static callback signature), so this
  // flag is how the callback tells which one just completed.
  bool _cashfreePendingIsSubscription = false;

  /* Paytm */
  String paytmResult = "";

  /* Stripe */
  Map<String, dynamic>? paymentIntent;

  @override
  void initState() {
    prDialog = ProgressDialog(context);
    _getData();

    if (!kIsWeb) {
      /* InApp Purchase PG */
      _kProductIds = <String>[widget.productPackage ?? ""];
      final Stream<List<PurchaseDetails>> purchaseUpdated =
          _inAppPurchase.purchaseStream;
      _subscription =
          purchaseUpdated.listen((List<PurchaseDetails> purchaseDetailsList) {
        _listenToPurchaseUpdated(purchaseDetailsList);
      }, onDone: () {
        _subscription.cancel();
      }, onError: (Object error) {
        // handle error here.
        printLog("onError ============> ${error.toString()}");
      });
      initStoreInfo();
    }
    _cfPaymentGatewayService.setCallback(
        _cashfreeVerifyPayment, _cashfreeOnError);
    super.initState();
  }

  bool checkKeysAndContinue({
    required String isLive,
    required bool isBothKeyReq,
    required String liveKey1,
    required String liveKey2,
    required String testKey1,
    required String testKey2,
  }) {
    if (isLive == "1") {
      if (isBothKeyReq) {
        if (liveKey1 == "" || liveKey2 == "") {
          Utils.showSnackbar(context, "payment_not_processed", true);
          return false;
        }
      } else {
        if (liveKey1 == "") {
          Utils.showSnackbar(context, "payment_not_processed", true);
          return false;
        }
      }
      return true;
    } else {
      if (isBothKeyReq) {
        if (testKey1 == "" || testKey2 == "") {
          Utils.showSnackbar(context, "payment_not_processed", true);
          return false;
        }
      } else {
        if (testKey1 == "") {
          Utils.showSnackbar(context, "payment_not_processed", true);
          return false;
        }
      }
      return true;
    }
  }

  Future<void> _getData() async {
    paymentProvider = Provider.of<PaymentProvider>(context, listen: false);
    await paymentProvider.getPaymentOption();
    paymentProvider.setFinalAmount(widget.price ?? "");
    /* PaymentID */
    paymentId = Utils.generateRandomOrderID();
    print('paymentId =====================> $paymentId');

    userName = await sharedPref.read("username");
    userEmail = await sharedPref.read("useremail");
    userMobileNo = await sharedPref.read("usermobile");
    print('getUserData userName ==> $userName');
    print('getUserData userEmail ==> $userEmail');
    print('getUserData userMobileNo ==> $userMobileNo');

    Future.delayed(Duration.zero).then((value) {
      if (!mounted) return;
      setState(() {});
    });
  }

  @override
  void dispose() {
    paymentProvider.clearProvider();
    couponController.dispose();
    _razorpay?.clear();
    if (!kIsWeb) {
      if (Platform.isIOS) {
        final InAppPurchaseStoreKitPlatformAddition iosPlatformAddition =
            _inAppPurchase
                .getPlatformAddition<InAppPurchaseStoreKitPlatformAddition>();
        iosPlatformAddition.setDelegate(null);
      }
      _subscription.cancel();
    }
    super.dispose();
  }

  /* add_transaction API */
  Future addTransaction(
      dynamic packageId, description, amount, paymentId, currencyCode) async {
    Utils().showProgress(context);
    await paymentProvider.addTransaction(
        packageId, description, amount, paymentId);

    if (!paymentProvider.payLoading) {
      prDialog.hide();

      if (paymentProvider.successModel.status == 200) {
        prDialog.hide();
        isPaymentDone = true;
        await musicManager.clearMusicPlayer();
        if (!mounted) return;
        Navigator.of(context).pushAndRemoveUntil(
            MaterialPageRoute(builder: (context) => Homemusic()),
            (Route route) => false);
      } else {
        prDialog.hide();
        isPaymentDone = false;
        if (!mounted) return;
        Utils.showSnackbar(
            context, paymentProvider.successModel.message ?? "", false);
      }
    }
  }

  Future joinEventTransection(
      dynamic eventId, type, amount, transectionId, discription) async {
    Utils().showProgress(context);
    await paymentProvider.joinLiveEventTransaction(
        eventId, type, amount, transectionId, discription);

    if (!paymentProvider.payLoading) {
      await prDialog.hide();

      if (paymentProvider.successModel.status == 200) {
        isPaymentDone = true;
        if (!mounted) return;
        Navigator.pop(context, isPaymentDone);

        final liveEventProvider =
            Provider.of<LiveEventProvider>(context, listen: false);
        liveEventProvider.clearProvider();
        await liveEventProvider.getLiveEventList("1");
      } else {
        isPaymentDone = false;
        if (!mounted) return;
        Utils.showSnackbar(
            context, paymentProvider.successModel.message ?? "", false);
      }
    }
  }

  Future<void> openPayment({required String pgName}) async {
    if (pgName == "demo") {
      await addTransaction(widget.itemId, widget.itemTitle,
          paymentProvider.finalAmount, paymentId, widget.currency);
    } else {
      printLog("finalAmount =============> ${paymentProvider.finalAmount}");
      if (paymentProvider.finalAmount != "0") {
        if (pgName == "inapp") {
          _initInAppPurchase();
        } else if (pgName == "paypal") {
          _paypalInit();
        } else if (pgName == "razorpay") {
          printLog("Enter Razerpay");
          _initializeRazorpay();
        } else if (pgName == "flutterwave") {
          _flutterwaveinit();
        } else if (pgName == "stripe") {
          _stripeInit();
        } else if (pgName == "cashfree") {
          // Package purchases use the auto-renew subscription mandate once
          // admin has enabled it (requires Cashfree's separate Subscriptions
          // product approval); until then, fall back to a one-time order so
          // purchases keep working. Live-event tickets are always one-time.
          if (widget.payType == "Package" &&
              Constant.cashfreeSubscriptionEnabled == "1") {
            _cashfreeSubscriptionInit();
          } else {
            _cashfreeInit();
          }
        } else if (pgName == "cash") {
          if (!mounted) return;
          Utils.showSnackbar(context, "cash_payment_msg", true);
        }
      } else {
        if (widget.payType == "Package") {
          addTransaction(widget.itemId, widget.itemTitle,
              paymentProvider.finalAmount, paymentId, widget.currency);
        } else {
          joinEventTransection(widget.itemId, widget.contentType,
              paymentProvider.finalAmount, paymentId, widget.itemTitle);
        }
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return PopScope(
      canPop: false,
      onPopInvokedWithResult: (didPop, result) {
        onBackPressed(didPop);
      },
      child: _buildPage(),
    );
  }

  Widget _buildPage() {
    return Scaffold(
      backgroundColor: black,
      appBar: (kIsWeb)
          ? null
          : Utils.myAppBarWithBack(context, "payment_details", true),
      body: SafeArea(
        child: Center(
          child: _buildMobilePage(),
        ),
      ),
    );
  }

  Widget _buildMobilePage() {
    return Container(
      width: ((kIsWeb) && MediaQuery.of(context).size.width > 720)
          ? MediaQuery.of(context).size.width * 0.5
          : MediaQuery.of(context).size.width,
      margin: (kIsWeb)
          ? const EdgeInsets.fromLTRB(50, 0, 50, 50)
          : const EdgeInsets.all(0),
      alignment: Alignment.center,
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.center,
        children: [
          const SizedBox(height: (kIsWeb) ? 40 : 0),
          /* Total Amount */
          Container(
            width: MediaQuery.of(context).size.width,
            constraints: const BoxConstraints(minHeight: 50),
            decoration: Utils.setBackground(brandGreen, 0),
            padding: const EdgeInsets.fromLTRB(15, 0, 15, 0),
            alignment: Alignment.centerLeft,
            child: Consumer<PaymentProvider>(
              builder: (context, paymentProvider, child) {
                return RichText(
                  textAlign: TextAlign.start,
                  text: TextSpan(
                    text: payableAmountIs,
                    style: GoogleFonts.inter(
                      textStyle: const TextStyle(
                        color: appBgColor,
                        fontSize: 16,
                        fontWeight: FontWeight.w600,
                        fontStyle: FontStyle.normal,
                        letterSpacing: 0.5,
                      ),
                    ),
                    children: <TextSpan>[
                      TextSpan(
                        text:
                            "${Constant.currencySymbol}${paymentProvider.finalAmount ?? ""}",
                        style: GoogleFonts.inter(
                          textStyle: const TextStyle(
                            color: white,
                            fontSize: 20,
                            fontWeight: FontWeight.w700,
                            fontStyle: FontStyle.normal,
                            letterSpacing: 0.2,
                          ),
                        ),
                      ),
                    ],
                  ),
                );
              },
            ),
          ),

          /* PGs */
          Expanded(
            child: SingleChildScrollView(
                child: paymentProvider.loading
                    ? Container(
                        height: 230,
                        padding: const EdgeInsets.all(20),
                        child: Utils.pageLoader(),
                      )
                    : paymentProvider.paymentOptionModel.status == 200
                        ? paymentProvider.paymentOptionModel.result != null
                            ? ((kIsWeb)
                                ? _buildWebPayments()
                                : _buildPayments())
                            : _buildPaymentOptionError()
                        : _buildPaymentOptionError()),
          ),
        ],
      ),
    );
  }

  /// Shows the *actual* reason payment options failed to load — network
  /// error, timeout, bad response — instead of a silent blank screen. Also
  /// offers a retry so a transient failure doesn't require reopening the page.
  Widget _buildPaymentOptionError() {
    final message = paymentProvider.paymentOptionError ??
        "message=${paymentProvider.paymentOptionModel.message}";
    return Container(
      padding: const EdgeInsets.all(24),
      alignment: Alignment.center,
      child: Column(
        mainAxisSize: MainAxisSize.min,
        children: [
          MyText(
            color: white,
            text: "Couldn't load payment methods",
            multilanguage: false,
            fontsize: Dimens.textMedium,
            maxline: 2,
            overflow: TextOverflow.ellipsis,
            fontwaight: FontWeight.w700,
            textalign: TextAlign.center,
            fontstyle: FontStyle.normal,
          ),
          const SizedBox(height: 8),
          MyText(
            color: colorAccent,
            text: message,
            multilanguage: false,
            fontsize: Dimens.textSmall,
            maxline: 5,
            overflow: TextOverflow.ellipsis,
            textalign: TextAlign.center,
            fontwaight: FontWeight.w400,
            fontstyle: FontStyle.normal,
          ),
          const SizedBox(height: 16),
          ElevatedButton(
            onPressed: () => paymentProvider.getPaymentOption(),
            child: const Text("Retry"),
          ),
        ],
      ),
    );
  }

  Widget _buildPayments() {
    return Container(
      padding: const EdgeInsets.fromLTRB(15, 20, 15, 20),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.center,
        children: [
          MyText(
            color: white,
            text: "payment_methods",
            fontsize: Dimens.textMedium,
            maxline: 1,
            multilanguage: true,
            overflow: TextOverflow.ellipsis,
            fontwaight: FontWeight.w600,
            textalign: TextAlign.center,
            fontstyle: FontStyle.normal,
          ),
          const SizedBox(height: 5),
          MyText(
            color: white,
            text: "choose_a_payment_methods_to_pay",
            multilanguage: true,
            fontsize: Dimens.textMedium,
            maxline: 2,
            overflow: TextOverflow.ellipsis,
            fontwaight: FontWeight.w500,
            textalign: TextAlign.center,
            fontstyle: FontStyle.normal,
          ),
          const SizedBox(height: 15),
          MyText(
            color: colorAccent,
            text: "pay_with",
            multilanguage: true,
            fontsize: Dimens.textTitle,
            maxline: 1,
            overflow: TextOverflow.ellipsis,
            fontwaight: FontWeight.w700,
            textalign: TextAlign.center,
            fontstyle: FontStyle.normal,
          ),
          const SizedBox(height: 20),

          /* /* Payments */ */
          (!kIsWeb)
              ? (Platform.isIOS ? _buildIOSPG() : _buildAndroidPG())
              : const SizedBox.shrink(),
        ],
      ),
    );
  }

  Widget _buildWebPayments() {
    return Container(
      padding: const EdgeInsets.fromLTRB(15, 20, 15, 20),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.center,
        children: [
          MyText(
            color: black,
            text: "payment_methods",
            fontsize: Dimens.textMedium,
            maxline: 1,
            multilanguage: true,
            overflow: TextOverflow.ellipsis,
            fontwaight: FontWeight.w600,
            textalign: TextAlign.center,
            fontstyle: FontStyle.normal,
          ),
          const SizedBox(height: 5),
          MyText(
            color: gray,
            text: "choose_a_payment_methods_to_pay",
            multilanguage: true,
            fontsize: Dimens.textMedium,
            maxline: 2,
            overflow: TextOverflow.ellipsis,
            fontwaight: FontWeight.w500,
            textalign: TextAlign.center,
            fontstyle: FontStyle.normal,
          ),
          const SizedBox(height: 15),
          MyText(
            color: colorAccent,
            text: "pay_with",
            multilanguage: true,
            fontsize: Dimens.textTitle,
            maxline: 1,
            overflow: TextOverflow.ellipsis,
            fontwaight: FontWeight.w700,
            textalign: TextAlign.center,
            fontstyle: FontStyle.normal,
          ),
          const SizedBox(height: 20),

          /* Razorpay */
          paymentProvider.paymentOptionModel.result?.razorpay != null
              ? paymentProvider
                          .paymentOptionModel.result?.razorpay?.visibility ==
                      "1"
                  ? _buildPGButton(
                      "pg_razorpay.png",
                      "Razorpay",
                      35,
                      130,
                      onClick: () async {
                        paymentProvider.setCurrentPayment("razorpay");
                        openPayment(pgName: "razorpay");
                      },
                    )
                  : const SizedBox.shrink()
              : const NoData(text: "", subTitle: ""),

          /* Cashfree */
          paymentProvider.paymentOptionModel.result?.cashfree != null
              ? paymentProvider
                          .paymentOptionModel.result?.cashfree?.visibility ==
                      "1"
                  ? _buildCashfreeButton(onClick: () async {
                      paymentProvider.setCurrentPayment("cashfree");
                      openPayment(pgName: "cashfree");
                    })
                  : const SizedBox.shrink()
              : const SizedBox.shrink(),
        ],
      ),
    );
  }

  Widget _buildAndroidPG() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.center,
      children: [
        /* /* Payments */ */
        if (Constant.isDemo)
          _buildPGButton("pg_inapp.png", "demo", 35, 110, onClick: () async {
            paymentProvider.setCurrentPayment("demo");
            openPayment(pgName: "demo");
          }),

        /* In-App purchase */
        paymentProvider.paymentOptionModel.result?.inAppPurchageAndroid != null
            ? paymentProvider.paymentOptionModel.result?.inAppPurchageAndroid
                        ?.visibility ==
                    "1"
                ? _buildPGButton(
                    "pg_inapp.png",
                    "InApp Purchase",
                    35,
                    110,
                    onClick: () async {
                      paymentProvider.setCurrentPayment("inapp");
                      openPayment(pgName: "inapp");
                    },
                  )
                : const SizedBox.shrink()
            : const SizedBox.shrink(),

        /* Paypal */
        paymentProvider.paymentOptionModel.result?.paypal != null
            ? paymentProvider.paymentOptionModel.result?.paypal?.visibility ==
                    "1"
                ? _buildPGButton(
                    "pg_paypal.png",
                    "Paypal",
                    35,
                    130,
                    onClick: () async {
                      paymentProvider.setCurrentPayment("paypal");
                      openPayment(pgName: "paypal");
                    },
                  )
                : const SizedBox.shrink()
            : const SizedBox.shrink(),

        /* Razorpay */
        paymentProvider.paymentOptionModel.result?.razorpay != null
            ? paymentProvider.paymentOptionModel.result?.razorpay?.visibility ==
                    "1"
                ? _buildPGButton(
                    "pg_razorpay.png",
                    "Razorpay",
                    35,
                    130,
                    onClick: () async {
                      paymentProvider.setCurrentPayment("razorpay");
                      openPayment(pgName: "razorpay");
                    },
                  )
                : const SizedBox.shrink()
            : const SizedBox.shrink(),

        /* Paytm */
        paymentProvider.paymentOptionModel.result?.payTm != null
            ? paymentProvider.paymentOptionModel.result?.payTm?.visibility ==
                    "1"
                ? _buildPGButton(
                    "pg_paytm.png",
                    "Paytm",
                    30,
                    90,
                    onClick: () async {
                      paymentProvider.setCurrentPayment("paytm");
                      openPayment(pgName: "paytm");
                    },
                  )
                : const SizedBox.shrink()
            : const SizedBox.shrink(),

        /* Flutterwave */
        paymentProvider.paymentOptionModel.result?.flutterWave != null
            ? paymentProvider
                        .paymentOptionModel.result?.flutterWave?.visibility ==
                    "1"
                ? _buildPGButton(
                    "pg_flutterwave.png",
                    "Flutterwave",
                    35,
                    130,
                    onClick: () async {
                      paymentProvider.setCurrentPayment("flutterwave");
                      openPayment(pgName: "flutterwave");
                    },
                  )
                : const SizedBox.shrink()
            : const SizedBox.shrink(),

        /* Stripe */
        paymentProvider.paymentOptionModel.result?.stripe != null
            ? paymentProvider.paymentOptionModel.result?.stripe?.visibility ==
                    "1"
                ? _buildPGButton(
                    "pg_stripe.png",
                    "Stripe",
                    35,
                    100,
                    onClick: () async {
                      paymentProvider.setCurrentPayment("stripe");
                      openPayment(pgName: "stripe");
                    },
                  )
                : const SizedBox.shrink()
            : const SizedBox.shrink(),

        /* Cashfree */
        paymentProvider.paymentOptionModel.result?.cashfree != null
            ? paymentProvider.paymentOptionModel.result?.cashfree?.visibility ==
                    "1"
                ? _buildCashfreeButton(onClick: () async {
                    paymentProvider.setCurrentPayment("cashfree");
                    openPayment(pgName: "cashfree");
                  })
                : const SizedBox.shrink()
            : const SizedBox.shrink(),
      ],
    );
  }

  Widget _buildIOSPG() {
    final result = paymentProvider.paymentOptionModel.result;

    return Column(
      children: [
        if (Constant.isDemo)
          _buildPGButton("pg_inapp.png", "demo", 35, 110, onClick: () async {
            paymentProvider.setCurrentPayment("demo");
            openPayment(pgName: "demo");
          }),

        /// IOS In-App Purchase
        if (result?.inAppPurchageIos != null &&
            result?.inAppPurchageIos?.visibility == "1")
          _buildPGButton(
            "pg_inapp.png",
            "InApp Purchase",
            35,
            110,
            onClick: () async {
              paymentProvider.setCurrentPayment("inapp");
              openPayment(pgName: "inapp");
            },
          ),

        /// Paypal
        if (result?.paypal != null && result?.paypal?.visibility == "1")
          _buildPGButton(
            "pg_paypal.png",
            "Paypal",
            35,
            130,
            onClick: () async {
              paymentProvider.setCurrentPayment("paypal");
              openPayment(pgName: "paypal");
            },
          ),

        /// Razorpay
        if (result?.razorpay != null && result?.razorpay?.visibility == "1")
          _buildPGButton(
            "pg_razorpay.png",
            "Razorpay",
            35,
            130,
            onClick: () async {
              paymentProvider.setCurrentPayment("razorpay");
              openPayment(pgName: "razorpay");
            },
          ),

        /// Paytm
        if (result?.payTm != null && result?.payTm?.visibility == "1")
          _buildPGButton(
            "pg_paytm.png",
            "Paytm",
            30,
            90,
            onClick: () async {
              paymentProvider.setCurrentPayment("paytm");
              openPayment(pgName: "paytm");
            },
          ),

        /// Flutterwave
        if (result?.flutterWave != null &&
            result?.flutterWave?.visibility == "1")
          _buildPGButton(
            "pg_flutterwave.png",
            "Flutterwave",
            35,
            130,
            onClick: () async {
              paymentProvider.setCurrentPayment("flutterwave");
              openPayment(pgName: "flutterwave");
            },
          ),

        /// Stripe
        if (result?.stripe != null && result?.stripe?.visibility == "1")
          _buildPGButton(
            "pg_stripe.png",
            "Stripe",
            35,
            100,
            onClick: () async {
              paymentProvider.setCurrentPayment("stripe");
              openPayment(pgName: "stripe");
            },
          ),

        /// Restore Purchases (required by App Store guidelines)
        const SizedBox(height: 10),
        TextButton(
          onPressed: _restorePurchases,
          child: const Text(
            "Restore Purchases",
            style: TextStyle(color: Colors.white70, fontSize: 14),
          ),
        ),
      ],
    );
  }

  Future<void> _restorePurchases() async {
    Utils().showProgress(context);
    await _inAppPurchase.restorePurchases();
    if (!mounted) return;
    prDialog.hide();
    Utils.showSnackbar(context, "restore_purchases_initiated", true);
  }

  // Widget _buildIOSPGButton(String pgName, double imgHeight, double imgWidth,
  //     {required Function() onClick}) {
  //   return Container(
  //     margin: const EdgeInsets.only(bottom: 5),
  //     child: Card(
  //       semanticContainer: true,
  //       clipBehavior: Clip.antiAliasWithSaveLayer,
  //       elevation: 5,
  //       color: white,
  //       shadowColor: black.withValues(alpha: 0.2),
  //       shape: RoundedRectangleBorder(
  //         borderRadius: BorderRadius.circular(8),
  //       ),
  //       child: InkWell(
  //         borderRadius: BorderRadius.circular(8),
  //         onTap: onClick,
  //         child: Container(
  //           constraints: const BoxConstraints(minHeight: 85),
  //           padding: const EdgeInsets.all(20),
  //           child: Row(
  //             mainAxisSize: MainAxisSize.max,
  //             crossAxisAlignment: CrossAxisAlignment.center,
  //             children: <Widget>[
  //               Expanded(
  //                 child: MyText(
  //                   color: brandGreen,
  //                   text: pgName,
  //                   multilanguage: false,
  //                   fontsize: Dimens.textlargeExtraBig,
  //                   maxline: 2,
  //                   overflow: TextOverflow.ellipsis,
  //                   fontwaight: FontWeight.w600,
  //                   textalign: TextAlign.start,
  //                   fontstyle: FontStyle.normal,
  //                 ),
  //               ),
  //               const SizedBox(width: 20),
  //               MyImage(
  //                 imagePath: "ic_arrow_right.png",
  //                 fit: BoxFit.contain,
  //                 height: 22,
  //                 width: 20,
  //                 color: white,
  //               ),
  //             ],
  //           ),
  //         ),
  //       ),
  //     ),
  //   );
  // }

  Widget _buildPGButton(
      String imageName, String pgName, double imgHeight, double imgWidth,
      {required Function() onClick}) {
    return Container(
      margin: const EdgeInsets.only(bottom: 5),
      child: Card(
        semanticContainer: true,
        clipBehavior: Clip.antiAliasWithSaveLayer,
        elevation: 5,
        color: white,
        shadowColor: black.withValues(alpha: 0.2),
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(8),
        ),
        child: InkWell(
          borderRadius: BorderRadius.circular(8),
          onTap: onClick,
          child: Container(
            constraints: const BoxConstraints(minHeight: 85),
            padding: const EdgeInsets.all(12),
            child: Row(
              mainAxisSize: MainAxisSize.max,
              crossAxisAlignment: CrossAxisAlignment.center,
              children: <Widget>[
                MyImage(
                  imagePath: imageName,
                  fit: BoxFit.fill,
                  height: imgHeight,
                  width: imgWidth,
                ),
                const SizedBox(width: 15),
                Expanded(
                  child: MyText(
                    color: brandGreen,
                    text: pgName,
                    multilanguage: false,
                    fontsize: Dimens.textMedium,
                    maxline: 2,
                    overflow: TextOverflow.ellipsis,
                    fontwaight: FontWeight.w600,
                    textalign: TextAlign.end,
                    fontstyle: FontStyle.normal,
                  ),
                ),
                const SizedBox(width: 15),
                MyImage(
                  imagePath: "ic_arrow_right.png",
                  fit: BoxFit.fill,
                  height: 22,
                  width: 20,
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }

  /* Cashfree uses an icon in place of a brand logo asset — swap in
     assets/images/pg_cashfree.png via MyImage if an official logo is added later. */
  Widget _buildCashfreeButton({required Function() onClick}) {
    // Cashfree's SDK ships no brand logo asset (only card-network icons for
    // their own card-entry widget), and pulling one from an unverified
    // third-party mirror isn't safe to bundle. This uses Cashfree's own
    // brand purple (#6A3FD3 — their SDK's own default theme color) in a
    // proper icon badge instead of a plain generic wallet glyph in the
    // app's unrelated green, which read as unbranded/placeholder.
    const cashfreePurple = Color(0xFF6A3FD3);
    return Container(
      margin: const EdgeInsets.only(bottom: 5),
      child: Card(
        semanticContainer: true,
        clipBehavior: Clip.antiAliasWithSaveLayer,
        elevation: 5,
        color: white,
        shadowColor: black.withValues(alpha: 0.2),
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(8),
        ),
        child: InkWell(
          borderRadius: BorderRadius.circular(8),
          onTap: onClick,
          child: Container(
            constraints: const BoxConstraints(minHeight: 85),
            padding: const EdgeInsets.all(12),
            child: Row(
              mainAxisSize: MainAxisSize.max,
              crossAxisAlignment: CrossAxisAlignment.center,
              children: <Widget>[
                Container(
                  width: 44,
                  height: 44,
                  decoration: BoxDecoration(
                    color: cashfreePurple,
                    borderRadius: BorderRadius.circular(12),
                  ),
                  alignment: Alignment.center,
                  child: const Icon(Icons.bolt_rounded, color: white, size: 26),
                ),
                const SizedBox(width: 15),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    mainAxisSize: MainAxisSize.min,
                    children: [
                      MyText(
                        color: black,
                        text: "Cashfree",
                        multilanguage: false,
                        fontsize: Dimens.textMedium,
                        maxline: 1,
                        overflow: TextOverflow.ellipsis,
                        fontwaight: FontWeight.w700,
                        textalign: TextAlign.start,
                        fontstyle: FontStyle.normal,
                      ),
                      const SizedBox(height: 2),
                      MyText(
                        color: colorAccent,
                        text: "Cards, UPI, Netbanking & more",
                        multilanguage: false,
                        fontsize: Dimens.textSmall,
                        maxline: 1,
                        overflow: TextOverflow.ellipsis,
                        fontwaight: FontWeight.w400,
                        textalign: TextAlign.start,
                        fontstyle: FontStyle.normal,
                      ),
                    ],
                  ),
                ),
                const SizedBox(width: 15),
                MyImage(
                  imagePath: "ic_arrow_right.png",
                  fit: BoxFit.fill,
                  height: 22,
                  width: 20,
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }

  /* ********* InApp purchase START ********* */
  Future<void> initStoreInfo() async {
    final bool isAvailable = await _inAppPurchase.isAvailable();
    if (!isAvailable) {
      setState(() {});
      return;
    }

    if (Platform.isIOS) {
      final InAppPurchaseStoreKitPlatformAddition iosPlatformAddition =
          _inAppPurchase
              .getPlatformAddition<InAppPurchaseStoreKitPlatformAddition>();
      await iosPlatformAddition.setDelegate(JailaoiPaymentQueueDelegate());
    }

    final ProductDetailsResponse productDetailResponse =
        await _inAppPurchase.queryProductDetails(_kProductIds.toSet());
    if (productDetailResponse.error != null) {
      setState(() {});
      return;
    }

    if (productDetailResponse.productDetails.isEmpty) {
      setState(() {});
      return;
    }
    setState(() {});
  }

  Future<void> _initInAppPurchase() async {
    Utils.showSnackbar(context,
        'Store billing is not configured yet. Please contact support.', false);
  }

  Future<void> _listenToPurchaseUpdated(
      List<PurchaseDetails> purchaseDetailsList) async {
    for (final PurchaseDetails purchaseDetails in purchaseDetailsList) {
      if (purchaseDetails.status == PurchaseStatus.pending) {
        showPendingUI();
      } else {
        if (purchaseDetails.status == PurchaseStatus.error) {
          printLog(
              "purchaseDetails ============> ${purchaseDetails.error.toString()}");
          handleError(purchaseDetails.error!);
        } else if (purchaseDetails.status == PurchaseStatus.purchased ||
            purchaseDetails.status == PurchaseStatus.restored) {
          printLog("===> status ${purchaseDetails.status}");
          final bool valid = await _verifyPurchase(purchaseDetails);
          if (valid) {
            deliverProduct(purchaseDetails);
          } else {
            _handleInvalidPurchase(purchaseDetails);
            return;
          }
        }
        if (Platform.isAndroid) {
          if (!_kAutoConsume && purchaseDetails.productID == _kProductIds[0]) {
            final InAppPurchaseAndroidPlatformAddition androidAddition =
                _inAppPurchase.getPlatformAddition<
                    InAppPurchaseAndroidPlatformAddition>();
            await androidAddition.consumePurchase(purchaseDetails);
          }
        }
        if (purchaseDetails.pendingCompletePurchase) {
          printLog(
              "===> pendingCompletePurchase ${purchaseDetails.pendingCompletePurchase}");
          await _inAppPurchase.completePurchase(purchaseDetails);
        }
      }
    }
  }

  Future<void> deliverProduct(PurchaseDetails purchaseDetails) async {
    printLog("===> productID ${purchaseDetails.productID}");
    if (purchaseDetails.productID == _kProductIds[0]) {
      if (widget.payType == "Package") {
        addTransaction(widget.itemId, widget.itemTitle,
            paymentProvider.finalAmount, paymentId, widget.currency);
      } else {
        joinEventTransection(widget.itemId, widget.contentType,
            paymentProvider.finalAmount, paymentId, widget.itemTitle);
      }
      setState(() {});
    } else {
      printLog("===> consumables else $purchaseDetails");
      setState(() {
        _purchases.add(purchaseDetails);
      });
    }
  }

  void showPendingUI() {
    setState(() {});
  }

  void handleError(IAPError error) {
    Utils.showToast(error.message);
    setState(() {});
  }

  Future<bool> _verifyPurchase(PurchaseDetails purchaseDetails) {
    // Store receipt verification is not configured on the backend yet.
    // Never acknowledge or claim delivery based only on a client callback.
    return Future<bool>.value(false);
  }

  void _handleInvalidPurchase(PurchaseDetails purchaseDetails) {
    Utils.showSnackbar(
        context,
        'Store purchase verification is unavailable. Contact support before retrying.',
        false);
  }
  /* ********* InApp purchase END ********* */

  /* ********* Razorpay START ********* */
  void _initializeRazorpay() {
    final rzp = paymentProvider.paymentOptionModel.result?.razorpay;
    if (rzp == null) {
      Utils.showSnackbar(context, "payment_not_processed", true);
      return;
    }

    bool isContinue = checkKeysAndContinue(
      isLive: rzp.isLive ?? "",
      isBothKeyReq: false,
      liveKey1: rzp.key1 ?? "",
      liveKey2: "",
      testKey1: rzp.key1 ?? "",
      testKey2: "",
    );
    if (!isContinue) return;

    _razorpay = Razorpay();
    _razorpay!.on(Razorpay.EVENT_PAYMENT_ERROR, handlePaymentErrorResponse);
    _razorpay!.on(Razorpay.EVENT_PAYMENT_SUCCESS, handlePaymentSuccessResponse);
    _razorpay!.on(Razorpay.EVENT_EXTERNAL_WALLET, handleExternalWalletSelected);

    var options = {
      'key': rzp.key1 ?? "",
      'currency': Constant.currency,
      'amount':
          (double.parse(paymentProvider.finalAmount ?? "0") * 100).toInt(),
      'name': Constant.appName,
      'description': widget.itemTitle ?? "",
      'retry': {'enabled': true, 'max_count': 1},
      'send_sms_hash': true,
      'prefill': {'contact': userMobileNo ?? "", 'email': userEmail ?? ""},
    };

    printLog("Razorpay options: $options");
    try {
      _razorpay!.open(options);
    } catch (e) {
      printLog('Razorpay open error: $e');
      Utils.showSnackbar(context, "payment_not_processed", true);
    }
  }

  void handlePaymentErrorResponse(PaymentFailureResponse response) {
    printLog("Razorpay error: ${response.code} ${response.message}");
    Utils.showSnackbar(context, "payment_fail", true);
    paymentProvider.setCurrentPayment("");
  }

  void handlePaymentSuccessResponse(PaymentSuccessResponse response) {
    paymentId = response.paymentId.toString();
    printLog("Razorpay success paymentId: $paymentId");
    if (widget.payType == "Package") {
      addTransaction(widget.itemId, widget.itemTitle,
          paymentProvider.finalAmount, paymentId, widget.currency);
    } else {
      joinEventTransection(widget.itemId, widget.contentType,
          paymentProvider.finalAmount, paymentId, widget.itemTitle);
    }
  }

  void handleExternalWalletSelected(ExternalWalletResponse response) {
    printLog("Razorpay external wallet: ${response.walletName}");
  }

  /* ********* Paypal START ********* */
  Future<void> _paypalInit() async {
    if (paymentProvider.paymentOptionModel.result?.paypal != null) {
      /* Check Keys */
      bool isContinue = checkKeysAndContinue(
        isLive:
            (paymentProvider.paymentOptionModel.result?.paypal?.isLive ?? ""),
        isBothKeyReq: true,
        liveKey1:
            (paymentProvider.paymentOptionModel.result?.paypal?.key1 ?? ""),
        liveKey2:
            (paymentProvider.paymentOptionModel.result?.paypal?.key2 ?? ""),
        testKey1: "",
        testKey2: '',
      );
      if (!isContinue) return;
      /* Check Keys */
      Navigator.of(context).push(
        MaterialPageRoute(
          builder: (BuildContext context) => UsePaypal(
              sandboxMode:
                  (paymentProvider.paymentOptionModel.result?.paypal?.isLive ??
                              "") ==
                          "1"
                      ? false
                      : true,
              clientId: paymentProvider
                          .paymentOptionModel.result?.paypal?.isLive ==
                      "1"
                  ? paymentProvider.paymentOptionModel.result?.paypal?.key1 ??
                      ""
                  : paymentProvider.paymentOptionModel.result?.paypal?.key1 ??
                      "",
              secretKey: paymentProvider
                          .paymentOptionModel.result?.paypal?.isLive ==
                      "1"
                  ? paymentProvider.paymentOptionModel.result?.paypal?.key2 ??
                      ""
                  : paymentProvider.paymentOptionModel.result?.paypal?.key2 ??
                      "",
              returnURL: "https://portal.jailaoi.com/payment/success",
              cancelURL: "https://portal.jailaoi.com/payment/cancel",
              transactions: [
                {
                  "amount": {
                    "total": '${paymentProvider.finalAmount}',
                    "currency": Constant.currency,
                    "details": {
                      "subtotal": '${paymentProvider.finalAmount}',
                      "shipping": '0',
                      "shipping_discount": 0
                    }
                  },
                  "description": "The payment transaction description.",
                  "item_list": {
                    "items": [
                      {
                        "name": "${widget.itemTitle}",
                        "quantity": 1,
                        "price": '${paymentProvider.finalAmount}',
                        "currency": Constant.currency
                      }
                    ],
                  }
                }
              ],
              note: "Contact us for any questions on your order.",
              onSuccess: (params) async {
                printLog("onSuccess: ${params["paymentId"]}");
                if (widget.payType == "Package") {
                  addTransaction(
                      widget.itemId,
                      widget.itemTitle,
                      paymentProvider.finalAmount,
                      params["paymentId"],
                      widget.currency);
                } else {
                  joinEventTransection(widget.itemId, widget.contentType,
                      paymentProvider.finalAmount, paymentId, widget.itemTitle);
                }
              },
              onError: (params) {
                printLog("onError: ${params["message"]}");
                Utils.showSnackbar(
                    context, params["message"].toString(), false);
              },
              onCancel: (params) {
                printLog('cancelled: $params');
                Utils.showSnackbar(context, params.toString(), false);
              }),
        ),
      );
    } else {
      Utils.showSnackbar(context, "payment_not_processed", true);
    }
  }
  /* ********* Paypal END ********* */

  /* ********* Stripe START ********* */
  Future<void> _stripeInit() async {
    if (paymentProvider.paymentOptionModel.result?.stripe != null) {
      /* Check Keys */
      bool isContinue = checkKeysAndContinue(
        isLive:
            (paymentProvider.paymentOptionModel.result?.stripe?.isLive ?? ""),
        isBothKeyReq: true,
        liveKey1:
            (paymentProvider.paymentOptionModel.result?.stripe?.key1 ?? ""),
        liveKey2:
            (paymentProvider.paymentOptionModel.result?.stripe?.key2 ?? ""),
        testKey1:
            (paymentProvider.paymentOptionModel.result?.stripe?.key1 ?? ""),
        testKey2:
            (paymentProvider.paymentOptionModel.result?.stripe?.key2 ?? ""),
      );
      if (!isContinue) return;
      /* Check Keys */
      stripe.Stripe.publishableKey =
          paymentProvider.paymentOptionModel.result?.stripe?.isLive == "1"
              ? paymentProvider.paymentOptionModel.result?.stripe?.key1 ?? ""
              : paymentProvider.paymentOptionModel.result?.stripe?.key1 ?? "";
      try {
        //STEP 1: Create Payment Intent
        paymentIntent = await createPaymentIntent(
            paymentProvider.finalAmount ?? "", Constant.currency);

        //STEP 2: Initialize Payment Sheet

        await stripe.Stripe.instance
            .initPaymentSheet(
                paymentSheetParameters: stripe.SetupPaymentSheetParameters(
              merchantDisplayName: Constant.appName,
              paymentIntentClientSecret: paymentIntent?['client_secret'],
              style: ThemeMode.light,
            ))
            .then((value) {});
        //STEP 3: Display Payment sheet
        displayPaymentSheet();
      } catch (err) {
        throw Exception(err);
      }
    } else {
      Utils.showSnackbar(context, "payment_not_processed", true);
    }
  }

  Future createPaymentIntent(String amount, String currency) async {
    try {
      //Request body
      Map<String, dynamic> body = {
        'amount': calculateAmount(amount),
        'currency': currency,
        'description': widget.itemTitle,
      };

      //Make post request to Stripe
      var response = await http.post(
        Uri.parse('https://api.stripe.com/v1/payment_intents'),
        headers: {
          'Authorization':
              'Bearer ${paymentProvider.paymentOptionModel.result?.stripe?.isLive == "1" ? paymentProvider.paymentOptionModel.result?.stripe?.key2 ?? "" : paymentProvider.paymentOptionModel.result?.stripe?.key2 ?? ""}',
          'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: body,
      );
      print("--------------------------------${json.decode(response.body)}");
      return json.decode(response.body);
    } catch (err) {
      throw Exception(err.toString());
    }
  }

  String calculateAmount(String amount) {
    final calculatedAmout = (int.parse(amount)) * 100;
    return calculatedAmout.toString();
  }

  Future<void> displayPaymentSheet() async {
    try {
      await stripe.Stripe.instance.presentPaymentSheet().then((value) {
        if (!mounted) return;
        Utils.showSnackbar(context, "payment_success", true);
        if (widget.payType == "Package") {
          addTransaction(widget.itemId, widget.itemTitle,
              paymentProvider.finalAmount, paymentId, widget.currency);
        } else {
          joinEventTransection(widget.itemId, widget.contentType,
              paymentProvider.finalAmount, paymentId, widget.itemTitle);
        }

        paymentIntent = null;
      }).onError((error, stackTrace) {
        throw Exception(error);
      });
    } on stripe.StripeException catch (e) {
      printLog('Error is:---> $e');
      const AlertDialog(
        content: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            Row(
              children: [
                Icon(
                  Icons.cancel,
                  color: Colors.red,
                ),
                Text("Payment Failed"),
              ],
            ),
          ],
        ),
      );
    } catch (e) {
      printLog('$e');
    }
  }
  /* ********* Stripe END ********* */

  /* ********* Cashfree START ********* */
  /// On Android/iOS, Cashfree's SDK drives an in-app checkout and reports back
  /// via [_cashfreeVerifyPayment] / [_cashfreeOnError] without leaving the app.
  /// On Flutter Web, Cashfree does a full-page browser redirect (no embeddable
  /// in-app WebView), so the app state here is lost — [Splash] resumes the
  /// pending purchase on reload by reading `order_id` from the return URL and
  /// the pending purchase details saved to SharedPref below.
  Future<void> _cashfreeInit() async {
    final cf = paymentProvider.paymentOptionModel.result?.cashfree;
    if (cf == null) {
      Utils.showSnackbar(context, "payment_not_processed", true);
      return;
    }

    _cashfreePendingIsSubscription = false;
    Utils().showProgress(context);

    // paymentId alone is only ~7 digits of randomness generated once per
    // screen instance — reusing it verbatim as the Cashfree order_id causes
    // "order with same id is already present" on retries/re-taps since
    // Cashfree order IDs must be unique forever, not just per-session.
    final orderId = "cf_${paymentId}_${DateTime.now().millisecondsSinceEpoch}";
    String? returnUrl;
    if (kIsWeb) {
      returnUrl = Uri.base.origin + Uri.base.path;
      await sharedPref.save("cf_pending_order_id", orderId);
      await sharedPref.save("cf_pending_package_id", "${widget.itemId}");
      await sharedPref.save(
          "cf_pending_amount", paymentProvider.finalAmount ?? "");
      await sharedPref.save("cf_pending_desc", widget.itemTitle ?? "");
      await sharedPref.save("cf_pending_pay_type", widget.payType ?? "Package");
      await sharedPref.save("cf_pending_content_type", "${widget.contentType}");
    }

    final orderModel = await paymentProvider.createCashfreeOrder(
      widget.itemId,
      paymentProvider.finalAmount,
      orderId,
      userEmail,
      userMobileNo,
      returnUrl: returnUrl,
    );

    if (!mounted) return;
    prDialog.hide();

    final sessionId = orderModel.result?.paymentSessionId;
    if (orderModel.status != 200 || sessionId == null || sessionId.isEmpty) {
      // orderModel.message is raw server text (e.g. "Cashfree error: ..."),
      // not a locale key — multilanguage:true was mangling it into
      // something like "$cashfree_error:_...". Only the generic fallback
      // string is an actual locale key.
      final msg = orderModel.message;
      if (msg != null && msg.isNotEmpty) {
        Utils.showSnackbar(context, msg, false);
      } else {
        Utils.showSnackbar(context, "payment_not_processed", true);
      }
      return;
    }

    try {
      final isLive = orderModel.result?.isLive ?? false;
      final session = CFSessionBuilder()
          .setEnvironment(
              isLive ? CFEnvironment.PRODUCTION : CFEnvironment.SANDBOX)
          .setOrderId(orderModel.result?.orderId ?? orderId)
          .setPaymentSessionId(sessionId)
          .build();

      final cfWebCheckout =
          CFWebCheckoutPaymentBuilder().setSession(session).build();

      _cfPaymentGatewayService.doPayment(cfWebCheckout);
    } catch (e) {
      printLog("Cashfree doPayment error ============> $e");
      if (!mounted) return;
      Utils.showSnackbar(context, "payment_not_processed", true);
    }
  }

  void _cashfreeVerifyPayment(String id) async {
    if (_cashfreePendingIsSubscription) {
      _cashfreeSubscriptionVerified(id);
      return;
    }

    Utils().showProgress(context);
    final verifyModel = await paymentProvider.verifyCashfreeOrder(id);
    if (!mounted) return;
    prDialog.hide();

    if (verifyModel.result?.paid == true) {
      if (widget.payType == "Package") {
        addTransaction(widget.itemId, widget.itemTitle,
            paymentProvider.finalAmount, id, widget.currency);
      } else {
        joinEventTransection(widget.itemId, widget.contentType,
            paymentProvider.finalAmount, id, widget.itemTitle);
      }
    } else {
      // verifyModel.message is raw server text (e.g. "Payment status:
      // PENDING"), not a locale key.
      final msg = verifyModel.message;
      if (msg != null && msg.isNotEmpty) {
        Utils.showSnackbar(context, msg, false);
      } else {
        Utils.showSnackbar(context, "payment_fail", true);
      }
    }
  }

  void _cashfreeOnError(CFErrorResponse errorResponse, String id) {
    printLog("Cashfree onError ============> ${errorResponse.getMessage()}");
    if (!mounted) return;
    prDialog.hide();
    Utils.showSnackbar(
        context, errorResponse.getMessage() ?? "payment_fail", true);
  }
  /* ********* Cashfree END ********* */

  /* ********* Cashfree Subscription (auto-renew) START ********* */
  /// Every Cashfree Package purchase is a recurring subscription now — this
  /// screen's checkout is the mandate authorization (RBI's required
  /// full-authentication consent step). The mandate rail then auto-charges
  /// on each renewal without the app being open; the backend webhook is the
  /// ONLY thing that ever credits a charge (initial or renewal), so this
  /// success callback intentionally does not call addTransaction itself —
  /// doing so would double-credit the very first charge against the webhook.
  Future<void> _cashfreeSubscriptionInit() async {
    final cf = paymentProvider.paymentOptionModel.result?.cashfree;
    if (cf == null) {
      Utils.showSnackbar(context, "payment_not_processed", true);
      return;
    }

    if ((userEmail ?? "").isEmpty || (userMobileNo ?? "").isEmpty) {
      Utils.showSnackbar(
          context, "please_complete_profile_for_subscription", true);
      return;
    }

    _cashfreePendingIsSubscription = true;
    Utils().showProgress(context);

    final subscriptionId =
        "cfsub_${paymentId}_${DateTime.now().millisecondsSinceEpoch}";
    if (kIsWeb) {
      final returnUrl = Uri.base.origin + Uri.base.path;
      await sharedPref.save("cf_pending_subscription_id", subscriptionId);
      await createSubscriptionAndPay(subscriptionId, returnUrl);
      return;
    }

    await createSubscriptionAndPay(subscriptionId, null);
  }

  Future<void> createSubscriptionAndPay(
      String subscriptionId, String? returnUrl) async {
    final subModel = await paymentProvider.createCashfreeSubscription(
      widget.itemId,
      subscriptionId,
      userEmail,
      userMobileNo,
      userName,
      returnUrl: returnUrl,
    );

    if (!mounted) return;
    prDialog.hide();

    final sessionId = subModel.result?.subscriptionSessionId;
    if (subModel.status != 200 || sessionId == null || sessionId.isEmpty) {
      // Same as the order flow — subModel.message is raw server text, not
      // a locale key, so it must not go through multilanguage lookup.
      final msg = subModel.message;
      if (msg != null && msg.isNotEmpty) {
        Utils.showSnackbar(context, msg, false);
      } else {
        Utils.showSnackbar(context, "payment_not_processed", true);
      }
      return;
    }

    try {
      final isLive = subModel.result?.isLive ?? false;
      final session = CFSubscriptionSessionBuilder()
          .setEnvironment(
              isLive ? CFEnvironment.PRODUCTION : CFEnvironment.SANDBOX)
          .setSubscriptionId(subModel.result?.subscriptionId ?? subscriptionId)
          .setSubscriptionSessionId(sessionId)
          .build();

      final cfSubscriptionPayment =
          CFSubscriptionPaymentBuilder().setSession(session).build();

      _cfPaymentGatewayService.doPayment(cfSubscriptionPayment);
    } catch (e) {
      printLog("Cashfree subscription doPayment error ============> $e");
      if (!mounted) return;
      Utils.showSnackbar(context, "payment_not_processed", true);
    }
  }

  Future<void> _cashfreeSubscriptionVerified(String subscriptionId) async {
    if (!mounted) return;
    prDialog.hide();
    // The SDK confirms checkout completion. Only our server confirms paid access.
    final confirmation =
        await paymentProvider.cashfreeSubscriptionStatus(subscriptionId);
    if (!mounted) return;
    if (confirmation.status != 200 || confirmation.result?.paid != true) {
      Utils.showSnackbar(
          context,
          confirmation.message ??
              'Payment is awaiting confirmation. Check your subscription shortly.',
          false);
      return;
    }
    await sharedPref.remove("cf_pending_subscription_id");
    if (!mounted) return;
    await context.read<ProfileProvider>().getProfile(context);
    if (!mounted) return;
    isPaymentDone = true;
    await musicManager.clearMusicPlayer();
    if (!mounted) return;
    Utils.showSnackbar(context, "subscription_activated_msg", true);
    Navigator.of(context).pushAndRemoveUntil(
        MaterialPageRoute(builder: (context) => Homemusic()),
        (Route route) => false);
  }
  /* ********* Cashfree Subscription END ********* */

  /* ********  Fluttter Wave START ********** */
  Future<void> _flutterwaveinit() async {
    if (paymentProvider.paymentOptionModel.result?.flutterWave != null) {
      printLog(
          "public key =${paymentProvider.paymentOptionModel.result?.flutterWave?.key1} ");
      printLog(
          "secret key =${paymentProvider.paymentOptionModel.result?.flutterWave?.key2} ");
      /* Check Keys */
      bool isContinue = checkKeysAndContinue(
        isLive:
            (paymentProvider.paymentOptionModel.result?.flutterWave?.isLive ??
                ""),
        isBothKeyReq: false,
        liveKey1:
            (paymentProvider.paymentOptionModel.result?.flutterWave?.key2 ??
                ""),
        liveKey2: "",
        testKey1:
            (paymentProvider.paymentOptionModel.result?.flutterWave?.key2 ??
                ""),
        testKey2: "",
      );
      if (!isContinue) return;
      /* Check Keys */
      handlePaymentInitialization();
    } else {
      Utils.showSnackbar(context, "payment_not_processed", true);
    }
  }

  Future<void> handlePaymentInitialization() async {
    final Customer customer = Customer(
        name: userName.toString(),
        phoneNumber: userMobileNo.toString(),
        email: userEmail.toString());

    final Flutterwave flutterwave = Flutterwave(
      context: context,
      publicKey: paymentProvider
                  .paymentOptionModel.result?.flutterWave?.isLive ==
              "1"
          ? paymentProvider.paymentOptionModel.result?.flutterWave?.key1 ?? ""
          : paymentProvider.paymentOptionModel.result?.flutterWave?.key1 ?? "",
      currency: Constant.currency,
      redirectUrl: "https://portal.jailaoi.com/payment/success",
      txRef: const Uuid().v1(),
      amount: widget.price ?? "",
      customer: customer,
      paymentOptions: "ussd, card, barter, payattitude",
      customization: Customization(title: "My Payment"),
      isTestMode:
          paymentProvider.paymentOptionModel.result?.flutterWave?.isLive != "1",
    );
    final ChargeResponse response = await flutterwave.charge();
    if (response.status == "success") {
      if (widget.payType == "Package") {
        addTransaction(widget.itemId, widget.itemTitle,
            paymentProvider.finalAmount, paymentId, widget.currency);
      } else {
        joinEventTransection(widget.itemId, widget.contentType,
            paymentProvider.finalAmount, paymentId, widget.itemTitle);
      }
    }
    showMessageDialog(response.status.toString());
  }

  Future<void> showMessageDialog(String message) {
    return showDialog(
      context: context,
      barrierDismissible: true,
      barrierColor: black.withValues(alpha: 0.6),
      builder: (BuildContext context) {
        return Dialog(
          backgroundColor: black,
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(8),
          ),
          child: Padding(
            padding: const EdgeInsets.fromLTRB(20, 16, 20, 16),
            child: Text(
              message,
              textAlign: TextAlign.center,
              maxLines: 2,
              overflow: TextOverflow.ellipsis,
              style: TextStyle(
                color: brandGreen,
                fontSize: Dimens.textMedium,
                fontWeight: FontWeight.w600,
              ),
            ),
          ),
        );
      },
    );
  }

  /* ********  Fluttter Wave END ********** */

  Future<void> onBackPressed(bool didPop) async {
    if (didPop) return;
    if (!mounted) return;
    if (Navigator.canPop(context)) {
      Navigator.pop(context, isPaymentDone);
    }
  }
}

class JailaoiPaymentQueueDelegate implements SKPaymentQueueDelegateWrapper {
  @override
  bool shouldContinueTransaction(
      SKPaymentTransactionWrapper transaction, SKStorefrontWrapper storefront) {
    return true;
  }

  @override
  bool shouldShowPriceConsent() {
    return false;
  }
}
