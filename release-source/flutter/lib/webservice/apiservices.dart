import 'package:firebase_auth/firebase_auth.dart';
import 'package:jailaoi/utils/sharedpref.dart';
import 'dart:convert';
import 'dart:io';
import 'package:dio/dio.dart';
import 'package:flutter/foundation.dart';
import 'package:path/path.dart';
import 'package:jailaoi/model/adduseractionmodel.dart';
import 'package:jailaoi/model/bannermodel.dart';
import 'package:jailaoi/model/commentlistmodel.dart';
import 'package:jailaoi/model/getfavoritelistmodel.dart';
import 'package:jailaoi/model/getreleteddatamodel.dart';
import 'package:jailaoi/model/introscreenmodel.dart';
import 'package:jailaoi/model/podcastsectiondetailmodel.dart';
import 'package:jailaoi/model/registermodel.dart';
import 'package:jailaoi/model/sectiondetailmodel.dart';
import 'package:jailaoi/model/sectionlistmodel.dart';
import 'package:jailaoi/model/sociallinkmodel.dart';
import 'package:jailaoi/utils/utils.dart';
import 'package:pretty_dio_logger/pretty_dio_logger.dart';
import 'package:jailaoi/model/citymodel.dart';
import 'package:jailaoi/model/audiomodel.dart';
import 'package:jailaoi/model/getepisodebypodcastmodel.dart';
import 'package:jailaoi/model/historymodel.dart';
import 'package:jailaoi/model/languagemodel.dart';
import 'package:jailaoi/model/generalsettingmodel.dart';
import 'package:jailaoi/model/liveeventmodel.dart';
import 'package:jailaoi/model/loginmodel.dart';
import 'package:jailaoi/model/notificationlistmodel.dart';
import 'package:jailaoi/model/cashfreeordermodel.dart';
import 'package:jailaoi/model/cashfreesubscriptionmodel.dart';
import 'package:jailaoi/model/pagesmodel.dart';
import 'package:jailaoi/model/paymentoptionmodel.dart';
import 'package:jailaoi/model/paytmmodel.dart';
import 'package:jailaoi/model/podcastsectionmodel.dart';
import 'package:jailaoi/model/profilemodel.dart';
import 'package:jailaoi/model/categorymodel.dart';
import 'package:jailaoi/model/searchmodel.dart';
import 'package:jailaoi/model/subscriptionmodel.dart';
import 'package:jailaoi/model/successmodel.dart';
import 'package:jailaoi/model/supportticketmodel.dart';
import 'package:jailaoi/model/artistmodel.dart';
import 'package:jailaoi/model/updateprofilemodel.dart';
import 'package:jailaoi/utils/constant.dart';

class ApiService {
  String baseurl = Constant().baseurl;
  late Dio dio;

  Options optHeaders = Options(headers: <String, dynamic>{
    'Content-Type': 'application/json',
  });
  ApiService() {
    dio = Dio(BaseOptions(
      connectTimeout: const Duration(seconds: 30),
      receiveTimeout: const Duration(seconds: 30),
      sendTimeout: const Duration(seconds: 30),
    ));
    dio.interceptors.add(InterceptorsWrapper(
      onRequest: (options, handler) async {
        final token = await SharedPref().read('auth_token');
        if (token != null && token.contains('|')) {
          options.headers['Authorization'] = 'Bearer $token';
        }
        handler.next(options);
      },
      onError: (error, handler) async {
        if (error.response?.statusCode == 401) {
          await SharedPref().remove('auth_token');
          await SharedPref().remove('userid');
          Constant.userID = null;
        }
        handler.next(error);
      },
      onResponse: (response, handler) async {
        if (response.data is Map && response.data['token'] is String) {
          await SharedPref().save('auth_token', response.data['token']);
        }
        handler.next(response);
      },
    ));
    if (kDebugMode) {
      dio.interceptors.add(
        PrettyDioLogger(
          requestHeader: false,
          requestBody: false,
          responseBody: false,
          responseHeader: false,
          error: true,
          compact: true,
        ),
      );
    }
  }

  /*  =========================== General Api Start =========================== */

  Future<GeneralsettingModel> generalSetting() async {
    GeneralsettingModel generalsettingModel;
    String generalsetting = 'general_setting';
    Response response = await dio.post(
      '$baseurl$generalsetting',
      options: optHeaders,
    );
    generalsettingModel = GeneralsettingModel.fromJson((response.data));
    return generalsettingModel;
  }

  Future<IntroScreenModel> getOnboardingScreen() async {
    IntroScreenModel introScreenModel;
    String apiName = "get_onboarding_screen";
    Response response = await dio.post(
      '$baseurl$apiName',
    );
    introScreenModel = IntroScreenModel.fromJson(response.data);
    return introScreenModel;
  }

  Future<PagesModel> getPages() async {
    PagesModel pagesModel;
    String getPagesAPI = "get_pages";
    Response response = await dio.post(
      '$baseurl$getPagesAPI',
      options: optHeaders,
    );
    pagesModel = PagesModel.fromJson(response.data);
    return pagesModel;
  }

