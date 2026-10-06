import 'dart:async';
import 'dart:developer';
import 'package:audio_video_progress_bar/audio_video_progress_bar.dart';
import 'package:jailaoi/music/downloadsevice.dart';
import 'package:jailaoi/utils/downloadsong.dart';
import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:iconify_flutter/iconify_flutter.dart';
import 'package:iconify_flutter/icons/cil.dart';
import 'package:iconify_flutter/icons/material_symbols.dart';
import 'package:iconify_flutter/icons/uil.dart';
import 'package:just_audio/just_audio.dart';
import 'package:just_audio_background/just_audio_background.dart';
import 'package:miniplayer/miniplayer.dart';
import 'package:provider/provider.dart';
import 'package:jailaoi/music/player_globals.dart';
import 'package:jailaoi/pages/login.dart';
import 'package:jailaoi/pages/radiobyartist.dart';
import 'package:jailaoi/webservice/apiservices.dart';
import 'package:jailaoi/provider/musicdetailprovider.dart';
import 'package:jailaoi/subscription/subscription.dart';
import 'package:jailaoi/utils/adhelper.dart';
import 'package:jailaoi/utils/color.dart';
import 'package:jailaoi/utils/constant.dart';
import 'package:jailaoi/music/musicmanager.dart';
import 'package:jailaoi/utils/dimens.dart';
import 'package:jailaoi/utils/utils.dart';
import 'package:jailaoi/widget/musicutils.dart';
import 'package:jailaoi/widget/myimage.dart';
import 'package:jailaoi/widget/mynetworkimg.dart';
import 'package:jailaoi/widget/mytext.dart';
import 'package:rxdart/rxdart.dart';
import 'package:text_scroll/text_scroll.dart';

final AudioPlayer audioPlayer = AudioPlayer();
late MusicManager musicManager;

Stream<PositionData> get positionDataStream {
  return Rx.combineLatest3<Duration, Duration, Duration?, PositionData>(
          audioPlayer.positionStream,
          audioPlayer.bufferedPositionStream,
          audioPlayer.durationStream,
          (position, bufferedPosition, duration) => PositionData(
              position, bufferedPosition, duration ?? Duration.zero))
      .asBroadcastStream();
}

final ValueNotifier<double> playerExpandProgress =
    ValueNotifier(playerMinHeight);

final MiniplayerController miniPlayerController = MiniplayerController();

class MusicDetails extends StatefulWidget {
  final bool ishomepage;
  const MusicDetails({super.key, required this.ishomepage});

  @override
  State<MusicDetails> createState() => _MusicDetailsState();
}

