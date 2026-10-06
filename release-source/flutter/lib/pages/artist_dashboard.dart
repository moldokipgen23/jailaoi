import 'package:flutter/material.dart';
import 'package:jailaoi/pages/portal_webview.dart';
import 'package:jailaoi/utils/color.dart';
import 'package:jailaoi/utils/constant.dart';
import 'package:jailaoi/utils/sharedpref.dart';
import 'package:jailaoi/webservice/apiservices.dart';
import 'package:jailaoi/widget/mynetworkimg.dart';
import 'package:jailaoi/widget/mytext.dart';
import 'package:url_launcher/url_launcher.dart';

class ArtistDashboardPage extends StatefulWidget {
  const ArtistDashboardPage({super.key});

  @override
  State<ArtistDashboardPage> createState() => _ArtistDashboardPageState();
}

class _ArtistDashboardPageState extends State<ArtistDashboardPage> {
  bool _loading = true;
  bool _portalLoading = false;
  Map<String, dynamic> _data = {};
  String _supportEmail = '';

  @override
  void initState() {
    super.initState();
    _load();
  }

  Future<void> _load() async {
    final data = await ApiService().getArtistDashboard();
    final email = await SharedPref().read('email') ?? '';
    if (mounted) {
      setState(() {
        _data = data;
        _supportEmail = email;
        _loading = false;
      });
    }
  }

  Future<void> _openPortal(String path, {String? title}) async {
    // Block immediately if suspended
    if ((_data['is_suspended'] ?? 0) == 1) return;

    setState(() => _portalLoading = true);
    final result = await ApiService().generatePortalToken();
    if (mounted) setState(() => _portalLoading = false);
    if (!mounted) return;

    // API returned 423 — suspended
    if (result is Map && result['suspended'] == true) {
      setState(() {
        _data['is_suspended'] = 1;
        _data['suspend_reason'] = result['reason'] ?? '';
      });
      return;
    }

    final token = result is String ? result : null;
    final base = 'https://portal.jailaoi.com/user/$path';
    final url = token != null ? '$base?portal_token=$token' : base;
    Navigator.push(
      context,
      MaterialPageRoute(
        builder: (_) => PortalWebViewPage(
          url: url,
          title: title ?? 'Artist Portal',
        ),
      ),
    );
  }

  String _fmt(dynamic n) {
    final v =
        (n is num) ? n.toInt() : (int.tryParse(n?.toString() ?? '0') ?? 0);
    if (v >= 1000000) return '${(v / 1000000).toStringAsFixed(1)}M';
    if (v >= 1000) return '${(v / 1000).toStringAsFixed(1)}K';
    return '$v';
  }

  String _fmtMoney(dynamic n) {
    final v = (n is num)
        ? n.toDouble()
        : (double.tryParse(n?.toString() ?? '0') ?? 0.0);
    // JailaOi is an INR app — artist earnings/wallet are in rupees, not dollars.
    final symbol =
        Constant.currencySymbol.isNotEmpty ? Constant.currencySymbol : '₹';
    return '$symbol${v.toStringAsFixed(2)}';
  }