  Future<RegisterModel> register(
      dynamic type,
      fullName,
      email,
      mobile,
      password,
      countryCode,
      countryName,
      deviceToken,
      deviceType,
      gender) async {
    RegisterModel registerModel;
    String generalsetting = 'register';
    Response response = await dio.post(
      '$baseurl$generalsetting',
      data: FormData.fromMap({
        'type': type,
        'full_name': fullName,
        'email': email,
        'mobile_number': mobile,
        'password': password,
        'country_code': countryCode,
        'country_name': countryName,
        'device_token': deviceToken,
        'device_type': deviceType,
        "gender": gender,
      }),
      options: optHeaders,
    );
    registerModel = RegisterModel.fromJson((response.data));
    return registerModel;
  }

  Future<LoginModel> login(dynamic type, mobile, email, password, deviceToken,
      deviceType, countryCode, countryName) async {
    LoginModel loginModel;
    String login = "login";
    Response response = await dio.post(
      '$baseurl$login',
      data: FormData.fromMap({
        'type': type,
        if ([1, 2, 3].contains(int.tryParse(type.toString())))
          'identity_token':
              await FirebaseAuth.instance.currentUser?.getIdToken(),
        'mobile_number': mobile,
        'email': email,
        'password': password,
        'device_token': deviceToken,
        'device_type': deviceType,
        'country_code': countryCode,
        'country_name': countryName,
      }),
      options: optHeaders,
    );
    loginModel = LoginModel.fromJson(response.data);
    return loginModel;
  }

  Future<SocialLinkModel> getSocialLink() async {
    SocialLinkModel socialLinkModel;
    String apiname = "get_social_link";
    Response response = await dio.post('$baseurl$apiname');
    socialLinkModel = SocialLinkModel.fromJson(response.data);
    return socialLinkModel;
  }

  /* =========================== General Api End =========================== */

  /* =========================== Home Section Api End =========================== */

  Future<BannerModel> getBanner(dynamic pageNo) async {
    try {
      String login = "get_banner";
      Response response = await dio.post(
        '$baseurl$login',
        data: FormData.fromMap({
          'page_no': pageNo,
        }),
        options: optHeaders,
      );
      return BannerModel.fromJson(response.data);
    } catch (e) {
      printLog("getBanner error: $e");
      return BannerModel();
    }
  }

  Future<SectionListModel> sectionList(int sectiontype, dynamic pageNo) async {
    SectionListModel sectionListModel;
    String login = "get_section_list";
    Response response = await dio.post(
      '$baseurl$login',
      data: FormData.fromMap({
        'section_type': sectiontype,
        'user_id': (Constant.userID == null) ? 0 : Constant.userID,
        'page_no': pageNo,
      }),
      // options: optHeaders,
    );
    sectionListModel = SectionListModel.fromJson(response.data);
    printLog("Sectionlistjson======>>: ${jsonEncode(response.data)}");

    return sectionListModel;
  }

  Future<SectionDetailModel> sectionDetail(dynamic sectionId, pageNo) async {
    SectionDetailModel sectionDetailModel;
    String apiName = "get_section_detail";
    Response response = await dio.post(
      '$baseurl$apiName',
      data: FormData.fromMap({
        'section_id': sectionId,
        'user_id': (Constant.userID == null) ? 0 : Constant.userID,
        'page_no': pageNo,
      }),
      // options: optHeaders,
    );
    sectionDetailModel = SectionDetailModel.fromJson(response.data);
    return sectionDetailModel;
  }

  Future<SectionListModel> radiosectionList(dynamic pageNo) async {
    SectionListModel sectionListModel;
    String login = "get_radio_section_list";
    Response response = await dio.post(
      '$baseurl$login',
      data: FormData.fromMap({
        'page_no': pageNo,
      }),
      // options: optHeaders,
    );
    sectionListModel = SectionListModel.fromJson(response.data);
    printLog("Sectionlistjson======>>: ${jsonEncode(response.data)}");

    return sectionListModel;
  }

  Future<SectionListModel> podcastsectionList(dynamic pageNo) async {
    SectionListModel sectionListModel;
    String login = "get_podcast_section_list";
    Response response = await dio.post(
      '$baseurl$login',
      data: FormData.fromMap({
        'page_no': pageNo,
      }),
      // options: optHeaders,
    );
    sectionListModel = SectionListModel.fromJson(response.data);
    printLog("Sectionlistjson======>>: ${jsonEncode(response.data)}");

    return sectionListModel;
  }

  Future<SectionListModel> musicsectionList(dynamic pageNo) async {
    SectionListModel sectionListModel;
    String login = "get_music_section_list";
    Response response = await dio.post(
      '$baseurl$login',
      data: FormData.fromMap({
        'page_no': pageNo,
      }),
      // options: optHeaders,
    );
    sectionListModel = SectionListModel.fromJson(response.data);
    printLog("Sectionlistjson======>>: ${jsonEncode(response.data)}");

    return sectionListModel;
  }

  /* =========================== Home Sections Api End =========================== */

  /* =========================== User Profile & Update Profile Start =========================== */