class _MusicDetailsState extends State<MusicDetails>
    with WidgetsBindingObserver {
  late MusicDetailProvider musicDetailProvider;
  final ScrollController _scrollController = ScrollController();
  final ScrollController _episodeScrollController = ScrollController();
  double get _episodeCardWidth =>
      MediaQuery.of(context).size.width * 0.70 + 2; // + separator spacing
  String? _lastScrolledEpisodeId;

  final commentController = TextEditingController();
  bool isPodcastDetailOpen = false;

  // Download state
  bool _isDownloading = false;
  double _downloadProgress = 0.0;

/* ADD USer Action  */

  DateTime? _playStartTime;
  int _timeSpentSeconds = 0;
  bool _isApiCalling = false;
  void _startTimer() {
    _playStartTime = DateTime.now();
  }

  int? _lastHandledIndex;
  DateTime? _lastApiCallTime;
  String? _lastTrackedContentId;

  Future<void> _stopAndSendUserAction({required int actionType}) async {
    final now = DateTime.now();

    if (_lastApiCallTime != null &&
        now.difference(_lastApiCallTime!).inMilliseconds < 1000) {
      log("API BLOCKED -> duplicate within 1s");
      return;
    }
    _lastApiCallTime = now;

    if (_isApiCalling) return;
    if (_playStartTime == null) return;

    final mediaItem =
        audioPlayer.sequenceState.currentSource?.tag as MediaItem?;

    if (mediaItem == null) return;
    if (_lastTrackedContentId == mediaItem.id) {
      return;
    }

    if (_isApiCalling) return;
    _isApiCalling = true;
    final diff = DateTime.now().difference(_playStartTime!);
    _timeSpentSeconds = diff.inSeconds;

    if (_timeSpentSeconds < 2) {
      _isApiCalling = false;
      return;
    }

    _lastTrackedContentId = mediaItem.id;

    int contentType = 1;
    final playType = mediaItem.displaySubtitle?.toLowerCase();

    if (playType == "podcast") {
      contentType = 2;
    } else if (playType == "music") {
      contentType = 8;
    }

    final totalDuration = audioPlayer.duration?.inSeconds ??
        ((mediaItem.extras?['duration'] ?? 0) ~/ 1000);

    await musicDetailProvider.addUserAction(
      contentType: contentType,
      contentId: mediaItem.id,
      actionType: actionType,
      timeSpend: _timeSpentSeconds,
      categoryId: mediaItem.extras?['category_id'] ??
          mediaItem.extras?['podcasts_id'] ??
          0,
      languageId: mediaItem.extras?['language_id'] ?? 0,
      cityId: mediaItem.extras?['city_id'] ?? 0,
      artistId: mediaItem.extras?['artist_id'] ??
          mediaItem.extras?['podcasts_id'] ??
          0,
      contentDuration: totalDuration,
    );

    _playStartTime = DateTime.now();
    _isApiCalling = false;
  }

  String? currentSongId;

  StreamSubscription<int?>? _currentIndexSubscription;

  @override
  void initState() {
    super.initState();

    musicDetailProvider =
        Provider.of<MusicDetailProvider>(context, listen: false);

    _currentIndexSubscription =
        audioPlayer.currentIndexStream.listen((index) async {
      if (index == null) return;

      if (_lastHandledIndex != null && _lastHandledIndex != index) {
        log("REAL SONG CHANGE -> $index");

        await _stopAndSendUserAction(actionType: 1);
        if (!mounted) return;
      }

      if (_lastHandledIndex != index) {
        _lastHandledIndex = index;

        final mediaItem =
            audioPlayer.sequenceState.currentSource?.tag as MediaItem?;

        currentSongId = mediaItem?.id;

        _startTimer();

        // Track play count so admin "Popular" sections update correctly.
        // add_play types: 1=Song, 2=Podcast, 3=Music (not 8 — that's add_user_action).
        if (mediaItem != null &&
            Constant.userID != null &&
            Constant.userID != '0') {
          final sub = mediaItem.displaySubtitle?.toLowerCase() ?? '';
          final int playType = sub == 'podcast'
              ? 2
              : sub == 'music'
                  ? 3
                  : 1;
          ApiService().addPlay(mediaItem.id, playType);
        }
      }
    });

    WidgetsBinding.instance.addObserver(this);
  }

  @override
  void dispose() {
    _currentIndexSubscription?.cancel();
    commentController.dispose();
    ambiguate(WidgetsBinding.instance)?.removeObserver(this);
    super.dispose();
  }

  @override
  void didChangeAppLifecycleState(AppLifecycleState state) {
    printLog("didChangeAppLifecycleState ====================> $state.");
    if (state == AppLifecycleState.paused) {
      // App went to background — just_audio + foreground notification handle
      // playback continuation. We only log so we can track it.
      printLog("App paused — audio may continue via foreground service.");
    } else if (state == AppLifecycleState.resumed) {
      // App came back to foreground — refresh UI state if needed.
      if (mounted) setState(() {});
    } else if (state == AppLifecycleState.detached) {
      // App is being destroyed — clean up audio resources.
      audioPlayer.stop();
      currentlyPlaying.value = null;
    }
  }

  Future<void> _checkPremiumPlayPause() async {
    if ((audioPlayer.sequenceState.currentSource?.tag as MediaItem?)
                ?.extras?['is_premium'] ==
            1 &&
        (audioPlayer.sequenceState.currentSource?.tag as MediaItem?)
                ?.extras?['is_buy'] ==
            0) {
      AdHelper.showFullscreenAd(context, Constant.interstialAdType, () {
        Navigator.push(
          context,
          MaterialPageRoute(
            builder: (context) {
              return const Subscription(openFrom: '');
            },
          ),
        );
      });
    } else {
      printLog("Play/Pause click");
      if (audioPlayer.playing) {
        audioPlayer.pause();
        printLog("Pause");
      } else {
        audioPlayer.play();
        printLog("Play");
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return Miniplayer(
      valueNotifier: playerExpandProgress,
      minHeight: playerMinHeight,
      duration: const Duration(seconds: 1),
      maxHeight: MediaQuery.of(context).size.height,
      controller: miniPlayerController,
      elevation: 4,
      // backgroundColor: brandGreen,
      onDismissed: () async {
        await _stopAndSendUserAction(actionType: 1);
        printLog("onDismissed");
        await musicManager.clearMusicPlayer();
        if (mounted) {
          setState(() {});
        }
        if (!musicManager.isPodcastDetailOpen) {
          musicDetailProvider.clearProvider();
        }
      },
      curve: Curves.easeInOutCubicEmphasized,
      builder: (height, percentage) {
        final bool miniplayer = percentage < miniplayerPercentageDeclaration;

        if (!miniplayer) {
          return Container(
            decoration: BoxDecoration(
              gradient: LinearGradient(
                begin: Alignment.topLeft,
                end: Alignment.bottomRight,
                colors: [Color(0xFF5E5A4F), Color(0xFF35322D), black, black],
              ),
            ),
            child: Scaffold(
              backgroundColor: transparent,
              body: StreamBuilder<SequenceState?>(
                  stream: audioPlayer.sequenceStateStream,
                  builder: (context, snapshot) {
                    return NotificationListener<ScrollNotification>(
                      onNotification: (notification) {
                        if (_scrollController.offset >=
                                _scrollController.position.maxScrollExtent &&
                            !_scrollController.position.outOfRange &&
                            (musicDetailProvider.currentPage ?? 0) <
                                (musicDetailProvider.totalPage ?? 0)) {
                          musicDetailProvider.setLoadMore(true);
                          _fetchEpisodeByPodcast(
                              ((audioPlayer.sequenceState.currentSource?.tag
                                          as MediaItem?)
                                      ?.artist)
                                  .toString(),
                              musicDetailProvider.currentPage ?? 0);
                        }
                        return true;
                      },
                      child: SingleChildScrollView(
                        controller: _scrollController,
                        child: Column(
                          children: [
                            buildPodcastAppBar(),
                            buildPodcastMusicPage(),
                          ],
                        ),
                      ),
                    );
                  }),
            ),
          );
        }

        //Miniplayer in BuildMethod
        final percentageMiniplayer = percentageFromValueInRange(
            min: playerMinHeight,
            max: MediaQuery.of(context).size.height,
            value: height);

        final elementOpacity = 1 - 1 * percentageMiniplayer;
        final progressIndicatorHeight = 2 - 2 * percentageMiniplayer;
        // MiniPlayer End

        // Scaffold
        return Scaffold(
          body:
              buildMusicPanel(height, elementOpacity, progressIndicatorHeight),
        );
      },
    );
  }

  // MiniPlayer AppBar
  Widget buildPodcastAppBar() {
    return Column(
      children: [
        Column(
          children: [
            AppBar(
              backgroundColor: transparent,
              surfaceTintColor: transparent,
              elevation: 0,
              titleSpacing: 0,
              automaticallyImplyLeading: false,
              leading: RotatedBox(
                  quarterTurns: 4,
                  child: Icon(
                    Icons.keyboard_arrow_down_sharp,
                    color: white,
                    size: 30,
                  )),
              title: MyText(
                color: white,
                text: ((audioPlayer.sequenceState.currentSource?.tag
                            as MediaItem?)
                        ?.title)
                    .toString(),
                textalign: TextAlign.center,
                fontsize: Dimens.textBig,
                inter: 1,
                maxline: 1,
                fontwaight: FontWeight.w500,
                overflow: TextOverflow.ellipsis,
                fontstyle: FontStyle.normal,
              ),
              centerTitle: true,
            ),
            SizedBox(
              height: 30,
            ),
            StreamBuilder<SequenceState?>(
              stream: audioPlayer.sequenceStateStream,
              builder: (context, snapshot) {
                return ClipRRect(
                  borderRadius: BorderRadius.circular(15),
                  child: MyNetworkImage(
                    imgWidth: MediaQuery.sizeOf(context).height * 0.40,
                    imgHeight: MediaQuery.sizeOf(context).height * 0.40,
                    imageUrl: ((audioPlayer.sequenceState.currentSource?.tag
                                as MediaItem?)
                            ?.artUri)
                        .toString(),
                    fit: BoxFit.cover,
                  ),
                );
              },
            ),
          ],
        ),
      ],
    );
  }

  // FullPage MiniPlayer Screen Open Using This Method
  Widget buildPodcastMusicPage() {
    return StreamBuilder<SequenceState?>(
      stream: audioPlayer.sequenceStateStream,
      builder: (context, snapshot) {
        // if ((audioPlayer.sequenceState.currentSource?.tag as MediaItem?)
        //             ?.extras?['is_premium'] ==
        //         1 &&
        //     (audioPlayer.sequenceState.currentSource?.tag as MediaItem?)
        //             ?.extras?['is_buy'] ==
        //         0) {
        //   audioPlayer.pause();
        // } else {
        //   audioPlayer.play();
        // }
        return Padding(
          padding: const EdgeInsets.symmetric(horizontal: 16),
          child: Container(
            alignment: Alignment.center,
            child: SingleChildScrollView(
              physics: const NeverScrollableScrollPhysics(),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.center,
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  const SizedBox(height: 10),
                  StreamBuilder<SequenceState?>(
                    stream: audioPlayer.sequenceStateStream,
                    builder: (context, snapshot) {
                      return Padding(
                        padding: const EdgeInsets.fromLTRB(16, 10, 16, 0),
                        child: Align(
                          alignment: Alignment.centerLeft,
                          child: TextScroll(
                            intervalSpaces: 10,
                            mode: TextScrollMode.endless,
                            ((audioPlayer.sequenceState.currentSource?.tag
                                        as MediaItem?)
                                    ?.title)
                                .toString(),
                            selectable: true,
                            delayBefore: const Duration(milliseconds: 500),
                            fadedBorder: true,
                            style: Utils.googleFontStyle(1, 18,
                                FontStyle.normal, white, FontWeight.w600),
                            fadeBorderVisibility: FadeBorderVisibility.auto,
                            fadeBorderSide: FadeBorderSide.both,
                            velocity:
                                const Velocity(pixelsPerSecond: Offset(50, 0)),
                          ),
                        ),
                      );
                    },
                  ),
                  StreamBuilder<SequenceState?>(
                    stream: audioPlayer.sequenceStateStream,
                    builder: (context, snapshot) {
                      final tag = audioPlayer.sequenceState.currentSource?.tag
                          as MediaItem?;
                      final artistId =
                          tag?.extras?['artist_id']?.toString() ?? '';
                      final artistName = tag?.artist ?? '';
                      final artistImage =
                          tag?.extras?['artist_image']?.toString() ?? '';
                      return GestureDetector(
                        onTap: artistId.isNotEmpty
                            ? () => Navigator.push(
                                  context,
                                  MaterialPageRoute(
                                    builder: (_) => Radiobyartist(
                                      itemId: artistId,
                                      viewType: 'artist',
                                      title: artistName,
                                      languagegId: '',
                                      image: artistImage,
                                      bio: '',
                                      type: 0,
                                    ),
                                  ),
                                )
                            : null,
                        child: Padding(
                          padding: const EdgeInsets.fromLTRB(16, 5, 16, 0),
                          child: Align(
                            alignment: Alignment.centerLeft,
                            child: Row(
                              mainAxisSize: MainAxisSize.min,
                              children: [
                                Flexible(
                                  child: TextScroll(
                                    intervalSpaces: 10,
                                    mode: TextScrollMode.endless,
                                    artistName,
                                    selectable: true,
                                    delayBefore:
                                        const Duration(milliseconds: 500),
                                    fadedBorder: true,
                                    style: Utils.googleFontStyle(
                                        1,
                                        15,
                                        FontStyle.normal,
                                        artistId.isNotEmpty
                                            ? white
                                            : white.withValues(alpha: 0.7),
                                        FontWeight.w400),
                                    fadeBorderVisibility:
                                        FadeBorderVisibility.auto,
                                    fadeBorderSide: FadeBorderSide.both,
                                    velocity: const Velocity(
                                        pixelsPerSecond: Offset(50, 0)),
                                  ),
                                ),
                                if (artistId.isNotEmpty) ...[
                                  const SizedBox(width: 4),
                                  Icon(Icons.chevron_right_rounded,
                                      color: white.withValues(alpha: 0.5),
                                      size: 16),
                                ],
                              ],
                            ),
                          ),
                        ),
                      );
                    },
                  ),
                  const SizedBox(height: 15),
                  // StreamBuilder<SequenceState?>(
                  //     stream: audioPlayer.sequenceStateStream,
                  //     builder: (context, snapshot) {
                  //       return ((audioPlayer.sequenceState.currentSource?.tag
                  //                           as MediaItem?)
                  //                       ?.displaySubtitle)
                  //                   .toString() ==
                  //               "podcast"
                  //           ? SizedBox(
                  //               width: MediaQuery.of(context).size.width,
                  //               // color: colorAccent,
                  //               child: SingleChildScrollView(
                  //                 padding:
                  //                     const EdgeInsets.fromLTRB(20, 0, 20, 0),
                  //                 physics: const BouncingScrollPhysics(),
                  //                 scrollDirection: Axis.horizontal,
                  //                 child: Row(
                  //                   mainAxisAlignment: MainAxisAlignment.start,
                  //                   crossAxisAlignment:
                  //                       CrossAxisAlignment.start,
                  //                   children: [
                  //                     InkWell(
                  //                       onTap: () {
                  //                         musicDetailProvider.getCommentList(
                  //                             "2",
                  //                             ((audioPlayer
                  //                                         .sequenceState
                  //                                         .currentSource
                  //                                         ?.tag as MediaItem?)
                  //                                     ?.artist)
                  //                                 .toString(),
                  //                             ((audioPlayer
                  //                                         .sequenceState
                  //                                         .currentSource
                  //                                         ?.tag as MediaItem?)
                  //                                     ?.id)
                  //                                 .toString(),
                  //                             "1");
                  //                         commentBottomSheet(
                  //                           index: 0,
                  //                           podcastId: ((audioPlayer
                  //                                       .sequenceState
                  //                                       .currentSource
                  //                                       ?.tag as MediaItem?)
                  //                                   ?.artist)
                  //                               .toString(),
                  //                           episodeId: ((audioPlayer
                  //                                       .sequenceState
                  //                                       .currentSource
                  //                                       ?.tag as MediaItem?)
                  //                                   ?.id)
                  //                               .toString(),
                  //                         );
                  //                       },
                  //                       child: Container(
                  //                         padding: const EdgeInsets.fromLTRB(
                  //                             15, 8, 15, 8),
                  //                         decoration: BoxDecoration(
                  //                           borderRadius:
                  //                               BorderRadius.circular(20),
                  //                           color: brandGreen.withValues(
                  //                               alpha: 0.25),
                  //                         ),
                  //                         child: Row(
                  //                           children: [
                  //                             MyImage(
                  //                               width: 18,
                  //                               height: 18,
                  //                               imagePath: "ic_comment.png",
                  //                               color: Theme.of(context)
                  //                                   .colorScheme
                  //                                   .surface,
                  //                             ),
                  //                             const SizedBox(width: 8),
                  //                             MyText(
                  //                                 color: white,
                  //                                 text: Utils.kmbGenerator(
                  //                                     int.parse(((audioPlayer
                  //                                                 .sequenceState
                  //                                                 .currentSource
                  //                                                 ?.tag as MediaItem?)
                  //                                             ?.extras?['total_comment'])
                  //                                         .toString())),
                  //                                 multilanguage: false,
                  //                                 textalign: TextAlign.center,
                  //                                 fontsize: Dimens.textTitle,
                  //                                 maxline: 1,
                  //                                 fontwaight: FontWeight.w500,
                  //                                 overflow: TextOverflow.ellipsis,
                  //                                 fontstyle: FontStyle.normal),
                  //                           ],
                  //                         ),
                  //                       ),
                  //                     ),
                  //                     const SizedBox(width: 10),
                  //                     InkWell(
                  //                       onTap: () {
                  //                         Utils.shareApp(Platform.isIOS
                  //                             ? "Hey! I'm Listening ${(audioPlayer.sequenceState.currentSource?.tag as MediaItem?)?.title}. Check it out now on ${Constant.appName}! \nhttps://apps.apple.com/us/app/${Constant.appName.toLowerCase()}/${Constant.appPackageName} \n"
                  //                             : "Hey! I'm Listening ${(audioPlayer.sequenceState.currentSource?.tag as MediaItem?)?.title}. Check it out now on ${Constant.appName}! \nhttps://play.google.com/store/apps/details?id=${Constant.appPackageName} \n");
                  //                       },
                  //                       child: Container(
                  //                         padding: const EdgeInsets.fromLTRB(
                  //                             15, 8, 15, 8),
                  //                         decoration: BoxDecoration(
                  //                           borderRadius:
                  //                               BorderRadius.circular(20),
                  //                           color: brandGreen.withValues(
                  //                               alpha: 0.25),
                  //                         ),
                  //                         child: Row(
                  //                           children: [
                  //                             MyImage(
                  //                               width: 18,
                  //                               height: 18,
                  //                               imagePath: "ic_sharemusic.png",
                  //                               color: Theme.of(context)
                  //                                   .colorScheme
                  //                                   .surface,
                  //                             ),
                  //                             const SizedBox(width: 8),
                  //                             MyText(
                  //                                 color: white,
                  //                                 text: "share",
                  //                                 multilanguage: true,
                  //                                 textalign: TextAlign.center,
                  //                                 fontsize: Dimens.textTitle,
                  //                                 maxline: 6,
                  //                                 fontwaight: FontWeight.w600,
                  //                                 overflow:
                  //                                     TextOverflow.ellipsis,
                  //                                 fontstyle: FontStyle.normal),
                  //                           ],
                  //                         ),
                  //                       ),
                  //                     ),
                  //                   ],
                  //                 ),
                  //               ),
                  //             )
                  //           : const SizedBox.shrink();
                  //     }),
                  Container(
                    margin: const EdgeInsets.fromLTRB(15, 20, 15, 15),
                    child: StreamBuilder<PositionData>(
                      stream: positionDataStream,
                      builder: (context, snapshot) {
                        final positionData = snapshot.data;
                        return ProgressBar(
                          progress: positionData?.position ?? Duration.zero,
                          buffered:
                              positionData?.bufferedPosition ?? Duration.zero,
                          total: positionData?.duration ?? Duration.zero,
                          progressBarColor: white,
                          baseBarColor: lightgray,
                          bufferedBarColor: gray,
                          thumbColor: white,
                          barHeight: 4.0,
                          thumbRadius: 6.0,
                          timeLabelPadding: 5.0,
                          timeLabelType: TimeLabelType.totalTime,
                          timeLabelTextStyle: GoogleFonts.inter(
                            fontSize: 12,
                            fontStyle: FontStyle.normal,
                            color: gray,
                            fontWeight: FontWeight.w700,
                          ),
                          onSeek: (duration) {
                            audioPlayer.seek(duration);
                          },
                        );
                      },
                    ),
                  ),
                  Row(
                    // mainAxisSize: MainAxisSize.min,
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      // Privious Audio Play
                      // StreamBuilder<bool>(
                      //   stream: audioPlayer.shuffleModeEnabledStream,
                      //   builder: (context, snapshot) {
                      //     final shuffleModeEnabled = snapshot.data ?? false;
                      //     return IconButton(
                      //       iconSize: 50.0,
                      //       icon: shuffleModeEnabled
                      //           ? const Iconify(
                      //               Ph.shuffle_bold,
                      //               color: brandGreen,
                      //               size: 30,
                      //             )
                      //           : Iconify(
                      //               Ph.shuffle_bold,
                      //               color: white,
                      //               size: 30,
                      //             ),
                      //       onPressed: () async {
                      //         final enable = !shuffleModeEnabled;
                      //         if (enable) {
                      //           await audioPlayer.shuffle();
                      //         }
                      //         await audioPlayer.setShuffleModeEnabled(enable);
                      //       },
                      //     );
                      //   },
                      // ),
                      Expanded(
                        flex: 1,
                        child: Align(
                          alignment: Alignment.center,
                          child: StreamBuilder<double>(
                            stream: audioPlayer.speedStream,
                            builder: (context, snapshot) => IconButton(
                              icon: Text(
                                overflow: TextOverflow.ellipsis,
                                "${snapshot.data?.toStringAsFixed(1)}x",
                                style: TextStyle(
                                    fontWeight: FontWeight.bold,
                                    color: white,
                                    fontSize: 14),
                              ),
                              onPressed: () {
                                showSliderDialog(
                                  context: context,
                                  title: "Adjust speed",
                                  divisions: 10,
                                  min: 0.5,
                                  max: 2.0,
                                  value: audioPlayer.speed,
                                  stream: audioPlayer.speedStream,
                                  onChanged: audioPlayer.setSpeed,
                                );
                              },
                            ),
                          ),
                        ),
                      ),

                      Expanded(
                        flex: 1,
                        child: Align(
                          alignment: Alignment.center,
                          child: StreamBuilder<SequenceState?>(
                            stream: audioPlayer.sequenceStateStream,
                            builder: (context, snapshot) => InkWell(
                              onTap: audioPlayer.hasPrevious
                                  ? audioPlayer.seekToPrevious
                                  : null,
                              child: Padding(
                                padding: const EdgeInsets.all(10),
                                child: Opacity(
                                    opacity:
                                        audioPlayer.hasPrevious ? 1.0 : 0.5,
                                    child: Iconify(
                                      MaterialSymbols.skip_previous,
                                      color: white,
                                      size: 40,
                                    )),
                              ),
                            ),
                          ),
                        ),
                      ),

                      // 10-second rewind hidden — short music doesn't need it
                      const SizedBox.shrink(),
                      const SizedBox(width: 15),
                      // Pause and Play Control
                      Expanded(
                        flex: 1,
                        child: Align(
                          alignment: Alignment.center,
                          child: StreamBuilder<PlayerState>(
                            stream: audioPlayer.playerStateStream,
                            builder: (context, snapshot) {
                              final playerState = snapshot.data;
                              final processingState =
                                  playerState?.processingState;
                              final playing = playerState?.playing;

                              if (processingState == ProcessingState.loading ||
                                  processingState ==
                                      ProcessingState.buffering) {
                                return Container(
                                  alignment: Alignment.center,
                                  height: 50,
                                  width: 50,
                                  decoration: const BoxDecoration(
                                      color: white, shape: BoxShape.circle),
                                  child: const SizedBox(
                                    width: 26,
                                    height: 26,
                                    child: CircularProgressIndicator(
                                      strokeWidth: 2.5,
                                      color: colorAccent,
                                    ),
                                  ),
                                );
                              } else if (playing != true) {
                                return Container(
                                  alignment: Alignment.center,
                                  height: 50,
                                  width: 50,
                                  decoration: BoxDecoration(
                                      color: white, shape: BoxShape.circle),
                                  child: IconButton(
                                    icon: const Icon(Icons.play_arrow_rounded,
                                        color: black),
                                    iconSize: 28.0,
                                    onPressed: () async {
                                      await _checkPremiumPlayPause();
                                      // **sync row buttons instantly**
                                      isPlayingNotifier.value =
                                          audioPlayer.playing;
                                      currentlyPlayingId = currentlyPlayingId;
                                    },
                                  ),
                                );
                              } else if (processingState !=
                                  ProcessingState.completed) {
                                return Container(
                                  alignment: Alignment.center,
                                  height: 50,
                                  width: 50,
                                  decoration: BoxDecoration(
                                      color: white, shape: BoxShape.circle),
                                  child: IconButton(
                                    icon: const Icon(Icons.pause_rounded,
                                        color: black),
                                    iconSize: 28.0,
                                    onPressed: () async {
                                      await _stopAndSendUserAction(
                                          actionType: 1);
                                      await _checkPremiumPlayPause();
                                      // **sync row buttons instantly**
                                      isPlayingNotifier.value =
                                          audioPlayer.playing;
                                    },
                                  ),
                                );
                              } else {
                                return Container(
                                  alignment: Alignment.center,
                                  height: 50,
                                  width: 50,
                                  decoration: BoxDecoration(
                                      color: white, shape: BoxShape.circle),
                                  child: IconButton(
                                    icon: const Icon(Icons.replay_rounded,
                                        color: black),
                                    iconSize: 28.0,
                                    onPressed: () => audioPlayer.seek(
                                      Duration.zero,
                                      index: audioPlayer.effectiveIndices.first,
                                    ),
                                  ),
                                );
                              }
                            },
                          ),
                        ),
                      ),
                      const SizedBox(width: 15),
                      // 10-second fast-forward hidden — short music doesn't need it
                      const SizedBox.shrink(),
                      // const SizedBox(width: 15),
                      // // Next Audio Play
                      Expanded(
                        flex: 1,
                        child: Align(
                          alignment: Alignment.center,
                          child: StreamBuilder<SequenceState?>(
                            stream: audioPlayer.sequenceStateStream,
                            builder: (context, snapshot) {
                              return InkWell(
                                onTap: audioPlayer.hasNext
                                    ? () async {
                                        await _stopAndSendUserAction(
                                            actionType: 1); // ⭐ FIRST

                                        await audioPlayer
                                            .seekToNext(); // ⭐ THEN NEXT
                                      }
                                    : null,
                                child: Padding(
                                  padding: const EdgeInsets.all(10),
                                  child: Opacity(
                                      opacity: audioPlayer.hasNext ? 1.0 : 0.5,
                                      child: Iconify(
                                        MaterialSymbols.skip_next,
                                        color: white,
                                        size: 40,
                                      )),
                                ),
                              );
                            },
                          ),
                        ),
                      ),

                      Expanded(
                        flex: 1,
                        child: Align(
                          alignment: Alignment.center,
                          child: StreamBuilder<LoopMode>(
                            stream: audioPlayer.loopModeStream,
                            builder: (context, snapshot) {
                              final loopMode = snapshot.data ?? LoopMode.off;
                              final icons = [
                                Iconify(Cil.loop, color: white, size: 30.0),
                                const Iconify(Cil.loop,
                                    color: brandGreen, size: 30.0),
                                const Iconify(Cil.loop_1,
                                    color: brandGreen, size: 30.0),
                              ];
                              const cycleModes = [
                                LoopMode.off,
                                LoopMode.all,
                                LoopMode.one,
                              ];
                              final index = cycleModes.indexOf(loopMode);
                              return IconButton(
                                icon: icons[index],
                                onPressed: () {
                                  audioPlayer.setLoopMode(cycleModes[
                                      (cycleModes.indexOf(loopMode) + 1) %
                                          cycleModes.length]);
                                },
                              );
                            },
                          ),
                        ),
                      ),

                      // ── Shuffle Button ──
                      Expanded(
                        flex: 1,
                        child: Align(
                          alignment: Alignment.center,
                          child: StreamBuilder<bool>(
                            stream: audioPlayer.shuffleModeEnabledStream,
                            builder: (context, snapshot) {
                              final enabled = snapshot.data ?? false;
                              return IconButton(
                                icon: Icon(
                                  Icons.shuffle_rounded,
                                  color: enabled ? brandGreen : white,
                                  size: 26,
                                ),
                                onPressed: () async {
                                  final enable = !enabled;
                                  if (enable) {
                                    await audioPlayer.shuffle();
                                  }
                                  await audioPlayer
                                      .setShuffleModeEnabled(enable);
                                },
                              );
                            },
                          ),
                        ),
                      ),
                    ],
                  ),

                  SizedBox(
                    height: 20,
                  ),
                  StreamBuilder<SequenceState?>(
                      stream: audioPlayer.sequenceStateStream,
                      builder: (context, snapshot) {
                        final tagForLike = audioPlayer
                            .sequenceState.currentSource?.tag as MediaItem?;
                        return SizedBox(
                          width: MediaQuery.of(context).size.width,
                          // color: colorAccent,
                          child: SingleChildScrollView(
                            scrollDirection: Axis.horizontal,
                            physics: const BouncingScrollPhysics(),
                            child: Row(
                              mainAxisAlignment: MainAxisAlignment.start,
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                // ── Like / Favorite Button ──
                                _buildPlayerLikeButton(tagForLike),
                                const SizedBox(width: 10),
                                // InkWell(
                                //   onTap: () {
                                //     musicDetailProvider.getCommentList(
                                //         "2",
                                //         ((audioPlayer
                                //                     .sequenceState
                                //                     .currentSource
                                //                     ?.tag as MediaItem?)
                                //                 ?.artist)
                                //             .toString(),
                                //         ((audioPlayer
                                //                     .sequenceState
                                //                     .currentSource
                                //                     ?.tag as MediaItem?)
                                //                 ?.id)
                                //             .toString(),
                                //         "1");
                                //     commentBottomSheet(
                                //       index: 0,
                                //       podcastId: ((audioPlayer
                                //                   .sequenceState
                                //                   .currentSource
                                //                   ?.tag as MediaItem?)
                                //               ?.artist)
                                //           .toString(),
                                //       episodeId: ((audioPlayer
                                //                   .sequenceState
                                //                   .currentSource
                                //                   ?.tag as MediaItem?)
                                //               ?.id)
                                //           .toString(),
                                //     );
                                //   },
                                //   child: Container(
                                //     padding: const EdgeInsets.fromLTRB(
                                //         15, 8, 15, 8),
                                //     decoration: BoxDecoration(
                                //       borderRadius:
                                //           BorderRadius.circular(20),
                                //       // color: brandGreen.withValues(
                                //       //     alpha: 0.25),
                                //     ),
                                //     child: Row(
                                //       children: [
                                //         MyImage(
                                //           width: 18,
                                //           height: 18,
                                //           imagePath: "ic_comment.png",
                                //           color: Theme.of(context)
                                //               .colorScheme
                                //               .surface,
                                //         ),
                                //         const SizedBox(width: 8),
                                //         MyText(
                                //             color: white,
                                //             text: Utils.kmbGenerator(
                                //                 int.parse(((audioPlayer
                                //                             .sequenceState
                                //                             .currentSource
                                //                             ?.tag as MediaItem?)
                                //                         ?.extras?['total_comment'])
                                //                     .toString())),
                                //             multilanguage: false,
                                //             textalign: TextAlign.center,
                                //             fontsize: Dimens.textTitle,
                                //             maxline: 1,
                                //             fontwaight: FontWeight.w500,
                                //             overflow: TextOverflow.ellipsis,
                                //             fontstyle: FontStyle.normal),
                                //       ],
                                //     ),
                                //   ),
                                // ),

                                const SizedBox(width: 10),
                                // ── Story Card Share ──
                                InkWell(
                                  onTap: () {
                                    // JAILAOI: brand-tagged share for viral growth.
                                    // Include a deep link so tapping it opens the
                                    // song straight in the app (or a web landing
                                    // page with an "Open in app" button).
                                    final tag = audioPlayer.sequenceState
                                        .currentSource?.tag as MediaItem?;
                                    final songId = tag?.id ?? "";
                                    final link = songId.isNotEmpty
                                        ? '\n\nhttps://www.jailaoi.com/music/$songId'
                                        : '';
                                    Utils.shareApp(
                                      '🎵 Now playing on JailaOi\n'
                                      '${tag?.title ?? ""}\n'
                                      '— ${tag?.artist ?? ""}\n\n'
                                      'Listen on JailaOi — Northeast India\'s music app.'
                                      '$link',
                                    );
                                  },
                                  child: Container(
                                    padding:
                                        const EdgeInsets.fromLTRB(12, 8, 12, 8),
                                    decoration: BoxDecoration(
                                      borderRadius: BorderRadius.circular(20),
                                      color: brandGreen.withValues(alpha: 0.15),
                                    ),
                                    child: Row(
                                      mainAxisSize: MainAxisSize.min,
                                      children: [
                                        Iconify(
                                          Uil.share_alt,
                                          color: brandGreen,
                                          size: 22,
                                        ),
                                        const SizedBox(width: 6),
                                        MyText(
                                          color: brandGreen,
                                          text: "Story",
                                          fontsize: 11,
                                          fontwaight: FontWeight.w600,
                                          fontstyle: FontStyle.normal,
                                        ),
                                      ],
                                    ),
                                  ),
                                ),
                                const SizedBox(width: 10),
                                // ── Lyrics Button ──
                                InkWell(
                                  onTap: () {
                                    final tag = audioPlayer.sequenceState
                                        .currentSource?.tag as MediaItem?;
                                    final lyricsText =
                                        tag?.extras?['lyrics']?.toString() ??
                                            '';
                                    if (lyricsText.isEmpty) {
                                      Utils.showToast(
                                          'No lyrics available for this track');
                                      return;
                                    }
                                    showModalBottomSheet(
                                      context: context,
                                      isScrollControlled: true,
                                      backgroundColor: Colors.transparent,
                                      builder: (_) => DraggableScrollableSheet(
                                        initialChildSize: 0.6,
                                        maxChildSize: 0.92,
                                        minChildSize: 0.3,
                                        builder: (_, scrollController) =>
                                            Container(
                                          decoration: BoxDecoration(
                                            color: Theme.of(context).cardColor,
                                            borderRadius:
                                                const BorderRadius.vertical(
                                                    top: Radius.circular(20)),
                                          ),
                                          child: Column(
                                            children: [
                                              const SizedBox(height: 12),
                                              Container(
                                                width: 40,
                                                height: 4,
                                                decoration: BoxDecoration(
                                                  color: Colors.grey
                                                      .withValues(alpha: 0.4),
                                                  borderRadius:
                                                      BorderRadius.circular(2),
                                                ),
                                              ),
                                              const SizedBox(height: 16),
                                              Text(
                                                'Lyrics',
                                                style: TextStyle(
                                                  fontSize: 18,
                                                  fontWeight: FontWeight.w700,
                                                  color: Theme.of(context)
                                                      .textTheme
                                                      .bodyLarge
                                                      ?.color,
                                                ),
                                              ),
                                              const SizedBox(height: 16),
                                              Expanded(
                                                child: SingleChildScrollView(
                                                  controller: scrollController,
                                                  padding: const EdgeInsets
                                                      .symmetric(
                                                      horizontal: 24,
                                                      vertical: 8),
                                                  child: Text(
                                                    lyricsText,
                                                    style: TextStyle(
                                                      fontSize: 15,
                                                      height: 1.8,
                                                      color: Theme.of(context)
                                                          .textTheme
                                                          .bodyMedium
                                                          ?.color,
                                                    ),
                                                  ),
                                                ),
                                              ),
                                              const SizedBox(height: 20),
                                            ],
                                          ),
                                        ),
                                      ),
                                    );
                                  },
                                  child: Container(
                                    padding:
                                        const EdgeInsets.fromLTRB(12, 8, 12, 8),
                                    decoration: BoxDecoration(
                                      borderRadius: BorderRadius.circular(20),
                                      color: riverTeal.withValues(alpha: 0.12),
                                    ),
                                    child: Row(
                                      mainAxisSize: MainAxisSize.min,
                                      children: [
                                        const Icon(
                                            Icons.mic_external_on_rounded,
                                            color: riverTeal,
                                            size: 20),
                                        const SizedBox(width: 6),
                                        MyText(
                                          color: riverTeal,
                                          text: "Lyrics",
                                          fontsize: 11,
                                          fontwaight: FontWeight.w600,
                                          fontstyle: FontStyle.normal,
                                        ),
                                      ],
                                    ),
                                  ),
                                ),
                                const SizedBox(width: 10),
                                // ── Download Button ──
                                StreamBuilder<SequenceState?>(
                                  stream: audioPlayer.sequenceStateStream,
                                  builder: (context, snapshot) {
                                    final tag = audioPlayer.sequenceState
                                        .currentSource?.tag as MediaItem?;
                                    final isBuy = tag?.extras?['is_buy'] == 1;
                                    final audioUrl =
                                        tag?.extras?['audio_url']?.toString() ??
                                            '';
                                    return InkWell(
                                      onTap: () async {
                                        if (!isBuy) {
                                          Navigator.push(
                                              context,
                                              MaterialPageRoute(
                                                  builder: (_) =>
                                                      const Subscription(
                                                          openFrom: '')));
                                          return;
                                        }
                                        if (_isDownloading || audioUrl.isEmpty)
                                          return;
                                        setState(() {
                                          _isDownloading = true;
                                          _downloadProgress = 0;
                                        });
                                        final path =
                                            await DownloadService.downloadSong(
                                          audioUrl,
                                          tag?.id ?? '',
                                          onProgress: (p) => setState(
                                              () => _downloadProgress = p),
                                        );
                                        if (path != null && tag != null) {
                                          await saveDownloadedSong(
                                            id: tag.id,
                                            title: tag.title,
                                            artist: tag.artist ?? '',
                                            image: tag.artUri?.toString() ?? '',
                                            path: path,
                                            type: tag.extras?['type'] ?? 8,
                                            categoryname: tag
                                                    .extras?['categoryname']
                                                    ?.toString() ??
                                                '',
                                            date: tag.extras?['date']
                                                    ?.toString() ??
                                                '',
                                            totalplay:
                                                tag.extras?['total_play'] ?? 0,
                                            duration:
                                                tag.extras?['duration'] ?? 0,
                                            artistid: tag.extras?['artist_id']
                                                    ?.toString() ??
                                                '',
                                            languagegname: tag
                                                    .extras?['language_name']
                                                    ?.toString() ??
                                                '',
                                            artistimage:
                                                tag.extras?['artist_images'] ??
                                                    [],
                                            isbuy: tag.extras?['is_buy'] ?? 0,
                                            premium:
                                                tag.extras?['is_premium'] ?? 0,
                                            url: audioUrl,
                                            position: 0,
                                            datalist: [],
                                            viewType: 'music',
                                          );
                                          Utils.showToast(
                                              'Downloaded successfully');
                                        } else {
                                          Utils.showToast('Download failed');
                                        }
                                        setState(() => _isDownloading = false);
                                      },
                                      child: Container(
                                        padding: const EdgeInsets.fromLTRB(
                                            12, 8, 12, 8),
                                        decoration: BoxDecoration(
                                          borderRadius:
                                              BorderRadius.circular(20),
                                          color: Colors.white
                                              .withValues(alpha: 0.10),
                                        ),
                                        child: Row(
                                          mainAxisSize: MainAxisSize.min,
                                          children: [
                                            _isDownloading
                                                ? SizedBox(
                                                    width: 18,
                                                    height: 18,
                                                    child:
                                                        CircularProgressIndicator(
                                                      value: _downloadProgress,
                                                      strokeWidth: 2,
                                                      color: white,
                                                    ),
                                                  )
                                                : Icon(
                                                    isBuy
                                                        ? Icons.download_rounded
                                                        : Icons.lock_rounded,
                                                    color: white,
                                                    size: 20,
                                                  ),
                                            const SizedBox(width: 6),
                                            MyText(
                                              color: white,
                                              text: _isDownloading
                                                  ? '${(_downloadProgress * 100).toInt()}%'
                                                  : 'Download',
                                              fontsize: 11,
                                              fontwaight: FontWeight.w600,
                                              fontstyle: FontStyle.normal,
                                            ),
                                          ],
                                        ),
                                      ),
                                    );
                                  },
                                ),
                              ],
                            ),
                          ),
                        );
                      }),

                  const SizedBox(height: 16),

                  // ── JAILAOI UX: Up Next peek card ──
                  StreamBuilder<SequenceState?>(
                    stream: audioPlayer.sequenceStateStream,
                    builder: (context, snapshot) {
                      final state = snapshot.data;
                      final hasNext = state?.effectiveSequence != null &&
                          (state!.currentIndex ?? 0) <
                              state.effectiveSequence.length - 1;
                      if (!hasNext) return const SizedBox.shrink();
                      final nextIndex = (state.currentIndex ?? 0) + 1;
                      final nextItem =
                          state.effectiveSequence[nextIndex].tag as MediaItem?;
                      return Container(
                        margin: const EdgeInsets.symmetric(horizontal: 16),
                        padding: const EdgeInsets.symmetric(
                            horizontal: 14, vertical: 12),
                        decoration: BoxDecoration(
                          color: warmWhite.withValues(alpha: 0.06),
                          borderRadius: BorderRadius.circular(14),
                          border: Border.all(
                              color: warmWhite.withValues(alpha: 0.06)),
                        ),
                        child: Row(
                          children: [
                            Icon(Icons.queue_music_rounded,
                                color: warmWhite.withValues(alpha: 0.35),
                                size: 18),
                            const SizedBox(width: 10),
                            MyText(
                              color: warmWhite.withValues(alpha: 0.45),
                              text: "Up next",
                              fontsize: 11,
                              fontwaight: FontWeight.w500,
                              fontstyle: FontStyle.normal,
                            ),
                            const SizedBox(width: 8),
                            Expanded(
                              child: MyText(
                                color: warmWhite.withValues(alpha: 0.8),
                                text: nextItem?.title ?? "",
                                fontsize: 12,
                                fontwaight: FontWeight.w600,
                                fontstyle: FontStyle.normal,
                                maxline: 1,
                                overflow: TextOverflow.ellipsis,
                              ),
                            ),
                          ],
                        ),
                      );
                    },
                  ),

                  const SizedBox(height: 20),
                  // Container(
                  //   width: MediaQuery.of(context).size.width,
                  //   padding: const EdgeInsets.fromLTRB(5, 5, 5, 5),
                  //   child: Row(
                  //     mainAxisAlignment: MainAxisAlignment.spaceEvenly,
                  //     crossAxisAlignment: CrossAxisAlignment.center,
                  //     children: [
                  //       // Volumn Costome Set
                  //       IconButton(
                  //         iconSize: 30.0,
                  //         icon: const Icon(Icons.volume_up),
                  //         color: white,
                  //         onPressed: () {
                  //           showSliderDialog(
                  //             context: context,
                  //             title: "Adjust volume",
                  //             divisions: 10,
                  //             min: 0.0,
                  //             max: 2.0,
                  //             value: audioPlayer.volume,
                  //             stream: audioPlayer.volumeStream,
                  //             onChanged: audioPlayer.setVolume,
                  //           );
                  //         },
                  //       ),
                  //       // Audio Speed Costomized
                  //       StreamBuilder<double>(
                  //         stream: audioPlayer.speedStream,
                  //         builder: (context, snapshot) => IconButton(
                  //           icon: Text(
                  //             overflow: TextOverflow.ellipsis,
                  //             "${snapshot.data?.toStringAsFixed(1)}x",
                  //             style: TextStyle(
                  //                 fontWeight: FontWeight.bold,
                  //                 color: white,
                  //                 fontSize: 14),
                  //           ),
                  //           onPressed: () {
                  //             showSliderDialog(
                  //               context: context,
                  //               title: "Adjust speed",
                  //               divisions: 10,
                  //               min: 0.5,
                  //               max: 2.0,
                  //               value: audioPlayer.speed,
                  //               stream: audioPlayer.speedStream,
                  //               onChanged: audioPlayer.setSpeed,
                  //             );
                  //           },
                  //         ),
                  //       ),
                  //       // Loop Node Button
                  //       // Suffle Button

                  //       // Favorite
                  //       // _buildLikeUnlike(),
                  //     ],
                  //   ),
                  // ),
                  // const SizedBox(height: 20),
                  /* Episode List */
                  if ((musicDetailProvider.episodeList?.length ?? 0) > 0 &&
                      ((audioPlayer.sequenceState.currentSource?.tag
                                      as MediaItem?)
                                  ?.displaySubtitle)
                              .toString() ==
                          "podcast")
                    Container(
                      width: MediaQuery.of(context).size.width,
                      decoration: BoxDecoration(
                        borderRadius: const BorderRadius.only(
                          topLeft: Radius.circular(18),
                          topRight: Radius.circular(18),
                        ),
                        // color: brandGreen.withValues(alpha: 0.25),
                      ),
                      child: Column(
                        children: [
                          podcastEpisodeDetail(),
                          const SizedBox(height: 20),
                          podcastEpisodeList(),
                          SizedBox(
                            height: 100,
                          ),
                        ],
                      ),
                    )
                  else
                    const SizedBox.shrink(),
                ],
              ),
            ),
          ),
        );
      },
    );
  }

  Widget podcastEpisodeList() {
    return Consumer<MusicDetailProvider>(
      builder: (context, musicdetailprovider, child) {
        if (musicdetailprovider.loading && !musicdetailprovider.loadMore) {
          return Utils.pageLoader();
        }

        if (musicdetailprovider.getEpisodeByPodcstModel.status != 200 ||
            musicdetailprovider.episodeList == null ||
            musicdetailprovider.episodeList!.isEmpty) {
          return const SizedBox.shrink();
        }

        return Container(
          height: 180,
          decoration: BoxDecoration(
            color: const Color(0xff333333),
            borderRadius: BorderRadius.circular(16),
          ),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              const Padding(
                padding: EdgeInsets.only(left: 20, bottom: 12, top: 12),
                child: MyText(
                  text: "In this episode",
                  color: white,
                  inter: 1,
                  fontsize: 18,
                  fontstyle: FontStyle.normal,
                  maxline: 1,
                  multilanguage: false,
                  overflow: TextOverflow.ellipsis,
                  fontwaight: FontWeight.w500,
                ),
              ),
              SizedBox(
                height: 100,
                child: ListView.separated(
                  controller: _episodeScrollController,
                  scrollDirection: Axis.horizontal,
                  padding: const EdgeInsets.symmetric(horizontal: 15),
                  physics: const BouncingScrollPhysics(),
                  itemCount: musicdetailprovider.episodeList!.length,
                  separatorBuilder: (context, index) =>
                      const SizedBox(width: 12),
                  itemBuilder: (context, index) {
                    final episode = musicdetailprovider.episodeList![index];

                    return StreamBuilder<SequenceState?>(
                      stream: audioPlayer.sequenceStateStream,
                      builder: (context, snapshot) {
                        final playingId = (audioPlayer
                                .sequenceState.currentSource?.tag as MediaItem?)
                            ?.id
                            .toString();
                        final isPlaying = playingId == episode.id.toString();
                        if (isPlaying &&
                            _lastScrolledEpisodeId != episode.id.toString()) {
                          _lastScrolledEpisodeId = episode.id.toString();

                          WidgetsBinding.instance.addPostFrameCallback((_) {
                            if (_episodeScrollController.hasClients) {
                              final targetOffset = index * _episodeCardWidth;

                              _episodeScrollController.animateTo(
                                targetOffset,
                                duration: const Duration(milliseconds: 350),
                                curve: Curves.easeInOut,
                              );
                            }
                          });
                        }

                        return InkWell(
                          onTap: () {
                            Utils
                                .playAudio(
                                    context,
                                    "podcast",
                                    0,
                                    0,
                                    musicdetailprovider.episodeList?.firstOrNull
                                            ?.landscapeImg
                                            .toString() ??
                                        "",
                                    musicdetailprovider.episodeList?.firstOrNull?.name
                                            .toString() ??
                                        "",
                                    '',
                                    musicdetailprovider.episodeList?.firstOrNull
                                            ?.episodeAudio
                                            .toString() ??
                                        "",
                                    "",
                                    musicdetailprovider.episodeList?.firstOrNull
                                            ?.description
                                            .toString() ??
                                        "",
                                    musicdetailprovider
                                            .episodeList?.firstOrNull?.id
                                            .toString() ??
                                        "",
                                    (audioPlayer.sequenceState.currentSource
                                            ?.tag as MediaItem?)
                                        ?.artist
                                        .toString() ?? '',
                                    index,
                                    musicdetailprovider.episodeList?.toList() ??
                                        []);
                          },
                          child: Container(
                            width: MediaQuery.of(context).size.width *
                                0.70, // Card ni width
                            padding: const EdgeInsets.all(12),
                            decoration: BoxDecoration(
                              // Image mujab Dark Grey background
                              color: const Color(0xff4A4A4A),
                              borderRadius: BorderRadius.circular(15),
                              border: Border.all(
                                color: isPlaying ? brandGreen : transparent,
                                width: 1,
                              ),
                            ),
                            child: Row(
                              children: [
                                // Episode Image
                                ClipRRect(
                                  borderRadius: BorderRadius.circular(10),
                                  child: MyNetworkImage(
                                    imgWidth: 80,
                                    imgHeight: 110,
                                    imageUrl: episode.portraitImg ?? "",
                                    fit: BoxFit.cover,
                                  ),
                                ),
                                const SizedBox(width: 15),
                                // Episode Details
                                Expanded(
                                  child: Column(
                                    mainAxisAlignment: MainAxisAlignment.center,
                                    crossAxisAlignment:
                                        CrossAxisAlignment.start,
                                    children: [
                                      MyText(
                                        color: white,
                                        text: episode.name ?? "",
                                        fontsize: 15,
                                        maxline: 1,
                                        fontstyle: FontStyle.normal,
                                        inter: 1,
                                        multilanguage: false,
                                        fontwaight: FontWeight.w600,
                                        overflow: TextOverflow.ellipsis,
                                      ),
                                      const SizedBox(height: 4),
                                      MyText(
                                        color: Colors.grey.shade400,
                                        text: episode.podcastTitle ?? "",
                                        fontsize: 13,
                                        maxline: 1,
                                        fontstyle: FontStyle.normal,
                                        inter: 1,
                                        multilanguage: false,
                                        fontwaight: FontWeight.w400,
                                        overflow: TextOverflow.ellipsis,
                                      ),
                                    ],
                                  ),
                                ),
                                // Plus Icon (Add Button)
                                const Icon(
                                  Icons.add_circle_outline,
                                  size: 30,
                                  color: white,
                                ),
                              ],
                            ),
                          ),
                        );
                      },
                    );
                  },
                ),
              ),
            ],
          ),
        );
      },
    );
  }

  Widget podcastEpisodeDetail() {
    final mediaItem =
        audioPlayer.sequenceState.currentSource?.tag as MediaItem?;

    final name = mediaItem?.extras?['name'];
    final description = mediaItem?.extras?['description'];

    if (mediaItem == null || name == null || description == null) {
      return const SizedBox.shrink();
    }

    return Container(
      decoration: BoxDecoration(
        color: const Color(0xff333333),
        borderRadius: BorderRadius.circular(16),
      ),
      child: Padding(
        padding: const EdgeInsets.fromLTRB(10, 15, 10, 20),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            MyText(
              color: white,
              text: "About the Episode",
              multilanguage: false,
              textalign: TextAlign.left,
              fontsize: Dimens.textTitle,
              inter: 1,
              maxline: 2,
              fontwaight: FontWeight.w600,
              overflow: TextOverflow.ellipsis,
              fontstyle: FontStyle.normal,
            ),
            // MyText(
            //   color: white,
            //   text: name.toString(),
            //   multilanguage: false,
            //   textalign: TextAlign.left,
            //   fontsize: Dimens.textTitle,
            //   inter: 1,
            //   maxline: 2,
            //   fontwaight: FontWeight.w400,
            //   overflow: TextOverflow.ellipsis,
            //   fontstyle: FontStyle.normal,
            // ),
            const SizedBox(height: 10),
            MyText(
              color: white,
              text: description.toString(),
              multilanguage: false,
              textalign: TextAlign.left,
              fontsize: Dimens.textMedium,
              inter: 1,
              maxline: 100,
              fontwaight: FontWeight.w400,
              overflow: TextOverflow.ellipsis,
              fontstyle: FontStyle.normal,
            ),
          ],
        ),
      ),
    );
  }

  // ── Player Like/Favorite button ──
  // Toggles the current track's favorite state (music = type 3). Seeds from
  // the song's is_favorite flag in the API response, and flips the value on
  // the MediaItem's extras so the heart updates instantly.
  bool _likeBusy = false;
  Widget _buildPlayerLikeButton(MediaItem? tag) {
    final isFav = (tag?.extras?['is_favorite'] == 1 ||
        tag?.extras?['is_favorite'] == '1' ||
        tag?.extras?['is_favorite'] == true);
    return InkWell(
      onTap: () async {
        if (_likeBusy || tag == null) return;
        final contentId =
            (tag.extras?['id'] ?? tag.extras?['content_id'] ?? tag.id)
                .toString();
        if (contentId.isEmpty) return;
        _likeBusy = true;
        final newVal = isFav ? 0 : 1;
        // optimistic UI
        tag.extras?['is_favorite'] = newVal;
        if (mounted) setState(() {});
        try {
          await ApiService().addfavourite(3, contentId);
        } catch (_) {
          tag.extras?['is_favorite'] = isFav ? 1 : 0; // revert on failure
        }
        _likeBusy = false;
        if (mounted) setState(() {});
      },
      child: Container(
        padding: const EdgeInsets.fromLTRB(12, 8, 12, 8),
        decoration: BoxDecoration(
          borderRadius: BorderRadius.circular(20),
          color: riverTeal.withValues(alpha: 0.15),
        ),
        child: Row(
          mainAxisSize: MainAxisSize.min,
          children: [
            Icon(
              isFav ? Icons.favorite_rounded : Icons.favorite_border_rounded,
              color: riverTeal,
              size: 22,
            ),
            const SizedBox(width: 6),
            MyText(
              color: riverTeal,
              text: isFav ? "Liked" : "Like",
              fontsize: 11,
              fontwaight: FontWeight.w600,
              fontstyle: FontStyle.normal,
            ),
          ],
        ),
      ),
    );
  }

  // Small MiniPlayer Panal Open Using This Method
  Widget buildMusicPanel(
      dynamic dynamicPanelHeight, elementOpacity, progressIndicatorHeight) {
    return StreamBuilder<SequenceState?>(
      stream: audioPlayer.sequenceStateStream,
      builder: (context, snapshot) {
        // if ((audioPlayer.sequenceState.currentSource?.tag as MediaItem?)
        //             ?.extras?['is_premium'] ==
        //         1 &&
        //     (audioPlayer.sequenceState.currentSource?.tag as MediaItem?)
        //             ?.extras?['is_buy'] ==
        //         0) {
        //   audioPlayer.pause();
        // } else {
        //   audioPlayer.play();
        // }
        // Spotify-style mini player — 64 px, flush above the nav bar, with a
        // live progress line along the top edge and swipe-left/right to skip.
        final isDark = Theme.of(context).brightness == Brightness.dark;
        final tag = audioPlayer.sequenceState.currentSource?.tag as MediaItem?;
        final artUrl = tag?.artUri?.toString() ?? '';
        final title = tag?.title ?? 'Now Playing';
        final artist = tag?.artist ?? '';

        return GestureDetector(
          // Swipe left → next track, swipe right → previous. Vertical drags
          // still bubble up to the Miniplayer's expand/collapse handler.
          onHorizontalDragEnd: (details) {
            final v = details.primaryVelocity ?? 0;
            if (v < -250 && audioPlayer.hasNext) {
              audioPlayer.seekToNext();
            } else if (v > 250 && audioPlayer.hasPrevious) {
              audioPlayer.seekToPrevious();
            }
          },
          child: Container(
            height: playerMinHeight,
            decoration: BoxDecoration(
              // Same background as the nav bar (surfaceCard / white) so the
              // mini player + nav bar read as ONE connected panel, not two
              // stacked cards. A single soft top shadow lifts the whole unit
              // off the content above; no side/bottom borders (that outline
              // was what made it look like a separate floating card).
              color: isDark ? surfaceCard : white,
              borderRadius: const BorderRadius.only(
                topLeft: Radius.circular(24),
                topRight: Radius.circular(24),
              ),
              boxShadow: [
                BoxShadow(
                  color: black.withValues(alpha: isDark ? 0.28 : 0.10),
                  blurRadius: 18,
                  offset: const Offset(0, -3),
                ),
              ],
            ),
            child: ClipRRect(
              borderRadius: const BorderRadius.only(
                topLeft: Radius.circular(24),
                topRight: Radius.circular(24),
              ),
              child: Column(
                children: [
                  // Live progress line (Spotify-style thin bar along the top)
                  StreamBuilder<PositionData>(
                    stream: positionDataStream,
                    builder: (context, snap) {
                      final pos = snap.data?.position ?? Duration.zero;
                      final dur = snap.data?.duration ?? Duration.zero;
                      final frac = dur.inMilliseconds > 0
                          ? (pos.inMilliseconds / dur.inMilliseconds)
                              .clamp(0.0, 1.0)
                          : 0.0;
                      return LinearProgressIndicator(
                        value: frac,
                        minHeight: 2.5,
                        backgroundColor:
                            (isDark ? white : black).withValues(alpha: 0.08),
                        valueColor:
                            const AlwaysStoppedAnimation<Color>(brandGreen),
                      );
                    },
                  ),
                  Expanded(
                    child: Padding(
                      padding: const EdgeInsets.symmetric(horizontal: 14),
                      child: Row(
                        children: [
                          ClipRRect(
                            borderRadius: BorderRadius.circular(10),
                            child: artUrl.isNotEmpty
                                ? MyNetworkImage(
                                    imgWidth: 42,
                                    imgHeight: 42,
                                    imageUrl: artUrl,
                                    fit: BoxFit.cover)
                                : Container(
                                    width: 42,
                                    height: 42,
                                    decoration: BoxDecoration(
                                        borderRadius: BorderRadius.circular(10),
                                        gradient: brandGradient()),
                                    child: const Icon(Icons.music_note_rounded,
                                        color: Colors.white, size: 22),
                                  ),
                          ),
                          const SizedBox(width: 12),
                          Expanded(
                            child: Column(
                              mainAxisAlignment: MainAxisAlignment.center,
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Text(
                                  title,
                                  style: TextStyle(
                                      fontSize: 13,
                                      fontWeight: FontWeight.w600,
                                      color: isDark ? warmWhite : black),
                                  maxLines: 1,
                                  overflow: TextOverflow.ellipsis,
                                ),
                                if (artist.isNotEmpty)
                                  Text(
                                    artist,
                                    style: TextStyle(
                                        fontSize: 11,
                                        color: isDark
                                            ? white.withValues(alpha: 0.55)
                                            : black.withValues(alpha: 0.55)),
                                    maxLines: 1,
                                    overflow: TextOverflow.ellipsis,
                                  ),
                              ],
                            ),
                          ),
                          // Skip-next button (Spotify has play + next on the mini bar)
                          StreamBuilder<SequenceState?>(
                            stream: audioPlayer.sequenceStateStream,
                            builder: (context, _) {
                              final hasNext = audioPlayer.hasNext;
                              return GestureDetector(
                                onTap: hasNext ? audioPlayer.seekToNext : null,
                                child: Padding(
                                  padding:
                                      const EdgeInsets.symmetric(horizontal: 6),
                                  child: Icon(
                                    Icons.skip_next_rounded,
                                    color: (isDark ? white : black).withValues(
                                        alpha: hasNext ? 0.85 : 0.30),
                                    size: 28,
                                  ),
                                ),
                              );
                            },
                          ),
                          const SizedBox(width: 4),
                          StreamBuilder<PlayerState>(
                            stream: audioPlayer.playerStateStream,
                            builder: (context, snapshot) {
                              final state = snapshot.data;
                              final loading = state?.processingState ==
                                      ProcessingState.loading ||
                                  state?.processingState ==
                                      ProcessingState.buffering;
                              final playing = state?.playing ?? false;
                              return GestureDetector(
                                onTap: () async {
                                  if (playing) {
                                    await _stopAndSendUserAction(actionType: 1);
                                    _checkPremiumPlayPause();
                                  } else {
                                    _checkPremiumPlayPause();
                                  }
                                },
                                child: Container(
                                  width: 34,
                                  height: 34,
                                  decoration: BoxDecoration(
                                    shape: BoxShape.circle,
                                    color: brandGreen,
                                    boxShadow: [
                                      BoxShadow(
                                          color: brandGreen.withValues(
                                              alpha: 0.30),
                                          blurRadius: 12)
                                    ],
                                  ),
                                  child: loading
                                      ? const Padding(
                                          padding: EdgeInsets.all(8),
                                          child: CircularProgressIndicator(
                                              strokeWidth: 2,
                                              color: Colors.white),
                                        )
                                      : Icon(
                                          playing
                                              ? Icons.pause_rounded
                                              : Icons.play_arrow_rounded,
                                          color: white,
                                          size: 20),
                                ),
                              );
                            },
                          ),
                          const SizedBox(width: 2),
                        ],
                      ),
                    ),
                  ),
                ],
              ),
            ),
          ),
        );
      },
    );
  }

  // 10 Second Next And Previous Functionality
  // bool isnext = true > next Audio Seek
  // bool isnext = false > previous Audio Seek
  void tenSecNextOrPrevious(String audioposition, bool isnext) {
    dynamic firstHalf = Duration(seconds: int.parse(audioposition));
    const secondHalf = Duration(seconds: 10);
    Duration movePosition;
    if (isnext == true) {
      movePosition = firstHalf + secondHalf;
    } else {
      movePosition = firstHalf - secondHalf;
    }

    musicManager.seek(movePosition);
  }

  Future<void> _fetchEpisodeByPodcast(dynamic podcastId, int? nextPage) async {
    printLog("Pageno:== ${(nextPage ?? 0) + 1}");
    await musicDetailProvider.getEpisodebyPodcastList(
        podcastId, (nextPage ?? 0) + 1);
    musicDetailProvider.setLoadMore(false);
  }
  /* ================================================ Like / UnLike END */

  void commentBottomSheet(
      {required int index, required podcastId, required episodeId}) {
    showModalBottomSheet<void>(
      context: context,
      backgroundColor: Theme.of(context).bottomSheetTheme.backgroundColor,
      isScrollControlled: true,
      useSafeArea: true,
      isDismissible: true,
      shape: const RoundedRectangleBorder(
        borderRadius: BorderRadius.vertical(top: Radius.circular(5)),
      ),
      clipBehavior: Clip.antiAliasWithSaveLayer,
      builder: (BuildContext context) {
        return Wrap(
          children: [
            buildComment(index, podcastId, episodeId),
          ],
        );
      },
    ).whenComplete(() {
      commentController.clear();
      musicDetailProvider.clearComment();
    });
  }

