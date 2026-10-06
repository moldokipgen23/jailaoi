# Security and monetization rollout — 6 October 2026

Strict listener identity is deployed on portal.jailaoi.com. Private APIs require an unexpired listener session; submitted user IDs cannot select another account. Firebase social/phone login verifies server-side identity. Password changes revoke other sessions, and unverified phone fields cannot take over password/social accounts. Artist portal tokens require an approved active linked artist.

Flutter source fixes keep artist portal tokens from overwriting login tokens and revoke the current session on sign-out. These mobile changes still require an Android release and actual-device verification. Older clients may need an update and a fresh sign-in.

Signed-in admin verification completed: selected-period artist earnings, revenue-pool explanations, historical split disclosure, and separate Revenue & Settlements navigation. A June 2026 review was prepared without wallet credits. Zero eligible credits block approval. No financial entries or payouts were submitted.

Validation: 63 backend tests / 169 assertions; 3 Flutter token-policy tests; Flutter analysis has no errors (one existing style info). Live anonymous private API checks return 401, forged social login returns 422, public artists remain accessible. Android ADB has no connected device, so actual phone sign-in and ad delivery are not verified.

Confirmed ad statements remain manual entries; no ad-provider accounting feed is implemented. Client-reported stream time is not strong anti-fraud proof. Historical duplicate credit groups require reconciliation before affected payouts. These limitations prevent claiming full production readiness.

Live source backups: codex-20261006T141542Z, codex-20261006T141906Z, codex-20261006T142412Z under /home/jailaoi-portal/repair-backups.