  Future<ProfileModel> profile() async {
    ProfileModel profileModel;
    String profile = 'get_profile';
    Response response = await dio.post(
      '$baseurl$profile',
      data: FormData.fromMap({
        'user_id': (Constant.userID == null) ? 0 : Constant.userID,
      }),
      options: optHeaders,
    );
    profileModel = ProfileModel.fromJson((response.data));
    return profileModel;
  }

  Future<UpdateprofileModel> updateprofile(String userid, String name,
      String email, String mobile, countryCode, countryName, File image) async {
    UpdateprofileModel updateprofileModel;
    String updateprofile = 'update_profile';
    Response response = await dio.post(
      '$baseurl$updateprofile',
      data: FormData.fromMap({
        'user_id': (Constant.userID == null) ? 0 : Constant.userID,
        'full_name': name,
        'email': email,
        'mobile_number': mobile,
        'country_code': countryCode,
        'country_name': countryName,
        if (image.path.isNotEmpty)
          "image": await MultipartFile.fromFile(image.path,
              filename: basename(image.path)),
      }),
      options: optHeaders,
    );
    updateprofileModel = UpdateprofileModel.fromJson((response.data));
    return updateprofileModel;
  }

  Future<UpdateprofileModel> updateDataForPayment(
      dynamic name, email, mobile) async {
    UpdateprofileModel responseModel;
    String apiName = 'update_profile';
    Response response = await dio.post(
      '$baseurl$apiName',
      data: FormData.fromMap({
        'user_id': Constant.userID ?? "",
        'full_name': name,
        'email': email,
        'mobile_number': mobile,
      }),
      options: optHeaders,
    );
    responseModel = UpdateprofileModel.fromJson((response.data));
    return responseModel;
  }

  /* =========================== User Profile & Update Profile End =========================== */

  Future<LanguageModel> language(String pageno) async {
    LanguageModel languageModel;
    String language = 'get_language';
    Response response = await dio.post(
      '$baseurl$language',
      data: FormData.fromMap({
        'page_no': pageno,
      }),
      options: optHeaders,
    );
    languageModel = LanguageModel.fromJson((response.data));
    return languageModel;
  }

  Future<CityModel> city(dynamic pageno) async {
    CityModel cityModel;
    String city = 'get_city';
    Response response = await dio.post(
      '$baseurl$city',
      data: FormData.fromMap({
        'page_no': pageno.toString(),
      }),
      options: optHeaders,
    );
    cityModel = CityModel.fromJson((response.data));
    return cityModel;
  }

  Future<NotificationModel> notification(String userid) async {
    NotificationModel notificationModel;
    String notification = 'get_notification';
    Response response = await dio.post(
      '$baseurl$notification',
      data: FormData.fromMap({
        'user_id': Constant.userID ?? "",
      }),
      options: optHeaders,
    );
    notificationModel = NotificationModel.fromJson((response.data));
    return notificationModel;
  }

  Future<SearchModel> search(dynamic searchText, type, pageNo) async {
    SearchModel searchModel;
    String apiname = 'search_content';
    try {
      Response response = await dio.post(
        '$baseurl$apiname',
        data: FormData.fromMap({
          'user_id': Constant.userID ?? "",
          'name': searchText,
          'type': type,
          'page_no': pageNo,
        }),
        options: optHeaders,
      );
      searchModel = SearchModel.fromJson((response.data));
    } catch (_) {
      searchModel = SearchModel();
    }
    return searchModel;
  }

  Future<CategoryModel> getCategories() async {
    try {
      Response response = await dio.post(
        '${baseurl}get_category',
        data: FormData.fromMap({}),
        options: optHeaders,
      );
      return CategoryModel.fromJson(response.data);
    } catch (_) {
      return CategoryModel();
    }
  }

  Future<AudioModel> radiobyartist(String artistid, String pageno) async {
    AudioModel getradiobyartistModel;
    String getradiobyartist = 'get_radio_by_artist';
    Response response = await dio.post(
      '$baseurl$getradiobyartist',
      data: FormData.fromMap({
        'user_id': Constant.userID ?? "",
        'artist_id': artistid,
        'page_no': pageno,
      }),
      options: optHeaders,
    );
    getradiobyartistModel = AudioModel.fromJson((response.data));
    return getradiobyartistModel;
  }

  Future<AudioModel> radiobycity(
      String cityid, String languageid, String pageno) async {
    AudioModel responseModel;
    String apiName = 'get_radio_by_city';
    Response response = await dio.post(
      '$baseurl$apiName',
      data: FormData.fromMap({
        'user_id': Constant.userID ?? "",
        'city_id': cityid,
        'language_id': languageid,
        'page_no': pageno,
      }),
      options: optHeaders,
    );
    responseModel = AudioModel.fromJson((response.data));
    return responseModel;
  }

