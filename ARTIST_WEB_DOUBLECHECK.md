# Artist web double-check — 6 October 2026

71 backend tests passed with 211 assertions. Expanded artist tests cover unchanged audio cleanup, forged content fields, another user's upload, another channel's track, transaction rollback when the mirror fails, moderation and play-count preservation, private KYC paths, payout detail structure, fractional-paise rejection, pending KYC rejection, duplicate withdrawal prevention, approved destination selection and password ownership/token revocation.

Additional deployed repairs:
- Canonical audio comes from the owned database record unless a validated uploaded replacement is supplied. Unchanged audio is not deleted. Switching from a URL to file audio requires an upload. Music edit targets are scoped by channel and content type.
- Recheck payout method as well as approved KYC data under lock. Earnings UI uses latest KYC and shows pending withdrawal, credit and settled-earnings thresholds before offering a request.
- Web password changes operate on the signed-in user rather than submitted IDs, require eight characters and revoke API tokens. Artist login regenerates its session; login/register/password-reset routes have rate limits.
- Unexpected music/KYC/earnings failures do not expose database errors in responses. Known validation failures remain readable. Missing artist profiles cannot silently report successful music synchronization.

Deployment backups: codex-20261006T151838Z and codex-20261006T152000Z under /home/jailaoi-portal/repair-backups. All installed hashes verified. Route and view caches cleared.

Live verification: all six artist pages render (dashboard, catalogue, upload, earnings, KYC, profile); wallet aggregate unchanged. Five private API probes return 401, invalid social proof returns 422, public artist listing returns 200. No live uploads, withdrawals, KYC decisions, financial entries or notifications were created in this double-check.

Visual limitation: Codex browser is still at /user/login. Signed-in visual, real-file upload/playback and form interaction checks remain pending artist sign-in. Controller rendering is not a substitute for those checks. No credentials were changed during verification (password tests use an in-memory test database).

Known remaining work: release drafts/review/scheduling, dedicated date-filtered analytics, notification inbox, historical catalogue linking, full KYC change history, public-artist/profile synchronization, user-controlled cancellation for withdrawals, and verification of provider media delivery. A vendor Sanctum PHP deprecation warning appears in CLI checks; functionality passes, but dependency compatibility needs a separate tested upgrade. Web password changes revoke API tokens and refresh the current session; revocation of all other web sessions is not claimed. Hashtag bookkeeping and filesystem orphan cleanup need broader failure/concurrency coverage. These tests do not prove every production scenario is covered.