  @override
  Widget build(BuildContext context) {
    final isDark = Theme.of(context).brightness == Brightness.dark;
    final bg = isDark ? hillNight : surfaceLight;
    final textPrimary = isDark ? warmWhite : black;

    return Scaffold(
      backgroundColor: bg,
      appBar: AppBar(
        backgroundColor: bg,
        elevation: 0,
        leading: IconButton(
          icon: Icon(Icons.arrow_back_ios_new_rounded,
              color: textPrimary, size: 20),
          onPressed: () => Navigator.pop(context),
        ),
        title: MyText(
          text: 'Artist Dashboard',
          color: textPrimary,
          fontsize: 17,
          fontwaight: FontWeight.w700,
          inter: 1,
          fontstyle: FontStyle.normal,
          maxline: 1,
          overflow: TextOverflow.ellipsis,
        ),
        centerTitle: true,
        actions: [
          if (_portalLoading)
            const Padding(
              padding: EdgeInsets.only(right: 16),
              child: Center(
                  child: SizedBox(
                      width: 18,
                      height: 18,
                      child: CircularProgressIndicator(
                          color: brandGreen, strokeWidth: 2))),
            ),
        ],
      ),
      body: _loading
          ? const Center(child: CircularProgressIndicator(color: brandGreen))
          : _data['load_error'] != null
              ? Center(
                  child: Padding(
                      padding: const EdgeInsets.all(24),
                      child: Column(mainAxisSize: MainAxisSize.min, children: [
                        Text(_data['load_error'].toString(),
                            textAlign: TextAlign.center,
                            style: TextStyle(color: textPrimary)),
                        const SizedBox(height: 16),
                        ElevatedButton(
                            onPressed: () {
                              setState(() => _loading = true);
                              _load();
                            },
                            child: const Text('Try again'))
                      ])))
              : (_data['is_suspended'] ?? 0) == 1
                  ? _buildSuspendedScreen(isDark, textPrimary)
                  : _buildBody(isDark, textPrimary),
    );
  }