  Future<AudioModel> radiobycategory(
      String categoryid, String languageid, String pageno) async {
    // Wrapped: an uncaught throw here (e.g. a JSON parse error) leaves
    // RadioByIdProvider.loading stuck true forever — an infinite black
    // shimmer on the category page. Return an empty model instead.
    try {
      String apiName = 'get_radio_by_category';
      Response response = await dio.post(
        '$baseurl$apiName',
        data: FormData.fromMap({
          'user_id': (Constant.userID == null) ? 0 : Constant.userID,
          'category_id': categoryid,
          'language_id': languageid,
          'page_no': pageno,
        }),
      );
      return AudioModel.fromJson((response.data));
    } catch (e) {
      printLog("radiobycategory error: $e");
      return AudioModel();
    }
  }

  Future<AudioModel> radiobylanguage(String languageid, String pageno) async {
    try {
      String apiName = 'get_radio_by_language';
      Response response = await dio.post(
        '$baseurl$apiName',
        data: FormData.fromMap({
          'user_id': (Constant.userID == null) ? 0 : Constant.userID,
          'language_id': languageid,
          'page_no': pageno,
        }),
        options: optHeaders,
      );
      return AudioModel.fromJson((response.data));
    } catch (e) {
      printLog("radiobylanguage error: $e");
      return AudioModel();
    }
  }

  // Curated Radio Station — resolves a station to its songs by its
  // category/artist/language filters (server-side). Returns music, plays like radio.
  Future<AudioModel> getStationSongs(String stationId, String pageno) async {
    try {
      Response response = await dio.post(
        '${baseurl}get_station_songs',
        data: FormData.fromMap({
          'user_id': (Constant.userID == null) ? 0 : Constant.userID,
          'station_id': stationId,
          'page_no': pageno,
        }),
        options: optHeaders,
      );
      return AudioModel.fromJson((response.data));
    } catch (e) {
      printLog("getStationSongs error: $e");
      return AudioModel();
    }
  }

  Future<SuccessModel> addfavourite(dynamic type, contentid) async {
    SuccessModel successModel;
    String addfavourite = 'add_remove_favorite';
    Response response = await dio.post(
      '$baseurl$addfavourite',
      data: FormData.fromMap({
        'user_id': (Constant.userID == null) ? 0 : Constant.userID,
        'type': type,
        'content_id': contentid,
      }),
      options: optHeaders,
    );
    successModel = SuccessModel.fromJson((response.data));
    return successModel;
  }

  /*  ======================== Payment Related Api Start ======================== */

  Future<SubscriptionModel> getPackage() async {
    SubscriptionModel subscriptionModel;
    String getPackageAPI = "get_package";
    Response response = await dio.post(
      '$baseurl$getPackageAPI',
      data: FormData.fromMap({
        'user_id': Constant.userID,
      }),
      options: optHeaders,
    );
    subscriptionModel = SubscriptionModel.fromJson(response.data);
    return subscriptionModel;
  }

  Future<PaymentOptionModel> getPaymentOption() async {
    String paymentOption = "get_payment_option";
    printLog("paymentOption API :==> $baseurl$paymentOption");

    Response response = await dio.post(
      '$baseurl$paymentOption',
      options: optHeaders,
    );

    // API returns List
    if (response.data is List && response.data.isNotEmpty) {
      return PaymentOptionModel.fromJson(response.data[0]);
    }

    // fallback if API ever returns Map
    if (response.data is Map<String, dynamic>) {
      return PaymentOptionModel.fromJson(response.data);
    }
    printLog("Payment option response ===> ${response.data}");
    printLog("Type ===> ${response.data.runtimeType}");

    throw Exception("Invalid payment option response");
  }

  Future<CashfreeOrderModel> createCashfreeOrder(
      dynamic packageId, amount, orderId, email, phone,
      {String? returnUrl}) async {
    printLog('cashfree/create-order userID =====>>> ${Constant.userID}');
    printLog('cashfree/create-order packageId ==>>> $packageId');
    printLog('cashfree/create-order amount ======>>> $amount');
    printLog('cashfree/create-order orderId =====>>> $orderId');
    String api = "cashfree/create-order";
    Response response = await dio.post(
      '$baseurl$api',
      data: FormData.fromMap({
        'user_id': (Constant.userID == null) ? 0 : Constant.userID,
        'package_id': packageId,
        'amount': amount,
        'order_id': orderId,
        'email': email ?? '',
        'phone': phone ?? '',
        if (returnUrl != null) 'return_url': returnUrl,
      }),
      options: optHeaders,
    );
    return CashfreeOrderModel.fromJson(response.data);
  }

  Future<CashfreeSubscriptionModel> createCashfreeSubscription(
      dynamic packageId, subscriptionId, email, phone, name,
      {String? returnUrl}) async {
    printLog('cashfree/create-subscription userID =====>>> ${Constant.userID}');
    printLog('cashfree/create-subscription packageId ==>>> $packageId');
    printLog(
        'cashfree/create-subscription subscriptionId =>>> $subscriptionId');
    String api = "cashfree/create-subscription";
    Response response = await dio.post(
      '$baseurl$api',
      data: FormData.fromMap({
        'user_id': (Constant.userID == null) ? 0 : Constant.userID,
        'package_id': packageId,
        'subscription_id': subscriptionId,
        'email': email ?? '',
        'phone': phone ?? '',
        'name': name ?? '',
        if (returnUrl != null) 'return_url': returnUrl,
      }),
      options: optHeaders,
    );
    return CashfreeSubscriptionModel.fromJson(response.data);
  }

