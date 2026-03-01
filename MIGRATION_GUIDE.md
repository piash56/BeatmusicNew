# MongoDB to Laravel (MySQL) Migration Guide

This guide explains how to safely migrate data from your old Next.js MongoDB database into the new Laravel application without losing or mismatching data.

---

## Easiest & Safest Way (Quick Steps)

1. **Export MongoDB from your Next.js app**  
   Use `mongoexport` or your app’s export so each collection is one JSON file. Name them like `soundwave.users.json`, `soundwave.tracks.json` and put them in a folder (e.g. `MongoDB-Database/` in the Laravel project root).

2. **Backup your Laravel database**  
   If you already have data in MySQL, run:
   ```bash
   mysqldump -u your_user -p your_database > backup_$(date +%Y%m%d_%H%M).sql
   ```
   Or use your host’s backup tool. Do not skip this.

3. **Run migration on a copy first (recommended)**  
   Use a **staging/local** copy of the Laravel app and database. Run the import there, verify counts and spot-check data, then do the same on production when satisfied.

4. **Dry run (no writes)**  
   See what would be imported without inserting anything:
   ```bash
   php artisan migrate:from-mongo --dry-run
   ```

5. **Run the real migration**  
   From the Laravel project root (with `MongoDB-Database/` containing the JSON files):
   ```bash
   php artisan migrate:from-mongo
   ```
   Or with a custom folder:
   ```bash
   php artisan migrate:from-mongo --path=path/to/your/mongodb-export
   ```

6. **Verify**  
   Compare record counts (users, tracks, etc.) in MySQL with the MongoDB export. Spot-check a few users and tracks. Fix any path differences for uploaded files (see “After Migration” below).

**To avoid mismatch or data loss:**  
- Do not edit the JSON export files.  
- Run once into an empty or dedicated DB, or ensure the command skips/updates by email (users) so you don’t duplicate.  
- Keep the MongoDB export folder as read-only backup.

---

## Clearing migrated data (to re-run without duplicates)

If the import failed partway or you need to fix data and re-run, do **not** run `migrate:from-mongo` again on top of existing data—that would create duplicate tracks and can cause conflicts.

**Safe way to reset and re-run:**

1. **Back up the database** (see above).
2. **Clear only the data that the migration writes** (users and tracks):
   ```bash
   php artisan migrate:from-mongo-clear
   ```
   You will be asked to confirm. Use `--force` to skip the prompt (e.g. in scripts).
3. **Run the import again:**
   ```bash
   php artisan migrate:from-mongo
   ```

The clear command deletes **all** rows from `tracks` and `users` (in that order). Use it only on a development/staging copy or right after a backup. If you have other tables that reference `users` (e.g. tickets, payouts), those rows will be left with orphaned user IDs after clearing; for a full reset you would need to clear those tables too or use a fresh database.

---

## Original dates (created_at / updated_at)

The migration keeps the **original** `createdAt` and `updatedAt` from MongoDB. After inserting each user and track, it updates the row with the parsed dates from the JSON, so submitted / created / updated dates in Laravel match the old data.

---

## What gets migrated (full list)

All collections in `MongoDB-Database/` are imported; none are skipped. Empty files (e.g. `vevorequests`, `subscriptions`, `vouchers`, `paymentmethods`, `presavecampaigns`, `uploadedfiles`) are read and import 0 rows.

| MongoDB collection (file) | Laravel table(s) |
|---------------------------|------------------|
| soundwave.users | users |
| soundwave.tracks | tracks |
| soundwave.radionetworks | radio_networks |
| soundwave.concertlives | concert_lives |
| soundwave.playlistsubmissions | playlist_submissions |
| soundwave.vevos | vevo_accounts |
| soundwave.payouts | payouts |
| soundwave.tickets | tickets + ticket_replies |
| soundwave.concertliverequests | concert_live_requests |
| soundwave.radiopromotions | radio_promotions |
| soundwave.pricingplans | pricing_plans |
| soundwave.sitesettings | site_settings |
| soundwave.faqs | faqs |
| soundwave.testimonials | testimonials |
| soundwave.knowledgebases | knowledge_bases |
| soundwave.vevorequests | vevo_requests |
| soundwave.subscriptions | subscriptions |
| soundwave.paymentmethods | payment_methods |
| soundwave.presaves | pre_saves |
| soundwave.vouchers | vouchers |

Import order is chosen so that foreign keys (user_id, track_id, plan_id, etc.) resolve correctly. **Dates** (`createdAt`, `updatedAt`, and other date fields) are taken from the MongoDB export and written as-is so the actual dates are preserved.

---

## Passwords (admin and artist login)