  Widget _buildSuspendedScreen(bool isDark, Color textPrimary) {
    final reason = _data['suspend_reason']?.toString() ?? '';
    final textSub =
        isDark ? white.withValues(alpha: 0.55) : black.withValues(alpha: 0.55);
    final cardBg = isDark ? surfaceCard : white;

    return SingleChildScrollView(
      padding: const EdgeInsets.all(24),
      child: Column(
        children: [
          const SizedBox(height: 40),
          // Warning icon
          Container(
            width: 88,
            height: 88,
            decoration: BoxDecoration(
              shape: BoxShape.circle,
              color: const Color(0xFFFEF3C7),
            ),
            child: const Icon(Icons.block_rounded,
                color: Color(0xFFF59E0B), size: 44),
          ),
          const SizedBox(height: 24),
          MyText(
            text: 'Account Suspended',
            color: const Color(0xFFF59E0B),
            fontsize: 22,
            fontwaight: FontWeight.w800,
            inter: 1,
            fontstyle: FontStyle.normal,
            maxline: 1,
            overflow: TextOverflow.ellipsis,
            textalign: TextAlign.center,
          ),
          const SizedBox(height: 10),
          MyText(
            text:
                'Your artist account has been temporarily suspended by the admin. All features and payments are on hold until this is resolved.',
            color: textSub,
            fontsize: 13,
            fontwaight: FontWeight.w400,
            inter: 1,
            fontstyle: FontStyle.normal,
            maxline: 5,
            overflow: TextOverflow.ellipsis,
            textalign: TextAlign.center,
          ),
          const SizedBox(height: 24),
          if (reason.isNotEmpty) ...[
            Container(
              width: double.infinity,
              padding: const EdgeInsets.all(16),
              decoration: BoxDecoration(
                color:
                    isDark ? const Color(0xFF2A1F0A) : const Color(0xFFFEF9EC),
                borderRadius: BorderRadius.circular(14),
                border: Border.all(
                    color: const Color(0xFFF59E0B).withValues(alpha: 0.4)),
              ),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  MyText(
                    text: 'Reason',
                    color: const Color(0xFFF59E0B),
                    fontsize: 11,
                    fontwaight: FontWeight.w700,
                    inter: 1,
                    fontstyle: FontStyle.normal,
                    maxline: 1,
                    overflow: TextOverflow.ellipsis,
                  ),
                  const SizedBox(height: 6),
                  MyText(
                    text: reason,
                    color: textPrimary,
                    fontsize: 13,
                    fontwaight: FontWeight.w400,
                    inter: 1,
                    fontstyle: FontStyle.normal,
                    maxline: 10,
                    overflow: TextOverflow.ellipsis,
                  ),
                ],
              ),
            ),
            const SizedBox(height: 20),
          ],
          // What's on hold
          Container(
            width: double.infinity,
            padding: const EdgeInsets.all(16),
            decoration: BoxDecoration(
              color: cardBg,
              borderRadius: BorderRadius.circular(14),
              border: Border.all(
                  color: isDark
                      ? white.withValues(alpha: 0.07)
                      : black.withValues(alpha: 0.06)),
            ),
            child: Column(
              children: [
                _holdItem(Icons.upload_file_rounded, 'Music uploads paused',
                    textPrimary, textSub),
                const SizedBox(height: 12),
                _holdItem(Icons.account_balance_wallet_rounded,
                    'Payments & withdrawals on hold', textPrimary, textSub),
                const SizedBox(height: 12),
                _holdItem(Icons.bar_chart_rounded, 'Dashboard access disabled',
                    textPrimary, textSub),
              ],
            ),
          ),
          const SizedBox(height: 28),
          // Contact support button
          GestureDetector(
            onTap: () async {
              final email = _supportEmail.isNotEmpty
                  ? _supportEmail
                  : 'support@jailaoi.com';
              final uri = Uri(
                scheme: 'mailto',
                path: email,
                queryParameters: {
                  'subject': 'Account Suspended — Support Request',
                  'body':
                      'Hello Support,\n\nMy artist account has been suspended. I would like to appeal this decision.\n\nAccount: ${_data['artist']?['name'] ?? ''}\n\nPlease review my case.\n\nThank you.',
                },
              );
              if (await canLaunchUrl(uri)) await launchUrl(uri);
            },
            child: Container(
              width: double.infinity,
              height: 50,
              alignment: Alignment.center,
              decoration: BoxDecoration(
                gradient: brandGradient(),
                borderRadius: BorderRadius.circular(25),
              ),
              child: MyText(
                text: 'Contact Support',
                color: white,
                fontsize: 15,
                fontwaight: FontWeight.w700,
                inter: 1,
                fontstyle: FontStyle.normal,
                maxline: 1,
                overflow: TextOverflow.ellipsis,
              ),
            ),
          ),
          const SizedBox(height: 40),
        ],
      ),
    );
  }

  Widget _holdItem(
      IconData icon, String label, Color textPrimary, Color textSub) {
    return Row(
      children: [
        Icon(icon, color: const Color(0xFFF59E0B), size: 18),
        const SizedBox(width: 10),
        MyText(
          text: label,
          color: textSub,
          fontsize: 13,
          fontwaight: FontWeight.w400,
          inter: 1,
          fontstyle: FontStyle.normal,
          maxline: 1,
          overflow: TextOverflow.ellipsis,
        ),
      ],
    );
  }

  Widget _buildBody(bool isDark, Color textPrimary) {
    final textSub =
        isDark ? white.withValues(alpha: 0.55) : black.withValues(alpha: 0.55);
    final cardBg = isDark ? surfaceCard : white;
    final artist = _data['artist'] as Map? ?? {};
    final artistName = artist['name']?.toString() ?? 'Your Artist Page';
    final artistImage = artist['image']?.toString() ?? '';
    final isVerified = (_data['is_verified'] ?? 0) == 1;
    final monetizationStatus = _data['monetization_status']?.toString();
    final kycStatus = _data['kyc_status']?.toString();
    final recentTracks = (_data['recent_tracks'] as List?) ?? [];

    return SingleChildScrollView(
      padding: const EdgeInsets.all(20),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // ── Artist Header ──
          Container(
            width: double.infinity,
            padding: const EdgeInsets.all(20),
            decoration: BoxDecoration(
              gradient: brandGradient(),
              borderRadius: BorderRadius.circular(20),
            ),
            child: Row(
              children: [
                ClipRRect(
                  borderRadius: BorderRadius.circular(30),
                  child: artistImage.isNotEmpty
                      ? MyNetworkImage(
                          imgWidth: 56,
                          imgHeight: 56,
                          imageUrl: artistImage,
                          fit: BoxFit.cover)
                      : Container(
                          width: 56,
                          height: 56,
                          decoration: BoxDecoration(
                              shape: BoxShape.circle,
                              color: white.withValues(alpha: 0.2)),
                          child: const Icon(Icons.mic_rounded,
                              color: white, size: 28),
                        ),
                ),
                const SizedBox(width: 14),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Row(
                        children: [
                          Flexible(
                            child: MyText(
                              text: artistName,
                              color: white,
                              fontsize: 17,
                              fontwaight: FontWeight.w700,
                              inter: 1,
                              fontstyle: FontStyle.normal,
                              maxline: 1,
                              overflow: TextOverflow.ellipsis,
                            ),
                          ),
                          if (isVerified) ...[
                            const SizedBox(width: 6),
                            const Icon(Icons.verified_rounded,
                                color: white, size: 16),
                          ],
                        ],
                      ),
                      const SizedBox(height: 3),
                      MyText(
                        text: isVerified ? 'Verified Artist' : 'Artist Account',
                        color: white.withValues(alpha: 0.75),
                        fontsize: 12,
                        fontwaight: FontWeight.w400,
                        inter: 1,
                        fontstyle: FontStyle.normal,
                        maxline: 1,
                        overflow: TextOverflow.ellipsis,
                      ),
                    ],
                  ),
                ),
              ],
            ),
          ),

          const SizedBox(height: 16),

          // ── Stats grid ──
          Row(
            children: [
              _stat(
                  isDark,
                  cardBg,
                  textPrimary,
                  textSub,
                  Icons.play_circle_rounded,
                  _fmt(_data['total_views']),
                  'Total Plays',
                  brandGreen),
              const SizedBox(width: 10),
              _stat(isDark, cardBg, textPrimary, textSub, Icons.people_rounded,
                  _fmt(_data['total_followers']), 'Followers', riverTeal),
              const SizedBox(width: 10),
              _stat(
                  isDark,
                  cardBg,
                  textPrimary,
                  textSub,
                  Icons.headphones_rounded,
                  _fmt(_data['monthly_listeners']),
                  'Monthly',
                  const Color(0xFF8B7FFF)),
            ],
          ),
          const SizedBox(height: 10),
          Row(
            children: [
              _stat(
                  isDark,
                  cardBg,
                  textPrimary,
                  textSub,
                  Icons.library_music_rounded,
                  _fmt(_data['total_content']),
                  'Tracks',
                  const Color(0xFFE8A838)),
              const SizedBox(width: 10),
              _earningsCard(
                  isDark, cardBg, textPrimary, textSub, monetizationStatus),
            ],
          ),

          const SizedBox(height: 20),

          _revenueSummary(cardBg, textPrimary, textSub),
          const SizedBox(height: 20),

          // ── Recent Tracks ──
          if (recentTracks.isNotEmpty) ...[
            _sectionLabel('Recent Uploads', textSub),
            const SizedBox(height: 10),
            Container(
              decoration: BoxDecoration(
                color: cardBg,
                borderRadius: BorderRadius.circular(16),
                border: Border.all(
                    color: isDark
                        ? white.withValues(alpha: 0.07)
                        : black.withValues(alpha: 0.06)),
              ),
              child: Column(
                children: List.generate(recentTracks.length, (i) {
                  final track = recentTracks[i] as Map;
                  final isLast = i == recentTracks.length - 1;
                  return Column(
                    children: [
                      Padding(
                        padding: const EdgeInsets.symmetric(
                            horizontal: 14, vertical: 10),
                        child: Row(
                          children: [
                            ClipRRect(
                              borderRadius: BorderRadius.circular(8),
                              child: (track['image']?.toString().isNotEmpty ==
                                      true)
                                  ? MyNetworkImage(
                                      imgWidth: 42,
                                      imgHeight: 42,
                                      imageUrl: track['image'].toString(),
                                      fit: BoxFit.cover)
                                  : Container(
                                      width: 42,
                                      height: 42,
                                      decoration: BoxDecoration(
                                          borderRadius:
                                              BorderRadius.circular(8),
                                          gradient: brandGradient()),
                                      child: const Icon(
                                          Icons.music_note_rounded,
                                          color: white,
                                          size: 20),
                                    ),
                            ),
                            const SizedBox(width: 12),
                            Expanded(
                              child: MyText(
                                text: track['title']?.toString() ?? 'Untitled',
                                color: textPrimary,
                                fontsize: 13,
                                fontwaight: FontWeight.w500,
                                inter: 1,
                                fontstyle: FontStyle.normal,
                                maxline: 1,
                                overflow: TextOverflow.ellipsis,
                              ),
                            ),
                            const SizedBox(width: 8),
                            Row(
                              children: [
                                Icon(Icons.play_arrow_rounded,
                                    color: textSub, size: 14),
                                const SizedBox(width: 2),
                                MyText(
                                  text: _fmt(track['total_play'] ?? 0),
                                  color: textSub,
                                  fontsize: 12,
                                  fontwaight: FontWeight.w400,
                                  inter: 1,
                                  fontstyle: FontStyle.normal,
                                  maxline: 1,
                                  overflow: TextOverflow.ellipsis,
                                ),
                              ],
                            ),
                          ],
                        ),
                      ),
                      if (!isLast)
                        Divider(
                            height: 1,
                            color: isDark
                                ? white.withValues(alpha: 0.06)
                                : black.withValues(alpha: 0.06)),
                    ],
                  );
                }),
              ),
            ),
            const SizedBox(height: 20),
          ],

          // ── Quick Actions ──
          _sectionLabel('Manage', textSub),
          const SizedBox(height: 10),
          _action(isDark, cardBg, textPrimary, textSub,
              icon: Icons.upload_rounded,
              iconBg: const Color(0xFF2A1F5F),
              iconColor: const Color(0xFF8B7FFF),
              title: 'Upload Music',
              sub: 'Add new songs, radio or podcast',
              onTap: () => _openPortal('music/create', title: 'Upload Music')),
          const SizedBox(height: 8),
          _action(isDark, cardBg, textPrimary, textSub,
              icon: Icons.library_music_rounded,
              iconBg: const Color(0xFF1A2F1A),
              iconColor: brandGreen,
              title: 'My Music',
              sub: 'Manage your uploaded tracks',
              onTap: () => _openPortal('music', title: 'My Music')),
          const SizedBox(height: 8),
          _action(isDark, cardBg, textPrimary, textSub,
              icon: Icons.account_balance_wallet_rounded,
              iconBg: const Color(0xFF2F2A1A),
              iconColor: const Color(0xFFE8A838),
              title: 'Earnings & Withdrawal',
              sub: 'View revenue and withdraw funds',
              onTap: () => _openPortal('earnings', title: 'Earnings')),
          const SizedBox(height: 8),
          _action(isDark, cardBg, textPrimary, textSub,
              icon: Icons.verified_user_rounded,
              iconBg: const Color(0xFF1A2A2F),
              iconColor: riverTeal,
              title: 'KYC Verification',
              sub: _kycSubtitle(kycStatus),
              badge: _kycBadge(kycStatus),
              onTap: () => _openPortal('kyc', title: 'KYC Verification')),
          const SizedBox(height: 8),
          _action(isDark, cardBg, textPrimary, textSub,
              icon: Icons.monetization_on_rounded,
              iconBg: const Color(0xFF2F1A1A),
              iconColor: const Color(0xFFE84B4B),
              title: 'Monetization',
              sub: _monetizationSubtitle(monetizationStatus),
              badge: _monetizationBadge(monetizationStatus),
              onTap: () => _openPortal('monetization', title: 'Monetization')),
          const SizedBox(height: 8),
          _action(isDark, cardBg, textPrimary, textSub,
              icon: Icons.person_rounded,
              iconBg:
                  isDark ? const Color(0xFF1E1830) : const Color(0xFFEEECF8),
              iconColor: isDark
                  ? white.withValues(alpha: 0.7)
                  : black.withValues(alpha: 0.6),
              title: 'Edit Artist Profile',
              sub: 'Update bio and profile image',
              onTap: () => _openPortal('profile', title: 'Edit Profile')),

          const SizedBox(height: 20),

          // ── Visit Portal banner ──
          GestureDetector(
            onTap: () => _openPortal('dashboard', title: 'Artist Dashboard'),
            child: Container(
              width: double.infinity,
              padding: const EdgeInsets.all(16),
              decoration: BoxDecoration(
                color:
                    isDark ? const Color(0xFF1E1830) : const Color(0xFFEEECF8),
                borderRadius: BorderRadius.circular(16),
                border: Border.all(
                    color: isDark
                        ? white.withValues(alpha: 0.07)
                        : black.withValues(alpha: 0.06)),
              ),
              child: Row(
                children: [
                  Icon(Icons.open_in_browser_rounded, color: textSub, size: 22),
                  const SizedBox(width: 12),
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        MyText(
                          text: 'Full Analytics & More',
                          color: textPrimary,
                          fontsize: 14,
                          fontwaight: FontWeight.w600,
                          inter: 1,
                          fontstyle: FontStyle.normal,
                          maxline: 1,
                          overflow: TextOverflow.ellipsis,
                        ),
                        const SizedBox(height: 2),
                        MyText(
                          text:
                              'Visit your artist portal for detailed insights',
                          color: textSub,
                          fontsize: 12,
                          fontwaight: FontWeight.w400,
                          inter: 1,
                          fontstyle: FontStyle.normal,
                          maxline: 1,
                          overflow: TextOverflow.ellipsis,
                        ),
                      ],
                    ),
                  ),
                  Icon(Icons.arrow_forward_ios_rounded,
                      color: textSub, size: 14),
                ],
              ),
            ),
          ),

          const SizedBox(height: 40),
        ],
      ),
    );
  }

  Widget _sectionLabel(String label, Color color) {
    return MyText(
      text: label,
      color: color,
      fontsize: 12,
      fontwaight: FontWeight.w600,
      inter: 1,
      fontstyle: FontStyle.normal,
      maxline: 1,
      overflow: TextOverflow.ellipsis,
    );
  }

  Widget _stat(bool isDark, Color cardBg, Color textPrimary, Color textSub,
      IconData icon, String value, String label, Color iconColor) {
    return Expanded(
      child: Container(
        padding: const EdgeInsets.symmetric(vertical: 14, horizontal: 8),
        decoration: BoxDecoration(
          color: cardBg,
          borderRadius: BorderRadius.circular(14),
          border: Border.all(
              color: isDark
                  ? white.withValues(alpha: 0.07)
                  : black.withValues(alpha: 0.06)),
        ),
        child: Column(
          children: [
            Icon(icon, color: iconColor, size: 20),
            const SizedBox(height: 6),
            MyText(
                text: value,
                color: textPrimary,
                fontsize: 16,
                fontwaight: FontWeight.w700,
                inter: 1,
                fontstyle: FontStyle.normal,
                maxline: 1,
                overflow: TextOverflow.ellipsis,
                textalign: TextAlign.center),
            const SizedBox(height: 2),
            MyText(
                text: label,
                color: textSub,
                fontsize: 10,
                fontwaight: FontWeight.w400,
                inter: 1,
                fontstyle: FontStyle.normal,
                maxline: 1,
                overflow: TextOverflow.ellipsis,
                textalign: TextAlign.center),
          ],
        ),
      ),
    );
  }

  String _kycSubtitle(String? status) {
    switch (status) {
      case 'approved':
        return 'Identity verified ✓';
      case 'submitted':
        return 'Documents submitted — pending review';
      case 'under_review':
        return 'Under review by our team';
      case 'rejected':
        return 'Verification rejected — resubmit';
      default:
        return 'Required to receive payments';
    }
  }

  Widget? _kycBadge(String? status) {
    switch (status) {
      case 'approved':
        return _badge(
            'Verified', const Color(0xFF1A9B5A), const Color(0xFF22C97A));
      case 'submitted':
      case 'under_review':
        return _badge(
            'Pending', const Color(0xFF3A2F0A), const Color(0xFFE8A838));
      case 'rejected':
        return _badge(
            'Rejected', const Color(0xFF3A0A0A), const Color(0xFFE84B4B));
      default:
        return _badge(
            'Required', const Color(0xFF2A1F5F), const Color(0xFF8B7FFF));
    }
  }

  String _monetizationSubtitle(String? status) {
    switch (status) {
      case 'approved':
        return 'Earning from your content';
      case 'pending':
        return 'Application under review';
      case 'rejected':
        return 'Application rejected — try again';
      default:
        return 'Apply to earn from your content';
    }
  }

  Widget? _monetizationBadge(String? status) {
    switch (status) {
      case 'approved':
        return _badge(
            'Active', const Color(0xFF1A9B5A), const Color(0xFF22C97A));
      case 'pending':
        return _badge(
            'Pending', const Color(0xFF3A2F0A), const Color(0xFFE8A838));
      case 'rejected':
        return _badge(
            'Rejected', const Color(0xFF3A0A0A), const Color(0xFFE84B4B));
      default:
        return null;
    }
  }

  Widget _badge(String label, Color bg, Color fg) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
      decoration:
          BoxDecoration(color: bg, borderRadius: BorderRadius.circular(6)),
      child: MyText(
          text: label,
          color: fg,
          fontsize: 10,
          fontwaight: FontWeight.w700,
          inter: 1,
          fontstyle: FontStyle.normal,
          maxline: 1,
          overflow: TextOverflow.ellipsis),
    );
  }

  Widget _revenueSummary(Color cardBg, Color textPrimary, Color textSub) {
    final revenue = _data['revenue_overview'] as Map? ?? {};
    if (revenue.isEmpty) return const SizedBox.shrink();
    final statements = revenue['statements'] as List? ?? [];
    final isPool = revenue['model'] == 'pool';
    return Container(
      width: double.infinity,
      padding: const EdgeInsets.all(18),
      decoration:
          BoxDecoration(color: cardBg, borderRadius: BorderRadius.circular(16)),
      child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
        Text('Your revenue',
            style: TextStyle(
                color: textPrimary, fontSize: 17, fontWeight: FontWeight.w700)),
        const SizedBox(height: 8),
        Text(
            isPool
                ? 'Artists share ${revenue['artist_pct']}% of reconciled monthly revenue. Your earnings depend on eligible stream credits, not a fixed price per listen.'
                : 'Fixed-rate earnings are active for approved monetized credits.',
            style: TextStyle(color: textSub)),
        const SizedBox(height: 14),
        Text(
            '${_fmt(revenue['current_credits'])} credits this month · awaiting settlement',
            style: TextStyle(color: textPrimary)),
        const SizedBox(height: 6),
        Text(
            'Held for withdrawal: ${_fmtMoney(revenue['held'])}\nMarked paid by admin: ${_fmtMoney(revenue['paid'])}',
            style: TextStyle(color: textSub)),
        const SizedBox(height: 12),
        Text(
            'Withdrawal requires approved KYC and monetization, ${_fmt(revenue['min_streams'])} credits and ${_fmtMoney(revenue['min_earned'])} earned. Minimum request: ${_fmtMoney(revenue['min_withdrawal'])}.',
            style: TextStyle(color: textSub)),
        if (statements.isNotEmpty) ...[
          const SizedBox(height: 14),
          Text('Latest statement · ${statements.first['month']}',
              style:
                  TextStyle(color: textPrimary, fontWeight: FontWeight.w600)),
          Text(
              '${_fmtMoney(statements.first['earned'])} · ${_fmt(statements.first['credits'])} eligible credits',
              style: TextStyle(color: brandGreen)),
        ],
        const SizedBox(height: 10),
        TextButton(
            onPressed: () =>
                _openPortal('earnings', title: 'Statements & earnings'),
            child: const Text('View statements & payout history')),
      ]),
    );
  }

  Widget _earningsCard(bool isDark, Color cardBg, Color textPrimary,
      Color textSub, String? monetizationStatus) {
    final approved = monetizationStatus == 'approved';
    final pending = monetizationStatus == 'pending';
    final balance = _fmtMoney(_data['available_balance'] ?? 0);

    String valueText;
    String labelText;
    if (approved) {
      valueText = balance;
      labelText = 'Balance';
    } else if (pending) {
      valueText = '…';
      labelText = 'Under Review';
    } else {
      valueText = 'Apply';
      labelText = 'Monetization';
    }

    return Expanded(
      flex: 2,
      child: Container(
        padding: const EdgeInsets.symmetric(vertical: 14, horizontal: 14),
        decoration: BoxDecoration(
          color: cardBg,
          borderRadius: BorderRadius.circular(14),
          border: Border.all(
              color: isDark
                  ? white.withValues(alpha: 0.07)
                  : black.withValues(alpha: 0.06)),
        ),
        child: Row(
          children: [
            Container(
              width: 36,
              height: 36,
              decoration: BoxDecoration(
                  color: const Color(0xFF2F2A1A),
                  borderRadius: BorderRadius.circular(10)),
              child: Icon(
                approved
                    ? Icons.account_balance_wallet_rounded
                    : pending
                        ? Icons.hourglass_top_rounded
                        : Icons.monetization_on_rounded,
                color: const Color(0xFFE8A838),
                size: 18,
              ),
            ),
            const SizedBox(width: 10),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  MyText(
                      text: valueText,
                      color: textPrimary,
                      fontsize: 16,
                      fontwaight: FontWeight.w700,
                      inter: 1,
                      fontstyle: FontStyle.normal,
                      maxline: 1,
                      overflow: TextOverflow.ellipsis),
                  const SizedBox(height: 2),
                  MyText(
                      text: labelText,
                      color: textSub,
                      fontsize: 10,
                      fontwaight: FontWeight.w400,
                      inter: 1,
                      fontstyle: FontStyle.normal,
                      maxline: 1,
                      overflow: TextOverflow.ellipsis),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _action(
    bool isDark,
    Color cardBg,
    Color textPrimary,
    Color textSub, {
    required IconData icon,
    required Color iconBg,
    required Color iconColor,
    required String title,
    required String sub,
    required VoidCallback onTap,
    Widget? badge,
  }) {
    return GestureDetector(
      onTap: onTap,
      child: Container(
        padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 13),
        decoration: BoxDecoration(
          color: cardBg,
          borderRadius: BorderRadius.circular(14),
          border: Border.all(
              color: isDark
                  ? white.withValues(alpha: 0.07)
                  : black.withValues(alpha: 0.06)),
        ),
        child: Row(
          children: [
            Container(
              width: 40,
              height: 40,
              decoration: BoxDecoration(
                  color: iconBg, borderRadius: BorderRadius.circular(11)),
              child: Icon(icon, color: iconColor, size: 20),
            ),
            const SizedBox(width: 12),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  MyText(
                      text: title,
                      color: textPrimary,
                      fontsize: 13,
                      fontwaight: FontWeight.w600,
                      inter: 1,
                      fontstyle: FontStyle.normal,
                      maxline: 1,
                      overflow: TextOverflow.ellipsis),
                  const SizedBox(height: 1),
                  MyText(
                      text: sub,
                      color: textSub,
                      fontsize: 11,
                      fontwaight: FontWeight.w400,
                      inter: 1,
                      fontstyle: FontStyle.normal,
                      maxline: 2,
                      overflow: TextOverflow.ellipsis),
                ],
              ),
            ),
            const SizedBox(width: 8),
            if (badge != null)
              badge
            else
              Icon(Icons.arrow_forward_ios_rounded, color: textSub, size: 13),
          ],
        ),
      ),
    );
  }
}
