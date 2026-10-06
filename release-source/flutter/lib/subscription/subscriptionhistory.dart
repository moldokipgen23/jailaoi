import 'package:jailaoi/provider/paymentprovider.dart';
import 'package:jailaoi/provider/subhistoryprovider.dart';
import 'package:jailaoi/utils/color.dart';
import 'package:jailaoi/utils/constant.dart';
import 'package:jailaoi/utils/dimens.dart';
import 'package:jailaoi/utils/utils.dart';
import 'package:jailaoi/widget/mytext.dart';
import 'package:jailaoi/pages/nodata.dart';
import 'package:flutter/material.dart';
import 'package:flutter_staggered_grid_view/flutter_staggered_grid_view.dart';
import 'package:provider/provider.dart';

class SubscriptionHistory extends StatefulWidget {
  const SubscriptionHistory({super.key});

  @override
  State<SubscriptionHistory> createState() => _SubscriptionHistoryState();
}

class _SubscriptionHistoryState extends State<SubscriptionHistory> {
  late SubHistoryProvider subHistoryProvider;
  late PaymentProvider paymentProvider;
  bool _cancelling = false;

  @override
  void initState() {
    subHistoryProvider =
        Provider.of<SubHistoryProvider>(context, listen: false);
    paymentProvider = Provider.of<PaymentProvider>(context, listen: false);
    _getData();
    super.initState();
  }

  Future<void> _getData() async {
    await subHistoryProvider.getTransactionList();
  }

  /// RBI's e-mandate framework requires a genuinely reachable opt-out — this
  /// is that opt-out. Cancels the recurring mandate with Cashfree; already
  ///-paid access continues until its expiry_date, it just won't renew again.
  Future<void> _cancelAutoRenew(String subscriptionId) async {
    if (_cancelling) return;
    setState(() => _cancelling = true);
    final result =
        await paymentProvider.cancelCashfreeSubscription(subscriptionId);
    if (!mounted) return;
    setState(() => _cancelling = false);
    Utils.showSnackbar(
        context,
        result.status == 200
            ? (result.message ?? "Auto-renew cancelled.")
            : (result.message ?? "payment_fail"),
        false);
    if (result.status == 200) {
      await _getData();
    }
  }

