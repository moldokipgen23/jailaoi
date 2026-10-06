# Revenue review and artist statements — 6 October 2026

Deployed to portal.jailaoi.com. Monthly scheduling now prepares a review and never credits wallets automatically. Super-admin approval is required. Existing balances and historical settlements were not changed.

## Implemented
- One shared monthly revenue calculation for the admin preview and approved settlement.
- Paid Cashfree orders matched to user/package/amount. Previously paid purchases remain revenue after subscription expiry. Recorded recurring subscription charges are matched to the subscription account and plan amount.
- Explicit INR ad-income and reconciled subscription entries with unique references and accounting notes; refund, payment-fee and tax deductions. Provider statements and bank receipts still require manual reconciliation; this is not an automated ad-provider accounting feed.
- Unmatched/duplicate payment records and duplicate daily artist stream credits block approval. Legacy music types 3/8 are normalized for duplicate checks. Suspended/inactive/unapproved artists are excluded.
- Integer-paise largest-remainder allocation preserves the exact pool total. Wallet credits, ledger settlement and artist statements are one transaction. Stale snapshots and repeated approval fail closed. Historical re-settlement is disabled.
- Admin month selector, source totals, adjustment audit trail, blockers, allocation table, explicit reconciliation confirmation and settlement history. Finance roles can read but cannot mutate revenue or approve settlements.
- Artist web statements, current unsettled credits, held withdrawals, marked-paid totals and current payout requirements. All earnings explanations use current configuration instead of hardcoded 55%/fixed-rate claims.
- Artist API returns the same overview. Recent uploads combine music, radio and podcast content. Podcast uploads appear in analytics but podcast earnings are still excluded by the current earning policy.
- Flutter source includes revenue summary and statement link, preserves suspension fields, shows retryable dashboard errors instead of false zero balances, and uses the brand gradient. It requires a new Android release; the currently published app has not been replaced.

## Verification
47 backend tests / 142 assertions passed. Flutter analysis passed with no errors and one existing style information warning. Live admin and artist views rendered; artist dashboard API returned success; aggregate wallet balances were unchanged. Deployment file hashes verified. Additive migration created three tables. Pre-deployment encrypted backup succeeded. Server source recovery snapshot: /home/jailaoi-portal/repair-backups/codex-20261006T140250Z.

## Remaining production gates
- Mandatory app identity enforcement must be deployed with a compatible Android release. Revenue approvals currently fail closed until services.revenue.identity_enforced is enabled alongside that middleware, never before it.
- Stronger playback evidence/fraud controls and reconciliation of existing duplicate stream credits remain required. App-reported elapsed listening is not sufficient proof of real listening.
- No artist has approved monetization or KYC in the live aggregate check. No eligible September credits existed in the live preview; no settlement or payout was executed.
- Actual ad fill/delivery and notification delivery need credentials and a release/device test.
- Browser visual review was blocked at admin login. Server rendering and source checks do not substitute for signed-in mobile/desktop visual QA. Product Design screenshot audit was not completed.
- No full database restore test or automatic off-server backup feed is configured. Bunny work deferred by user.

## Operating workflow
1. Open /admin/earnings/settlement and choose a completed month.
2. Reconcile provider/bank totals, add confirmed ad income and documented deductions. Unmatched transaction references are displayed for investigation; do not count a duplicate payment as new income.
3. Calculate/refresh review. Resolve all blockers and inspect artist allocations.
4. Super-admin confirms reconciliation and approves once. Approved statements and credits are recorded atomically; this does not send a bank payment.
5. Review artist withdrawals separately and mark paid only after the real payout is confirmed.