  Future<SuccessModel> cancelCashfreeSubscription(
      dynamic subscriptionId) async {
    printLog(
        'cashfree/cancel-subscription subscriptionId =>>> $subscriptionId');
    String api = "cashfree/cancel-subscription";
    Response response = await dio.post(
      '$baseurl$api',
      data: FormData.fromMap({
        'user_id': (Constant.userID == null) ? 0 : Constant.userID,
        'subscription_id': subscriptionId,
      }),
      options: optHeaders,
    );
    return SuccessModel.fromJson(response.data);
  }

  Future<CashfreeOrderModel> verifyCashfreeOrder(dynamic orderId) async {
    printLog('cashfree/verify-order orderId =====>>> $orderId');
    String api = "cashfree/verify-order";
    Response response = await dio.post(
      '$baseurl$api',
      data: FormData.fromMap({
        'order_id': orderId,
      }),
      options: optHeaders,
    );
    return CashfreeOrderModel.fromJson(response.data);
  }

  Future<SuccessModel> addTransaction(
      dynamic packageId, description, amount, paymentId) async {
    printLog('add_transaction userID =======>>> ${Constant.userID}');
    printLog('add_transaction packageId ====>>> $packageId');
    printLog('add_transaction description ==>>> $description');
    printLog('add_transaction amount =======>>> $amount');
    printLog('add_transaction paymentId ====>>> $paymentId');
    SuccessModel successModel;
    String transactionAPI = "add_transaction";
    Response response = await dio.post(
      '$baseurl$transactionAPI',
      data: FormData.fromMap({
        'user_id': (Constant.userID == null) ? 0 : Constant.userID,
        'package_id': packageId,
        'price': amount,
        'description': description,
        'transaction_id': paymentId,
      }),
      options: optHeaders,
    );
    successModel = SuccessModel.fromJson(response.data);
    return successModel;
  }

  Future<SuccessModel> addLiveEventTransaction(
      dynamic eventId, type, amount, transectionId, discription) async {
    printLog('add_transaction userID =======>>> ${Constant.userID}');
    printLog('add_transaction packageId ====>>> $eventId');
    printLog('add_transaction description ==>>> $type');
    printLog('add_transaction amount =======>>> $amount');
    printLog('add_transaction paymentId ====>>> $transectionId');
    printLog('add_transaction currencyCode =>>> $discription');
    SuccessModel successModel;
    String apiname = "join_live_event";
    Response response = await dio.post(
      '$baseurl$apiname',
      data: FormData.fromMap({
        'user_id': (Constant.userID == null) ? 0 : Constant.userID,
        'live_event_id': eventId,
        'type': type,
        'price': amount,
        'transaction_id': transectionId,
        'description': discription,
      }),
      options: optHeaders,
    );
    successModel = SuccessModel.fromJson(response.data);
    return successModel;
  }

  Future<HistoryModel> transactionList() async {
    HistoryModel historyModel;
    String subscriptionListAPI = "transaction_list";
    Response response = await dio.post(
      '$baseurl$subscriptionListAPI',
      data: FormData.fromMap({
        'user_id': (Constant.userID == null) ? 0 : Constant.userID,
      }),
      options: optHeaders,
    );
    historyModel = HistoryModel.fromJson(response.data);
    return historyModel;
  }

  Future<PayTmModel> getPaytmToken(dynamic merchantID, orderId, custmoreID,
      channelID, txnAmount, website, callbackURL, industryTypeID) async {
    PayTmModel payTmModel;
    String paytmToken = "get_payment_token";
    printLog("paytmToken API :==> $baseurl$paytmToken");
    Response response = await dio.post(
      '$baseurl$paytmToken',
      data: FormData.fromMap({
        'MID': merchantID,
        'order_id': orderId,
        'CUST_ID': custmoreID,
        'CHANNEL_ID': channelID,
        'TXN_AMOUNT': txnAmount,
        'WEBSITE': website,
        'CALLBACK_URL': callbackURL,
        'INDUSTRY_TYPE_ID': industryTypeID,
      }),
      options: optHeaders,
    );

    payTmModel = PayTmModel.fromJson(response.data);
    printLog("getPaytmToken payTmModel ==> $payTmModel");
    return payTmModel;
  }

  /*  ======================== Payment Related Api End ======================== */

/* Version 1.5 Intigrate New Api Start */

  Future<LiveEventModel> liveEventList(dynamic pageNo) async {
    LiveEventModel liveEventModel;
    String apiname = "get_live_event";
    Response response = await dio.post(
      '$baseurl$apiname',
      data: FormData.fromMap({
        'user_id': (Constant.userID == null) ? 0 : Constant.userID,
        'page_no': pageNo,
      }),
      options: optHeaders,
    );
    liveEventModel = LiveEventModel.fromJson(response.data);
    return liveEventModel;
  }