/* Build Comment List */
  Widget buildComment(dynamic index, dynamic podcastId, episodeId) {
    return AnimatedPadding(
      padding:
          EdgeInsets.only(bottom: MediaQuery.of(context).viewInsets.bottom),
      duration: const Duration(milliseconds: 100),
      curve: Curves.decelerate,
      child: Container(
        height: MediaQuery.of(context).size.height * 0.5,
        constraints: BoxConstraints(
          minHeight: 0,
          maxHeight: MediaQuery.of(context).size.height,
        ),
        width: MediaQuery.of(context).size.width,
        child: Column(
          mainAxisAlignment: MainAxisAlignment.spaceBetween,
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            SizedBox(
              width: MediaQuery.of(context).size.width,
              height: 50,
              child: Row(
                children: [
                  Expanded(
                    child: Container(
                      margin: const EdgeInsets.only(left: 20),
                      child: MyText(
                        color: white,
                        multilanguage: true,
                        text: "comment",
                        fontsize: Dimens.textMedium,
                        fontstyle: FontStyle.normal,
                        fontwaight: FontWeight.w600,
                        maxline: 1,
                        overflow: TextOverflow.ellipsis,
                        textalign: TextAlign.start,
                      ),
                    ),
                  ),
                  Container(
                    margin: const EdgeInsets.only(right: 12),
                    child: InkWell(
                      borderRadius: BorderRadius.circular(5),
                      onTap: () {
                        Navigator.pop(context);
                        commentController.clear();
                        musicDetailProvider.clearComment();
                      },
                      child: Container(
                          padding: const EdgeInsets.all(8),
                          child: Icon(
                            Icons.close,
                            size: 20,
                            color: white,
                          )),
                    ),
                  ),
                ],
              ),
            ),
            Expanded(
              child: SingleChildScrollView(
                scrollDirection: Axis.vertical,
                physics: const BouncingScrollPhysics(),
                padding: const EdgeInsets.fromLTRB(20, 0, 20, 0),
                child: Column(
                  children: [
                    Consumer<MusicDetailProvider>(
                        builder: (context, commentprovider, child) {
                      if (musicDetailProvider.commentloading &&
                          !musicDetailProvider.commentloadMore) {
                        return Utils.pageLoader();
                      } else {
                        if (musicDetailProvider.commentListModel.status ==
                                200 &&
                            musicDetailProvider.commentList != null) {
                          if ((musicDetailProvider.commentList?.length ?? 0) >
                              0) {
                            return Column(
                              mainAxisAlignment: MainAxisAlignment.start,
                              children: [
                                Align(
                                  alignment: Alignment.topCenter,
                                  child: ListView.builder(
                                      scrollDirection: Axis.vertical,
                                      shrinkWrap: true,
                                      physics:
                                          const NeverScrollableScrollPhysics(),
                                      itemCount:
                                          commentprovider.commentList?.length ??
                                              0,
                                      itemBuilder: (BuildContext ctx, index) {
                                        return Padding(
                                          padding: const EdgeInsets.fromLTRB(
                                              0, 10, 0, 10),
                                          child: Row(
                                            crossAxisAlignment:
                                                CrossAxisAlignment.start,
                                            mainAxisAlignment:
                                                MainAxisAlignment.start,
                                            children: [
                                              Container(
                                                padding:
                                                    const EdgeInsets.all(1),
                                                decoration: BoxDecoration(
                                                    borderRadius:
                                                        BorderRadius.circular(
                                                            50),
                                                    border: Border.all(
                                                        width: 1,
                                                        color: white)),
                                                child: ClipRRect(
                                                  borderRadius:
                                                      BorderRadius.circular(50),
                                                  child: MyNetworkImage(
                                                      imageUrl: commentprovider
                                                              .commentList?[
                                                                  index]
                                                              .image
                                                              .toString() ??
                                                          "",
                                                      fit: BoxFit.fill,
                                                      imgWidth: 30,
                                                      imgHeight: 30),
                                                ),
                                              ),
                                              const SizedBox(width: 15),
                                              Column(
                                                crossAxisAlignment:
                                                    CrossAxisAlignment.start,
                                                children: [
                                                  MyText(
                                                      color: brandGreen,
                                                      text: commentprovider
                                                                  .commentList?[
                                                                      index]
                                                                  .fullName
                                                                  .toString() ==
                                                              ""
                                                          ? "${commentprovider.commentList?[index].userName.toString()}"
                                                          : commentprovider
                                                                  .commentList?[
                                                                      index]
                                                                  .fullName
                                                                  .toString() ??
                                                              "",
                                                      fontsize:
                                                          Dimens.textMedium,
                                                      fontwaight:
                                                          FontWeight.w500,
                                                      multilanguage: false,
                                                      maxline: 1,
                                                      overflow:
                                                          TextOverflow.ellipsis,
                                                      textalign:
                                                          TextAlign.center,
                                                      fontstyle:
                                                          FontStyle.normal),
                                                  const SizedBox(height: 8),
                                                  SizedBox(
                                                    width:
                                                        MediaQuery.of(context)
                                                                .size
                                                                .width *
                                                            0.70,
                                                    child: MyText(
                                                        color: Theme.of(context)
                                                            .colorScheme
                                                            .surface,
                                                        text: commentprovider
                                                                .commentList?[
                                                                    index]
                                                                .comment
                                                                .toString() ??
                                                            "",
                                                        fontsize:
                                                            Dimens.textSmall,
                                                        fontwaight:
                                                            FontWeight.w400,
                                                        multilanguage: false,
                                                        maxline: 3,
                                                        overflow: TextOverflow
                                                            .ellipsis,
                                                        textalign:
                                                            TextAlign.left,
                                                        fontstyle:
                                                            FontStyle.normal),
                                                  ),
                                                  const SizedBox(height: 7),
                                                ],
                                              ),
                                            ],
                                          ),
                                        );
                                      }),
                                ),
                                if (musicDetailProvider.commentloading)
                                  const CircularProgressIndicator(
                                    color: colorAccent,
                                  )
                                else
                                  const SizedBox.shrink(),
                              ],
                            );
                          } else {
                            return Align(
                              alignment: Alignment.center,
                              child: MyImage(
                                width: 130,
                                height:
                                    MediaQuery.of(context).size.height * 0.40,
                                fit: BoxFit.contain,
                                imagePath: "nodata.png",
                              ),
                            );
                          }
                        } else {
                          return Align(
                            alignment: Alignment.center,
                            child: MyImage(
                              width: 130,
                              height: MediaQuery.of(context).size.height * 0.35,
                              fit: BoxFit.contain,
                              imagePath: "nodata.png",
                            ),
                          );
                        }
                      }
                    }),
                  ],
                ),
              ),
            ),
            Container(
              width: MediaQuery.of(context).size.width,
              height: 50,
              constraints: BoxConstraints(
                minHeight: 0,
                maxHeight: MediaQuery.of(context).size.height,
              ),
              alignment: Alignment.center,
              child: Center(
                child: Row(
                  mainAxisAlignment: MainAxisAlignment.center,
                  crossAxisAlignment: CrossAxisAlignment.center,
                  children: [
                    Expanded(
                      child: TextFormField(
                        controller: commentController,
                        maxLines: 1,
                        scrollPhysics: const AlwaysScrollableScrollPhysics(),
                        textAlign: TextAlign.start,
                        cursorColor: white,
                        decoration: InputDecoration(
                          filled: true,
                          fillColor: transparent,
                          border: InputBorder.none,
                          hintText: "Add Comments",
                          hintStyle: GoogleFonts.inter(
                            fontSize: 14,
                            fontWeight: FontWeight.w500,
                            fontStyle: FontStyle.normal,
                            color: white,
                          ),
                          contentPadding:
                              const EdgeInsets.only(left: 10, right: 10),
                        ),
                        obscureText: false,
                        style: GoogleFonts.inter(
                          fontSize: 14,
                          fontWeight: FontWeight.w500,
                          fontStyle: FontStyle.normal,
                          color: white,
                        ),
                      ),
                    ),
                    const SizedBox(width: 3),
                    InkWell(
                      borderRadius: BorderRadius.circular(5),
                      onTap: () async {
                        if (Constant.userID == null) {
                          Navigator.push(
                            context,
                            MaterialPageRoute(
                              builder: (context) {
                                return const Login();
                              },
                            ),
                          );
                        } else if (commentController.text.isEmpty) {
                          Utils.showToast("Please Enter Your Comment");
                        } else {
                          musicDetailProvider.getaddcomment(podcastId,
                              commentController.text, "2", episodeId);

                          if (musicDetailProvider.successModel.status == 200) {
                            commentController.clear();

                            setState(() {
                              (audioPlayer.sequenceState.currentSource?.tag
                                      as MediaItem?)
                                  ?.extras?['total_comment'] = (audioPlayer
                                          .sequenceState
                                          .currentSource
                                          ?.tag as MediaItem?)
                                      ?.extras?['total_comment'] +
                                  1;
                            });
                          } else {
                            Utils.showToast(
                                musicDetailProvider.successModel.message ?? "");
                          }
                        }
                      },
                      child: Padding(
                        padding: const EdgeInsets.all(4),
                        child: SizedBox(
                          width: 30,
                          height: 30,
                          child: Consumer<MusicDetailProvider>(
                            builder: (context, commentprovider, child) {
                              if (commentprovider.addcommentloading) {
                                return const SizedBox(
                                  height: 20,
                                  width: 20,
                                  child: CircularProgressIndicator(
                                    color: colorAccent,
                                    strokeWidth: 1,
                                  ),
                                );
                              } else {
                                return Icon(
                                  Icons.send,
                                  size: 20,
                                  color: white,
                                );
                              }
                            },
                          ),
                        ),
                      ),
                    ),
                    const SizedBox(width: 10),
                  ],
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }
}