  @override
  void dispose() {
    subHistoryProvider.clearProvider();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: appBgColor,
      appBar: Utils.myAppBarWithBack(context, "transactions", true),
      body: SafeArea(
        child: Column(
          children: [
            Expanded(
              child: SingleChildScrollView(
                padding: const EdgeInsets.only(top: 12, bottom: 12),
                child: Consumer<SubHistoryProvider>(
                  builder: (context, subHistoryProvider, child) {
                    if (subHistoryProvider.loading) {
                      return Utils.pageLoader();
                    } else {
                      if (subHistoryProvider.historyModel.status == 200 &&
                          subHistoryProvider.historyModel.result != null) {
                        if ((subHistoryProvider.historyModel.result?.length ??
                                0) >
                            0) {
                          return AlignedGridView.count(
                            shrinkWrap: true,
                            crossAxisCount: 1,
                            crossAxisSpacing: 0,
                            mainAxisSpacing: 12,
                            padding: const EdgeInsets.only(left: 15, right: 15),
                            physics: const NeverScrollableScrollPhysics(),
                            itemCount: subHistoryProvider
                                    .historyModel.result?.length ??
                                0,
                            itemBuilder: (BuildContext context, int position) {
                              return _buildHistoryItem(position);
                            },
                          );
                        } else {
                          return const NoData(text: "", subTitle: "");
                        }
                      } else {
                        return const NoData(text: "", subTitle: "");
                      }
                    }
                  },
                ),
              ),
            ),
            /* AdMob Banner */
            Container(
              child: Utils.showBannerAd(context),
            ),
          ],
        ),
      ),
    );
  }

  bool _checkExpiry(int position) {
    printLog("position ======> $position");
    printLog(
        "expDate =======> ${subHistoryProvider.historyModel.result?[position].expiryDate}");
    if (subHistoryProvider.historyModel.result?[position].status != 1) {
      return false;
    }
    if ((subHistoryProvider.historyModel.result?[position].expiryDate ?? "") !=
        "") {
      final expiry = DateTime.tryParse(
          subHistoryProvider.historyModel.result?[position].expiryDate ?? "");
      return expiry != null && DateTime.now().isBefore(expiry);
    } else {
      return false;
    }
  }

  Widget _buildHistoryItem(dynamic position) {
    return Container(
      width: MediaQuery.of(context).size.width,
      constraints: const BoxConstraints(minHeight: 70),
      decoration: Utils.setBackground(
          _checkExpiry(position) ? colorPrimary : appBgColor, 5),
      padding: const EdgeInsets.all(12),
      child: Row(
        children: [
          Expanded(
            child: Container(
              padding: const EdgeInsets.fromLTRB(0, 0, 15, 0),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                mainAxisAlignment: MainAxisAlignment.start,
                children: [
                  /* Title */
                  MyText(
                    color: _checkExpiry(position) ? black : white,
                    text: subHistoryProvider
                            .historyModel.result?[position].packageName ??
                        "",
                    textalign: TextAlign.start,
                    maxline: 2,
                    overflow: TextOverflow.ellipsis,
                    fontsize: Dimens.textBig,
                    fontwaight: FontWeight.w700,
                    fontstyle: FontStyle.normal,
                  ),

                  /* Price */
                  Container(
                    constraints: const BoxConstraints(minHeight: 0),
                    margin: const EdgeInsets.only(top: 5),
                    child: Row(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        MyText(
                          color: _checkExpiry(position) ? black : appBgColor,
                          text: "price",
                          textalign: TextAlign.center,
                          fontsize: Dimens.textMedium,
                          fontwaight: FontWeight.w500,
                          maxline: 1,
                          multilanguage: true,
                          overflow: TextOverflow.ellipsis,
                          fontstyle: FontStyle.normal,
                        ),
                        const SizedBox(width: 5),
                        MyText(
                          color: _checkExpiry(position) ? black : appBgColor,
                          text: ":",
                          textalign: TextAlign.center,
                          fontsize: Dimens.textMedium,
                          fontwaight: FontWeight.w500,
                          maxline: 1,
                          multilanguage: false,
                          overflow: TextOverflow.ellipsis,
                          fontstyle: FontStyle.normal,
                        ),
                        const SizedBox(width: 5),
                        Expanded(
                          child: MyText(
                            color: _checkExpiry(position) ? black : white,
                            text:
                                "${Constant.currencySymbol.isNotEmpty ? Constant.currencySymbol : '₹'}${subHistoryProvider.historyModel.result?[position].amount ?? ''}",
                            textalign: TextAlign.start,
                            fontsize: Dimens.textMedium,
                            fontwaight: FontWeight.w700,
                            multilanguage: false,
                            maxline: 1,
                            overflow: TextOverflow.ellipsis,
                            fontstyle: FontStyle.normal,
                          ),
                        ),
                      ],
                    ),
                  ),

                  /* Expire On */
                  Container(
                    constraints: const BoxConstraints(minHeight: 0),
                    margin: const EdgeInsets.only(top: 5),
                    child: Row(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        MyText(
                          color: _checkExpiry(position) ? black : appBgColor,
                          text: _checkExpiry(position)
                              ? "expired_on"
                              : "expire_on",
                          textalign: TextAlign.center,
                          fontsize: Dimens.textMedium,
                          fontwaight: FontWeight.w500,
                          maxline: 1,
                          multilanguage: true,
                          overflow: TextOverflow.ellipsis,
                          fontstyle: FontStyle.normal,
                        ),
                        const SizedBox(width: 5),
                        MyText(
                          color: _checkExpiry(position) ? black : appBgColor,
                          text: ":",
                          textalign: TextAlign.center,
                          fontsize: Dimens.textMedium,
                          fontwaight: FontWeight.w500,
                          maxline: 1,
                          multilanguage: false,
                          overflow: TextOverflow.ellipsis,
                          fontstyle: FontStyle.normal,
                        ),
                        const SizedBox(width: 5),
                        Expanded(
                          child: MyText(
                            color: _checkExpiry(position) ? black : white,
                            text: (subHistoryProvider.historyModel
                                            .result?[position].expiryDate !=
                                        null ||
                                    (subHistoryProvider.historyModel
                                                .result?[position].expiryDate ??
                                            "") !=
                                        "")
                                ? (subHistoryProvider.historyModel
                                        .result?[position].expiryDate
                                        .toString() ??
                                    "")
                                : "-",
                            textalign: TextAlign.start,
                            fontsize: Dimens.textMedium,
                            fontwaight: FontWeight.w700,
                            multilanguage: false,
                            maxline: 5,
                            overflow: TextOverflow.ellipsis,
                            fontstyle: FontStyle.normal,
                          ),
                        ),
                      ],
                    ),
                  ),
                ],
              ),
            ),
          ),
          if (subHistoryProvider.historyModel.result?[position].expiryDate !=
                  null ||
              (subHistoryProvider.historyModel.result?[position].expiryDate ??
                      "") !=
                  "")
            Column(
              crossAxisAlignment: CrossAxisAlignment.end,
              children: [
                Container(
                  height: 32,
                  constraints: const BoxConstraints(minWidth: 0),
                  decoration: Utils.setBGWithBorder(
                      _checkExpiry(position) ? colorAccent : colorPrimary,
                      white,
                      15,
                      0.5),
                  padding: const EdgeInsets.fromLTRB(8, 0, 8, 0),
                  alignment: Alignment.center,
                  child: MyText(
                    color: _checkExpiry(position) ? white : black,
                    text: _checkExpiry(position) ? "current" : "expired",
                    multilanguage: true,
                    textalign: TextAlign.center,
                    maxline: 1,
                    overflow: TextOverflow.ellipsis,
                    fontsize: Dimens.textMedium,
                    fontwaight: FontWeight.w700,
                    fontstyle: FontStyle.normal,
                  ),
                ),
                if ([
                      'created',
                      'active',
                      'on_hold',
                      'customer_paused',
                      'bank_approval_pending'
                    ].contains(subHistoryProvider
                        .historyModel.result?[position].subscriptionStatus) &&
                    (subHistoryProvider.historyModel.result?[position]
                                .cfSubscriptionId ??
                            "")
                        .isNotEmpty)
                  Padding(
                    padding: const EdgeInsets.only(top: 6),
                    child: InkWell(
                      onTap: _cancelling
                          ? null
                          : () => _cancelAutoRenew(subHistoryProvider
                              .historyModel
                              .result![position]
                              .cfSubscriptionId!),
                      child: MyText(
                        color: colorPrimary,
                        text: _cancelling ? "..." : "Cancel Auto-Renew",
                        multilanguage: false,
                        textalign: TextAlign.end,
                        maxline: 1,
                        overflow: TextOverflow.ellipsis,
                        fontsize: Dimens.textSmall,
                        fontwaight: FontWeight.w600,
                        fontstyle: FontStyle.normal,
                      ),
                    ),
                  ),
              ],
            ),
        ],
      ),
    );
  }
}