  Future<PodcastSectionModel> podcastSectionList(dynamic pageNo) async {
    PodcastSectionModel podcastSectionModel;
    String apiname = "get_podcast_section_list";
    Response response = await dio.post(
      '$baseurl$apiname',
      data: FormData.fromMap({
        'user_id': (Constant.userID == null) ? 0 : Constant.userID,
        'page_no': pageNo,
      }),
      options: optHeaders,
    );
    podcastSectionModel = PodcastSectionModel.fromJson(response.data);
    return podcastSectionModel;
  }

  Future<PodcastSectionDetailModel> podcastSectionDetailList(
      dynamic sectionId, pageNo) async {
    PodcastSectionDetailModel podcastSectionDetailModel;
    String apiname = "get_podcast_section_detail";
    Response response = await dio.post(
      '$baseurl$apiname',
      data: FormData.fromMap({
        'user_id': Constant.userID == null ? "0" : Constant.userID ?? "0",
        'section_id': sectionId,
        'page_no': pageNo,
      }),
      options: optHeaders,
    );
    podcastSectionDetailModel =
        PodcastSectionDetailModel.fromJson(response.data);
    return podcastSectionDetailModel;
  }

  Future<GetEpisodeByPodcstModel> getEpisodebyPodcast(
      dynamic podcastId, pageNo) async {
    GetEpisodeByPodcstModel getEpisodeByPodcstModel;
    String apiname = "get_episode_by_podcast";
    Response response = await dio.post(
      '$baseurl$apiname',
      data: FormData.fromMap({
        'podcast_id': podcastId,
        'user_id': (Constant.userID == null) ? 0 : Constant.userID,
        'page_no': pageNo,
      }),
      options: optHeaders,
    );
    getEpisodeByPodcstModel = GetEpisodeByPodcstModel.fromJson(response.data);
    return getEpisodeByPodcstModel;
  }

  Future<CommentListModel> commentList(
      dynamic type, songId, episodeId, pageNo) async {
    CommentListModel commentListModel;
    String apiname = "get_comment";
    Response response = await dio.post(
      '$baseurl$apiname',
      data: FormData.fromMap({
        'type': type,
        'song_id': songId,
        'episode_id': episodeId,
        'page_no': pageNo,
      }),
      options: optHeaders,
    );
    commentListModel = CommentListModel.fromJson(response.data);
    return commentListModel;
  }

  Future<SuccessModel> addComment(
      dynamic songId, comment, type, episodeId) async {
    SuccessModel successModel;
    String apiname = "add_comment";
    Response response = await dio.post(
      '$baseurl$apiname',
      data: FormData.fromMap({
        'user_id': (Constant.userID == null) ? 0 : Constant.userID,
        'song_id': songId,
        'comment': comment,
        'type': type,
        'episode_id': episodeId,
      }),
      options: optHeaders,
    );
    successModel = SuccessModel.fromJson(response.data);
    return successModel;
  }

/* Version 1.5 Intigrate New Api  End */

/* Artist Profile API */

  Future<ArtistModel> getArtistProfile(String artistId) async {
    ArtistModel artistModel;
    Response response = await dio.post(
      '${baseurl}get_artist_profile',
      data: FormData.fromMap({
        'artist_id': artistId,
        'login_user_id': Constant.userID ?? "0",
      }),
      options: optHeaders,
    );
    artistModel = ArtistModel.fromJson(response.data);
    return artistModel;
  }

  /// JAILAOI DEEPLINK: fetch a single playable content item by id.
  /// [type] 1=song, 2=podcast, 3=music (default for user uploads).
  /// Returns the raw result maps (already URL-formatted by the backend),
  /// ready to hand straight to MusicManager.setInitialPlaylist, or null.
  Future<List<Map<String, dynamic>>?> getContentDetail(
      String contentId, int type) async {
    try {
      Response response = await dio.post(
        '${baseurl}get_content_detail',
        data: FormData.fromMap({
          'content_id': contentId,
          'type': type,
          'user_id': Constant.userID ?? "0",
        }),
        options: optHeaders,
      );
      final data = response.data;
      if (data is Map && data['status'] == 200 && data['result'] is List) {
        return (data['result'] as List)
            .whereType<Map>()
            .map((e) => Map<String, dynamic>.from(e))
            .toList();
      }
    } catch (e) {
      printLog("getContentDetail error ===> $e");
    }
    return null;
  }

  Future<ArtistModel> getFollowedArtists(int pageNo) async {
    ArtistModel artistModel;
    try {
      Response response = await dio.post(
        '${baseurl}get_followed_artists',
        data: FormData.fromMap({
          'user_id': Constant.userID ?? "0",
          'page_no': pageNo,
        }),
        options: optHeaders,
      );
      artistModel = ArtistModel.fromJson(response.data);
    } catch (_) {
      artistModel = ArtistModel();
    }
    return artistModel;
  }

  Future<SearchModel> getRecentlyPlayed(int pageNo) async {
    SearchModel searchModel;
    try {
      Response response = await dio.post(
        '${baseurl}get_recently_played',
        data: FormData.fromMap({
          'user_id': Constant.userID ?? "0",
          'page_no': pageNo,
        }),
        options: optHeaders,
      );
      searchModel = SearchModel.fromJson(response.data);
    } catch (_) {
      searchModel = SearchModel();
    }
    return searchModel;
  }

/* Version 1.8 Intigrate New Api Start */

