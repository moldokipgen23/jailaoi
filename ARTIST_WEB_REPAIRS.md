# Artist web repair batch — 6 October 2026

Deployed 12 files with hash verification. Live source backups: codex-20261006T150520Z, codex-20261006T150652Z, codex-20261006T150841Z under /home/jailaoi-portal/repair-backups.

New KYC images use private local storage, and authorized admin document routes return no-store responses. Live read-only counts confirmed zero existing KYC records/files and zero legacy profile proof records; no existing customer documents needed migration. Legacy profile document uploads are blocked and the duplicate banking card is hidden. Login identity changes require a future verified flow rather than accepting unverified changes.

Payouts derive the destination from the latest approved KYC, validate bank/UPI detail structure, reject sub-paise requests, and recheck KYC under row lock before holding funds. Free-text request destinations cannot replace approved details. The form explains this and links to KYC.

Music changes preserve app play counts, premium and existing moderation status, mirror album name and landscape artwork, and write portal/app records within one database transaction. Mirror failures cannot produce a success response. Old media cleanup runs after a successful database save. Pre-upload filenames require user-scoped 24-hour cache proof; old upload sessions may need a fresh upload. WEBP artwork validation matches the wizard. Existing URL upload types are preserved.

Dashboard top tracks now uses music, chart labels say eligible stream credits, content labels distinguish radio/music, and a workspace card adds upload/manage/earnings actions and payout steps.

Validation: 67 tests / 184 assertions. Targeted tests cover private paths/no-store responses, traversal rejection, structured payout validation, forged destination rejection, wallet holds, and preserving play counts/premium/moderation. All six live pages (dashboard, music catalogue, upload, earnings, KYC, profile) rendered with read-only controller checks. The first catalogue rendering check lacked bound route context; corrected and repeated successfully. Wallet aggregate remained unchanged. No live payouts, uploads, KYC decisions or customer document reads were performed.

Remaining: artist browser login is required for signed-in visual/form testing. This batch does not add release drafts/review scheduling, a dedicated analytics page with date filters, notification inbox, historical catalogue linking, full KYC revision history, artist/public profile synchronization, or fully consolidated payout eligibility UX. Web password/session hardening also remains. No native Flutter artist management or Play Store release was changed in this batch.