MongoDB user documents store bcrypt hashes (e.g. `$2a$10$...`). The migration **preserves these hashes**: it does not re-hash them. Laravel’s `User` model normally casts `password` to `hashed`, which would hash an existing hash again; the command avoids that by writing the original hash directly to the database after creating the user. **Old passwords continue to work** for both admin and artist users after migration.

---

## Step-by-step: Local first, then production

### Phase 1 – On your local / development machine

1. **Put the MongoDB export in the Laravel project**  
   Copy your exported JSON files into a folder named `MongoDB-Database` in the Laravel project root. Files should be named like `soundwave.users.json`, `soundwave.tracks.json`, etc.

2. **Use a clean Laravel database (recommended)**  
   Either use a separate database (e.g. `beat_music_dev`) or run migrations fresh **only if** you have no important data:
   ```bash
   php artisan migrate:fresh
   ```
   If you already have data, skip this and rely on backups.

3. **Run the migration that widens track text columns (required for MySQL)**  
   This avoids "Data too long for column" errors when importing long text (e.g. featuring_artists, authors, composers):
   ```bash
   php artisan migrate
   ```
   Ensure the migration `expand_tracks_text_columns_for_migration` runs (it changes some `tracks` columns to TEXT).

4. **Dry run (no database writes)**  
   Check that users and tracks are detected and that user–track links resolve. You should see 0 tracks skipped:
   ```bash
   php artisan migrate:from-mongo --dry-run
   ```
   You should see: "Users imported. Map size: ..." and "Tracks imported. Map size: ..." with no (or very few) skipped tracks.

5. **Run the real import on local**  
   ```bash
   php artisan migrate:from-mongo
   ```

6. **Verify on local**  
   - Compare counts: users in DB vs lines in `soundwave.users.json`; tracks in DB vs `soundwave.tracks.json`.  
   - Spot-check a few users and tracks in the Laravel app (login, view release, etc.).  
   - If anything is wrong, fix the command or data and repeat from step 4 on a fresh DB.

### Phase 2 – Production

7. **Backup production database**  
   ```bash
   mysqldump -u your_user -p your_database > backup_$(date +%Y%m%d_%H%M).sql
   ```
   Or use your host's backup tool.

8. **Run new migrations on production**  
   So that the tracks table has the expanded TEXT columns:
   ```bash
   php artisan migrate
   ```

9. **Run the import on production**  
   Either copy the same `MongoDB-Database` folder to the server or point to it:
   ```bash
   php artisan migrate:from-mongo
   ```
   Or with a custom path:
   ```bash
   php artisan migrate:from-mongo --path=path/to/MongoDB-Database
   ```

10. **Verify on production**  
    Same as step 6: counts and spot-checks. If something is off, restore from the backup and fix before re-running.

**Summary:** Do the full cycle (dry run → import → verify) on local first. Only run the import on production when local results are correct and you have a backup.

---

## Why were tracks skipped in dry run? (fixed)

Previously, in dry run the command did not store the mapping from MongoDB user `_id` (ObjectId) to Laravel user id. Tracks reference users by that ObjectId (`userId`). So when resolving "which Laravel user does this track belong to?", the lookup failed and the track was skipped. The command was updated so that in dry run it also maps each user's MongoDB `_id` to a placeholder. With that, tracks resolve their user and are no longer skipped (dry run shows the same track count as the real run).

---

## Overview

- **Source:** MongoDB export (JSON files in `MongoDB-Database/`)
- **Target:** Laravel app using MySQL (existing migrations)
- **Strategy:** Read JSON → parse MongoDB extended JSON (`$oid`, `$date`) → map field names (camelCase → snake_case) → resolve foreign keys via ID mapping → insert in dependency order

## Safety Checklist (Do Before Migrating)

1. **Backup your current Laravel database**
   ```bash
   php artisan db:backup
   # or manually:
   mysqldump -u your_user -p your_database > backup_$(date +%Y%m%d).sql
   ```

2. **Keep the MongoDB export as-is**  
   Do not modify the JSON files in `MongoDB-Database/`. They are your source of truth.

3. **Run on a copy first**  
   Prefer running the migration on a staging/local copy of the Laravel app and DB, then verify data before running on production.

4. **Optional: Start with empty tables**  
   If this is a fresh Laravel install and you want to *replace* seed data with MongoDB data, you can run migrations fresh (this will drop all tables). Only do this if you have no important data in Laravel yet.
   ```bash
   php artisan migrate:fresh   # WARNING: drops all tables
   ```

## Migration Order (Dependencies)

Data must be imported in this order so that foreign keys exist:

| Order | MongoDB collection (file)        | Laravel table(s)           | Depends on                    |
|-------|----------------------------------|----------------------------|-------------------------------|
| 1     | soundwave.users                  | users                      | —                             |
| 2     | soundwave.tracks                 | tracks                     | users                         |
| 3     | soundwave.concertlives           | concert_lives              | users (created_by)            |
| 4     | soundwave.radionetworks          | radio_networks             | —                             |
| 5     | soundwave.playlistsubmissions    | playlist_submissions       | users, tracks                 |
| 6     | soundwave.payouts               | payouts                    | users                         |
| 7     | soundwave.tickets                | tickets, ticket_replies    | users                         |
| 8     | soundwave.vevos                  | vevo_accounts              | users                         |
| 9     | soundwave.concertliverequests    | concert_live_requests      | users, concert_lives          |
| 10    | soundwave.radiopromotions        | radio_promotions           | users, tracks, radio_networks |
| 11    | soundwave.sitesettings           | site_settings              | users (last_updated_by)       |
| 12    | soundwave.faqs                  | faqs                       | —                             |
| 13    | soundwave.testimonials           | testimonials               | —                             |
| 14    | soundwave.knowledgebases         | knowledge_bases            | users (created_by)            |
| 15    | soundwave.pricingplans           | pricing_plans               | —                             |
| 16    | soundwave.subscriptions          | subscriptions              | users, pricing_plans          |
| 17    | soundwave.vouchers               | vouchers                   | —                             |

**Not migrated by the command (optional / different schema):**  
`vevorequests` (empty in your export), `presaves`, `presavecampaigns`, `uploadedfiles`, `paymentmethods`, `sitesettings` (handled). You can extend the command for these if needed.

## Field Mappings (Summary)

- **IDs:** MongoDB `_id` ($oid) is not used as primary key. Laravel uses auto-increment `id`. The command keeps an internal map `mongoId → newId` so that references like `userId`, `trackId` are translated when inserting.
- **Dates:** MongoDB `$date` is parsed to Carbon and written as Laravel timestamps.
- **Nested objects:** Flattened where the Laravel schema expects it, e.g.:
  - `stats.totalStreams` → `stats_total_streams`
  - `preferences.theme` → `preferences_theme`
  - `socialLinks.facebook` → `social_facebook`
- **User:** `fullName` → `full_name`, `isAdmin` → `is_admin`, `isVerified` → `is_verified`, etc.
- **Track:** `userId` → `user_id`, `audioFile` → `audio_file`, `primaryGenre` → `primary_genre` (and normalized to match Laravel enum, e.g. "pop" → "Pop" where applicable).
- **Ticket:** `user` → `user_id`. Embedded `replies[]` are inserted into `ticket_replies` with `ticket_id` and `user_id` from the map.

## How to Run the Migration

1. **Place your MongoDB export** in the project root directory in a folder named `MongoDB-Database`, with filenames like:
   - `soundwave.users.json`
   - `soundwave.tracks.json`
   - etc.

2. **Run the Artisan command**
   ```bash
   php artisan migrate:from-mongodb
   ```
   This will:
   - Read each JSON file from `MongoDB-Database/` (or path you pass)
   - Parse extended JSON
   - Map and insert in dependency order
   - Resolve foreign keys using the internal ID map

3. **Optional: custom path**
   ```bash
   php artisan migrate:from-mongodb --path=path/to/mongodb-export
   ```

4. **Optional: dry run**  
   If the command supports `--dry-run`, it will only report what would be done without inserting.

## After Migration

- **File paths:** MongoDB stores paths like `/uploads/covers/...` and `/uploads/tracks/...`. The migration command now normalizes these to Laravel format (`covers/...`, `tracks/...`). Place your cover images in `storage/app/public/covers/` and audio files in `storage/app/public/tracks/`. Ensure `php artisan storage:link` has been run so `public/storage` symlinks to `storage/app/public`. For existing migrated data with old paths, run:
  ```bash
  php artisan migrate:normalize-storage-paths
  ```
  Use `--dry-run` to preview changes first.
- **Passwords:** User passwords hashed with bcrypt in Node (e.g. `$2a$10$...`) are compatible with Laravel’s `Hash::check()`; no re-hashing is needed.
- **Verify:** Spot-check users, tracks, and related counts (playlist submissions, payouts, tickets, etc.) in the Laravel DB vs MongoDB export.

## Troubleshooting

- **Duplicate key / unique constraint:** Ensure you are not re-importing on top of existing data that already has the same business keys (e.g. email). Either migrate into an empty DB or add logic to skip/update existing rows.
- **Missing reference:** If a document references another (e.g. `userId`) that is not in the export, the command can skip that row or set the FK to null (depending on implementation). Check logs.
- **Enum / status values:** If Laravel uses different enum values (e.g. "Approved" vs "approved"), the command normalizes them; if you see errors, report the value and we can add a mapping.

---

The actual import logic lives in the Artisan command `App\Console\Commands\MigrateFromMongoDB` and uses `App\Services\MongoDBJsonParser` to parse the JSON files.