  Future<Getreleteddatamodel> getreleteddata(dynamic type, contentid) async {
    printLog('get_related_data packageId ====>>> $contentid');
    printLog('get_related_data description ====>>> $type');

    Getreleteddatamodel getreleteddatamodel;
    String apiname = "get_related_data";
    Response response = await dio.post(
      '$baseurl$apiname',
      data: FormData.fromMap({
        'content_id': contentid,
        'type': type,
      }),
      options: optHeaders,
    );
    getreleteddatamodel = Getreleteddatamodel.fromJson(response.data);
    return getreleteddatamodel;
  }

  Future<Getreleteddatamodel> getcontentbyartist(
      dynamic type, artistid, pageno) async {
    printLog('get_related_data packageId ====>>> $artistid');
    printLog('get_related_data description ====>>> $type');

    Getreleteddatamodel getreleteddatamodel;
    String apiname = "get_content_by_artist";
    Response response = await dio.post(
      '$baseurl$apiname',
      data: FormData.fromMap({
        'artist_id': artistid,
        'type': type,
        'user_id': (Constant.userID == null) ? 0 : Constant.userID,
        'pageno': pageno,
      }),
      options: optHeaders,
    );
    getreleteddatamodel = Getreleteddatamodel.fromJson(response.data);
    return getreleteddatamodel;
  }

  Future<SuccessModel> addremovefollowapi(String artistid) async {
    SuccessModel successModel;
    String followAPi = "add_remove_follow";
    Response response = await dio.post('$baseurl$followAPi',
        data: FormData.fromMap({
          'user_id': (Constant.userID == null) ? 0 : Constant.userID,
          'artist_id': artistid,
        }));

    successModel = SuccessModel.fromJson(response.data);
    return successModel;
  }

/* Version 1.8 Intigrate New Api End */

/* Version 1.9 Intigrate New Api End */

  Future<Adduseractionmodel> adduseractionapi(
    dynamic contenttype,
    contentid,
    actiontype,
    timespend,
    categoryid,
    languageid,
    cityid,
    artistid,
    contentduration,
  ) async {
    printLog('user Action Content TYpe ====>>> $contenttype');
    printLog('user Action content Id ====>>> $contentid');
    printLog('user Action actiontype ====>>> $actiontype');
    printLog('user Action time Spend ====>>> $timespend');
    printLog('user Action Categoryid ====>>> $categoryid');
    printLog('user Action langauge Id ====>>> $languageid');
    printLog('user Action City Id ====>>> $cityid');
    printLog('user Action artistid ====>>> $artistid');
    printLog('user Action content duration ====>>> $contentduration');

    Adduseractionmodel adduseractionmodel;
    String apiname = "add_user_action";
    Response response = await dio.post(
      '$baseurl$apiname',
      data: FormData.fromMap({
        'user_id': (Constant.userID == null) ? 0 : Constant.userID,
        'content_type': contenttype,
        'content_id': contentid,
        'action': actiontype,
        'time_spend': timespend,
        'category_id': categoryid,
        'language_id': languageid,
        'city_id': cityid,
        'artist_id': artistid,
        'content_duration': contentduration,
      }),
      options: optHeaders,
    );
    adduseractionmodel = Adduseractionmodel.fromJson(response.data);
    return adduseractionmodel;
  }

  Future<SuccessModel> logPlayError(
      dynamic contentId, contentType, url, errorMessage, httpStatus) async {
    SuccessModel successModel;
    String apiname = "log_play_error";
    Response response = await dio.post(
      '$baseurl$apiname',
      data: FormData.fromMap({
        'user_id': (Constant.userID == null) ? 0 : Constant.userID,
        'content_id': contentId,
        'content_type': contentType,
        'url': url,
        'error_message': errorMessage,
        'http_status': httpStatus,
      }),
      options: optHeaders,
    );
    successModel = SuccessModel.fromJson(response.data);
    return successModel;
  }

  Future<Getfavoritelistmodel> getfavlist(dynamic type, pageno) async {
    printLog('get_related_data description ====>>> $type');

    Getfavoritelistmodel getfavoritelistmodel;
    String apiname = "get_favorite_list";
    Response response = await dio.post(
      '$baseurl$apiname',
      data: FormData.fromMap({
        'type': type,
        'user_id': (Constant.userID == null) ? 0 : Constant.userID,
        'pageno': pageno,
      }),
      options: optHeaders,
    );
    getfavoritelistmodel = Getfavoritelistmodel.fromJson(response.data);
    return getfavoritelistmodel;
  }

  // Increments total_play on Song/Music/Podcast — used by "Popular" section ordering.
  // type: 1=Song, 2=Podcast, 3=Music (different from add_user_action which uses 8 for Music)
  Future<void> addPlay(dynamic contentId, int type, {dynamic episodeId}) async {
    if (Constant.userID == null) return;
    try {
      final data = <String, dynamic>{
        'user_id': Constant.userID,
        'content_id': contentId,
        'type': type,
      };
      if (episodeId != null) data['episode_id'] = episodeId;
      await dio.post(
        '${baseurl}add_play',
        data: FormData.fromMap(data),
        options: optHeaders,
      );
    } catch (_) {}
  }

