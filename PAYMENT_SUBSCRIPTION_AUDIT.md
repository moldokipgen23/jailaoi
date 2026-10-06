# Payments and subscriptions — 7 October 2026

## Live findings

- Cashfree is the only visible gateway, in production mode. Saved credentials authenticate successfully against existing orders.
- Auto-renew toggle is off. No recurring subscription records exist. Current subscription purchases are one-time prepaid access, not automatic renewal.
- All 12 historical Cashfree orders were checked with the provider: two PAID, ten EXPIRED. Both paid orders had matching existing purchase records. Their ledgers incorrectly remained created; one legacy transaction used the plan title rather than the Cashfree payment-source description.
- Both paid ledgers are reconciled using authenticated provider lookups and account/package/amount checks. No new purchase, charge, access extension, artist credit or payout was created. Original expired access dates and statuses are retained. Private original-row backups are outside the document root.
- Family plans are active and Flutter advertises five devices. No family membership or five-device enforcement was found in the inspected application. This must be implemented or the plans withdrawn before advertising family sharing.

## Backend repairs deployed

- Cashfree millisecond webhook timestamps are normalized for the replay-age check; the original timestamp remains unchanged in signature computation. Seconds-form requests remain supported. Stale, malformed and forged requests are rejected.
- Recurring credits require SUCCESS, a stable provider payment ID, the exact agreed amount and INR. No synthetic authorization ID can produce a second first-payment credit; refundable authorization is not revenue.
- New recurring subscriptions respect the disabled setting, validate IDs/positive prices/supported intervals, and cannot overwrite another subscription ledger entry.
- Authenticated subscription-status endpoint reports owned, recorded and unexpired paid access. Checkout completion alone does not activate access.
- Subscription history exposes renewal state, including customer cancellation/pause. Cancellation does not revoke already-paid access.
- Paid-ledger retries repair legacy source/status fields only after provider verification and matching amount; existing entitlement is retained.
- Shared expiry calculation preserves remaining paid time and handles month-end/leap-year dates without calendar overflow.
- Disabled packages are omitted from the purchase catalogue.

## Flutter source repairs — Android release required

- Cashfree checkout no longer requires the gateway secret hidden by the API. Server-created session IDs remain the checkout credential.
- Recurring completion checks the authenticated backend before showing activation, and refreshes profile premium state after confirmation.
- Network failures in creation, cancellation, verification and purchase credit produce usable errors and stop loading.
- History uses active entitlement status and safe date parsing; cancelled renewal mandates no longer offer another cancellation button.
- Unsupported Google/Apple receipt verification no longer automatically returns true. Store checkout remains unavailable until genuine server-side receipt verification is configured. No Google/Apple purchase should be enabled with this placeholder integration.

## Validation

- Backend: 109 tests / 328 assertions pass, including authentic millisecond callbacks, forged/stale rejection, duplicate first-charge events, separate renewals, bad amounts/currency/status/IDs, cancellation, expiry, account isolation, month-end handling and legacy paid-ledger repair.
- Flutter: seven payment failure/session/provider regression tests pass. Analysis has no errors or new warnings; one pre-existing style info remains in musicdetails.dart.
- Live: signed synthetic unknown event returns 200; forged signature returns 401; unknown owned subscription returns 404; the new route is registered. These synthetic callbacks create no purchases.
- Source hashes verified on deployment. Source recovery snapshots: codex-20261006T190825Z and codex-20261006T191046Z under /home/jailaoi-portal/repair-backups.
- Debug Android APK built successfully with native payment SDK integration. Artifact: Jailaoi app/build/app/outputs/flutter-apk/app-debug.apk. Dependency deprecation/Java 8 compilation warnings remain; no build errors. This is a debug build, not a published Play Store release.

## Remaining production checks

- No phone is connected. Actual released Play Store binary, checkout UI, cancellation and reinstall/relogin behavior are not verified on a device. Local fixes do not update the Play Store app.
- Cashfree sandbox credentials were not supplied. Real sandbox checkout, initial authorization, scheduled renewal, asynchronous retries and merchant webhook dashboard configuration remain unverified. Live configuration was not switched to sandbox and recurring remains off.
- Refunds/disputes are not automatically ingested and tied to entitlement revocation. Revenue refunds/fees/taxes currently need documented admin reconciliation; automated accounting must not be claimed.
- Merchant approval for Cashfree subscriptions must be confirmed before enabling auto-renew.
- Google Play distribution requires a compliant billing route: Play Billing or applicable approved alternative-billing participation. Cashfree credentials alone do not establish enrollment. Do not enable unverified store checkout.
- Artist revenue accounting tests pass, but bank/provider reconciliation and admin settlement approval remain required; no real settlement or payout was run.

References: https://www.cashfree.com/docs/payments/online/webhooks/signature-verification ; https://www.cashfree.com/docs/payments/subscription/webhooks ; https://support.google.com/googleplay/android-developer/answer/9858738 .