  Future<void> readNotification(dynamic notificationId) async {
    if (Constant.userID == null) return;
    try {
      await dio.post(
        '${baseurl}read_notification',
        data: FormData.fromMap({
          'user_id': Constant.userID,
          'notification_id': notificationId,
        }),
        options: optHeaders,
      );
    } catch (_) {}
  }

/* Artist Portal APIs */

  Future<SuccessModel> applyArtist({
    required String artistName,
    required String bio,
    required String artistTypes,
    File? image,
  }) async {
    SuccessModel result;
    Response response = await dio.post(
      '${baseurl}apply_artist',
      data: FormData.fromMap({
        'user_id': Constant.userID,
        'artist_name': artistName,
        'bio': bio,
        'artist_types': artistTypes,
        if (image != null && image.path.isNotEmpty)
          "image": await MultipartFile.fromFile(image.path,
              filename: basename(image.path)),
      }),
      options: optHeaders,
    );
    result = SuccessModel.fromJson(response.data);
    return result;
  }

  Future<Map<String, dynamic>> getArtistRequestStatus() async {
    try {
      Response response = await dio.post(
        '${baseurl}get_artist_request_status',
        data: FormData.fromMap({'user_id': Constant.userID}),
        options: optHeaders,
      );
      final data = response.data;
      if (data['status'] == 200 && data['result'] != null) {
        final r = data['result'];
        return {
          'status': r['status'],
          'is_artist': r['is_artist'] ?? false,
          'admin_note': r['admin_note'] ?? '',
        };
      }
    } catch (_) {}
    return {'status': null, 'is_artist': false, 'admin_note': ''};
  }

  Future<Map<String, dynamic>> getArtistDashboard() async {
    try {
      Response response = await dio.post(
        '${baseurl}get_artist_dashboard',
        data: FormData.fromMap({'user_id': Constant.userID}),
        options: optHeaders,
      );
      final data = response.data;
      if (data['status'] == 200 && data['result'] != null) {
        final r = (data['result'] as List).first;
        return {
          'total_content': r['total_content'] ?? 0,
          'total_followers': r['total_followers'] ?? 0,
          'total_views': r['total_views'] ?? 0,
          'monthly_listeners': r['monthly_listeners'] ?? 0,
          'is_verified': r['is_verified'] ?? 0,
          'total_earned': r['total_earned'] ?? 0.0,
          'available_balance': r['available_balance'] ?? 0.0,
          'monetization_approved': r['monetization_approved'] ?? 0,
          'monetization_status': r['monetization_status'],
          'kyc_status': r['kyc_status'],
          'recent_tracks': r['recent_tracks'] ?? [],
          'artist': r['artist'] ?? {},
          'is_suspended': r['is_suspended'] ?? 0,
          'suspend_reason': r['suspend_reason'] ?? '',
          'revenue_overview': r['revenue_overview'] ?? {},
        };
      }
    } catch (_) {
      return {
        'load_error': 'Could not load your artist dashboard. Please try again.'
      };
    }
    return {
      'load_error':
          'Your artist dashboard is unavailable. Please sign in again.'
    };
  }

  /// Returns a String token on success, or a Map {'suspended':true,'reason':...} if suspended.
  Future<dynamic> generatePortalToken() async {
    try {
      Response response = await dio.post(
        '${baseurl}generate_portal_token',
        data: FormData.fromMap({'user_id': Constant.userID}),
        options: optHeaders,
      );
      final data = response.data;
      if (data['status'] == 200) return data['token']?.toString();
      if (data['status'] == 423) {
        return {'suspended': true, 'reason': data['suspend_reason'] ?? ''};
      }
    } catch (_) {}
    return null;
  }

  /* =========================== Support Ticket API Start =========================== */

  Future<SuccessModel> submitSupportTicket(
    dynamic userId, {
    required String type,
    required String subject,
    required String message,
  }) async {
    SuccessModel successModel;
    String apiname = "support/submit";
    Response response = await dio.post(
      '$baseurl$apiname',
      data: FormData.fromMap({
        'user_id': userId,
        'type': type,
        'subject': subject,
        'message': message,
      }),
      options: optHeaders,
    );
    successModel = SuccessModel.fromJson(response.data);
    return successModel;
  }

  Future<SupportTicketListModel> getSupportTickets(
    dynamic userId, {
    int page = 1,
  }) async {
    SupportTicketListModel supportTicketListModel;
    String apiname = "support/tickets";
    Response response = await dio.post(
      '$baseurl$apiname',
      data: FormData.fromMap({
        'user_id': userId,
        'page': page,
      }),
      options: optHeaders,
    );
    supportTicketListModel = SupportTicketListModel.fromJson(response.data);
    return supportTicketListModel;
  }

  /* =========================== Support Ticket API End =========================== */
}
