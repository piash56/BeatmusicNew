## 1. Editorial Playlists

### 1.1 Overview

Artists submit their **released tracks** to **editorial playlists** (Spotify, Apple Music, Amazon Music). Each submission goes through statuses: **Waiting → Processing → Published** or **Rejected**. Admins manage submissions and can set **streams** (and optionally listeners) when status is Published. **Admins also manage the list of editorial playlists** (add, edit, delete playlist name and link per platform) via a dedicated admin page.

### 1.2 Data Model

**Table: `editorial_playlists`** (admin-managed list of playlists per platform)

| Column       | Type / Constraint |
|--------------|-------------------|
| id           | bigint PK         |
| platform     | string, required: `Spotify`, `Apple Music`, or `Amazon Music` |
| name         | string, required (display name of the playlist) |
| url          | string, required (full URL to the playlist)    |
| sort_order   | int, default 0 (optional; for ordering in dropdowns) |
| is_active    | boolean, default true |
| created_at, updated_at | timestamps |

- Index: `(platform)`, unique `(platform, name)` so the same name cannot be duplicated per platform.
- **Admin must be able to:** list (filter by platform), add new (platform + name + url), edit (name, url, is_active, sort_order), delete.

**Table: `playlist_submissions`**

| Column           | Type / Constraint |
|------------------|-------------------|
| id               | bigint PK         |
| user_id          | FK users, required |
| track_id         | FK tracks, required |
| platform         | enum/string: `Spotify`, `Apple Music`, `Amazon Music` |
| playlist_name    | string, required  |
| playlist_url     | string, required  |
| status           | enum: `Waiting`, `Processing`, `Published`, `Rejected`; default `Waiting` |
| submission_date  | datetime, default now |
| review_date      | datetime, nullable |
| review_note      | text, nullable    |
| listeners        | int unsigned, default 0 |
| streams          | int unsigned, default 0 |
| created_at, updated_at | timestamps |

- **Source of truth for available playlists:** the `editorial_playlists` table. When an artist submits, validate that a row exists in `editorial_playlists` with that `platform` and `name` (and `is_active = true`), and use that row's `url` for `playlist_url`.

### 1.3 Default / Seed Editorial Playlists

Seed the `editorial_playlists` table with the following data so the app works out of the box. Admin can later add, edit, or delete entries.

**Spotify:** EQUAL Italia, Fresh Finds Italia, GENERAZIONE Z, Indie Italia, Indie Triste, Indimenticabili, Int'o Rione, Napoli Centro, New Music Friday Italia, Novità Pop, Novità Rap Italiano, nuovo pop IT, Pop italiano: video, Plus Ultra, RADAR Italia, ragazzo triste, Raptopia, Rock Italia, sanguegiovane, Scuola Indie, Street Culto.

**Apple Music:** OnRepeat, A-List Pop, Alpha, ALT CTRL, High Maintenance, New in Indie, New Music Daily, Puro Pop, R&B Rewind, Rap Life, Sexyy Red: Hood Summer, Todays Hits, Viral Hits.

**Amazon Music:** Acoustic Chill, All Hits, Buona Giornata, Cantautori Italiani, Eplosione Indie, Flow Italiano, La vita è pop!, Metropolitalia, Nectar, Nuova Gen, Platino, Pop Culture, Rock Arena, Top Hits Oggi.

Full URLs for each are in the reference app (`playlistSubmissionModel.js` EDITORIAL_PLAYLISTS). Create a **Seeder** (e.g. `EditorialPlaylistSeeder`) that inserts all of them into `editorial_playlists`; run it from `DatabaseSeeder`. Laravel implementation should copy the exact name + url pairs from the reference or from the table below.

**Spotify (name → url):**

- EQUAL Italia → https://open.spotify.com/playlist/37i9dQZF1DWUHxBb0SYtLj?si=gBnBM9VORja9uvXl_bHjVw&pi=kLE4-um3Smuxl
- Fresh Finds Italia → https://open.spotify.com/playlist/37i9dQZF1DX0KBgD4Jf5tY?si=8lPJhEE3TqGtWwB2z4QsUw&pi=nV21DhsARp62v
- GENERAZIONE Z → https://open.spotify.com/playlist/37i9dQZF1DWYCIYGXn56uz?si=IC7VbLGEQXSnDXeSbDc8rQ&pi=Bfszka9xT96sN
- Indie Italia → https://open.spotify.com/playlist/37i9dQZF1DX6PSDDh80gxI?si=Fj_sjbdWTKC3rVBsvgBQVA&pi=WBsds_5dTUOIJ
- Indie Triste → https://open.spotify.com/playlist/37i9dQZF1DWX21Ue9Rttn8?si=67Q75VxnQlSNd1hnW1TO8w&pi=vK5c0_UzTfK8V
- Indimenticabili → https://open.spotify.com/playlist/37i9dQZF1DX6ShdbyN9CkW?si=WocbpIlMQKqVADNKa6uBxA&pi=SCdbodN6RC2yz
- Int'o Rione → https://open.spotify.com/playlist/37i9dQZF1DWYrg01Xmlew6?si=A_0TZfc0TG-NpeZbWL4qoQ&pi=emHcqqfoQZiyj
- Napoli Centro → https://open.spotify.com/playlist/37i9dQZF1DX6gvUCtw1XOD?si=CMObif1GRCqV73KGk09GQg&pi=WI35Wse_QdG8e
- New Music Friday Italia → https://open.spotify.com/playlist/37i9dQZF1DWVKDF4ycOESi?si=BNqgmAQSQkyUXv0w8s3DnQ&pi=IizFMfogQMmL1
- Novità Pop → https://open.spotify.com/playlist/37i9dQZF1DX6K3mlB5G3WG?si=ceCHmPbESfeSwyvUP78H2A&pi=AWoBhTIZRJGM7
- Novità Rap Italiano → https://open.spotify.com/playlist/37i9dQZF1DX1OQlaot30zi?si=3SdocN_jQUuPF5bGb2Yrpg&pi=brnsFoUuT0i14
- nuovo pop IT → https://open.spotify.com/playlist/37i9dQZF1DX2c7QgpQBJFr?si=hD4ANM8AQTO3iTM-jqTsnQ&pi=FRsZ_IhyTfW8a
- Pop italiano: video → https://open.spotify.com/playlist/37i9dQZF1DWZ5HtCdI1TCV?si=_UQAm7LKSCWn3KsKlJTw6g&pi=V3uB7X6_RM6hn
- Plus Ultra → https://open.spotify.com/playlist/37i9dQZF1DX14EWeH2Pwf3?si=pZyAbboyTlGlbxUVkZfTTA&pi=z4suWburQnqL-
- RADAR Italia → https://open.spotify.com/playlist/37i9dQZF1DWVjDgOMO8jZl?si=QACiAK6FQXS_HNDBBaDaEA&pi=IHaNxhanRv6t6
- ragazzo triste → https://open.spotify.com/playlist/37i9dQZF1DX7JWqNxz28IX?si=PhquaegtSMuS74-a2IzqOQ&pi=UUgZJjxvSJGqW
- Raptopia → https://open.spotify.com/playlist/37i9dQZF1DWUQru3jd69v5?si=MBKq8_SwT1ibXyygZIZDgw&pi=XQNSpvSURlim_
- Rock Italia → https://open.spotify.com/playlist/37i9dQZF1DWViUlcvfltyZ?si=_oGgw4gLSC2f9FDbMy2pMA&pi=kX9ED22VSay6p
- sanguegiovane → https://open.spotify.com/playlist/37i9dQZF1DWW9tK1GiTdMf?si=qUTN2JVtR7y0LyBn6HfTOw&pi=fdSHZ-FcQQmwV
- Scuola Indie → https://open.spotify.com/playlist/37i9dQZF1DX6O5gXioqvYB?si=fHlwsQvWTjuxBqtu4gTEcA&pi=0pWW0jDUTQCnv
- Street Culto → https://open.spotify.com/playlist/37i9dQZF1DWXU2naFUn37x?si=OlL3MYARTzquTS4AOf3tng&pi=RhvwIeEYTNKER

**Apple Music (name → url):**

- OnRepeat → https://music.apple.com/us/playlist/onrepeat/pl.426a1044619f47d6b1f86b3f79ecf857
- A-List Pop → https://music.apple.com/us/playlist/a-list-pop/pl.5ee8333dbe944d9f9151e97d92d1ead9
- Alpha → https://music.apple.com/us/playlist/alpha/pl.bcb2f44b6e194cfa8950a796b4e65cd1
- ALT CTRL → https://music.apple.com/us/playlist/alt-ctrl/pl.0b593f1142b84a50a2c1e7088b3fb683
- High Maintenance → https://music.apple.com/us/playlist/high-maintenance/pl.8573a92705fe4253af597147d7cb981f
- New in Indie → https://music.apple.com/us/playlist/new-in-indie/pl.dbc3a7bd6b4843cb830af1b7cbbadbd6
- New Music Daily → https://music.apple.com/us/playlist/new-music-daily/pl.2b0e6e332fdf4b7a91164da3162127b5
- Puro Pop → https://music.apple.com/us/playlist/puro-pop/pl.2754a2d6e0084e2b8106fcd35fef6492
- R&B Rewind → https://music.apple.com/us/playlist/r-b-rewind/pl.efaf877db72a4c05b2654eb4371d6c24
- Rap Life → https://music.apple.com/us/playlist/rap-life/pl.abe8ba42278f4ef490e3a9fc5ec8e8c5
- Sexyy Red: Hood Summer → https://music.apple.com/us/playlist/sexyy-red-hood-summer/pl.485634cb956b4366b989a7373e168ce7
- Todays Hits → https://music.apple.com/us/playlist/todays-hits/pl.f4d106fed2bd41149aaacabb233eb5eb
- Viral Hits → https://music.apple.com/us/playlist/viral-hits/pl.3de89e62aa3340038e08fa325c3f3f01

**Amazon Music (name → url):**

- Acoustic Chill → https://music.amazon.it/playlists/B07ZG923HX?marketplaceId=APJ6JRA9NG5V4&musicTerritory=IT&ref=dm_sh_wkp1XwZOJrJvRWI7pHpfRlRdO
- All Hits → https://music.amazon.it/playlists/B07QY219HM?marketplaceId=APJ6JRA9NG5V4&musicTerritory=IT&ref=dm_sh_OWKTE9enCValXLfEYyOGpw471
- Buona Giornata → https://music.amazon.it/playlists/B076TDPYC1?marketplaceId=APJ6JRA9NG5V4&musicTerritory=IT&ref=dm_sh_LjAkfYd1FZISCwkqulgBdQWqk
- Cantautori Italiani → https://music.amazon.it/playlists/B07CT4KQ8T?marketplaceId=APJ6JRA9NG5V4&musicTerritory=IT&ref=dm_sh_Dq8HOc8LQ6Zck7F3Sv1GQrAJO
- Eplosione Indie → https://music.amazon.it/playlists/B074Q1DJ14?marketplaceId=APJ6JRA9NG5V4&musicTerritory=IT&ref=dm_sh_JGzbLfKqrHeZLXhp10s4il36Y
- Flow Italiano → https://music.amazon.it/playlists/B07RG14PWR?marketplaceId=APJ6JRA9NG5V4&musicTerritory=IT&ref=dm_sh_bSgXzgKF5XHogA6Yy1jxJFUjO
- La vita è pop! → https://music.amazon.it/playlists/B07RYVSC2H?marketplaceId=APJ6JRA9NG5V4&musicTerritory=IT&ref=dm_sh_N17KtwBKqUDvj8FkGj57ZQzLR
- Metropolitalia → https://music.amazon.it/playlists/B083P65WB4?marketplaceId=APJ6JRA9NG5V4&musicTerritory=IT&ref=dm_sh_2bFhdrGr1QDj5c8ZI8wA32Oyb
- Nectar → https://music.amazon.it/playlists/B07G4LLBXM?marketplaceId=APJ6JRA9NG5V4&musicTerritory=IT&ref=dm_sh_ihNImkTXxdlqYKSTnxRp5y7UV
- Nuova Gen → https://music.amazon.it/playlists/B091JDCVX6?marketplaceId=APJ6JRA9NG5V4&musicTerritory=IT&ref=dm_sh_hsJxGE6A1f45uMlWK7mjg7KSk
- Platino → https://music.amazon.it/playlists/B07D9XQL74?marketplaceId=APJ6JRA9NG5V4&musicTerritory=IT&ref=dm_sh_eQVvA9y4stn0aUvS9WDYPLRqK
- Pop Culture → https://music.amazon.it/playlists/B07H4ZPCX1?marketplaceId=APJ6JRA9NG5V4&musicTerritory=IT&ref=dm_sh_X5A1URhNdvwQXYJ1nbMEHpTat
- Rock Arena → https://music.amazon.it/playlists/B085Z9Z27G?marketplaceId=APJ6JRA9NG5V4&musicTerritory=IT&ref=dm_sh_68TtDExn7nVI9rk8Pg4lYOKgT
- Top Hits Oggi → https://music.amazon.it/playlists/B073PJ6QC4?marketplaceId=APJ6JRA9NG5V4&musicTerritory=IT&ref=dm_sh_bDSZ4Be22YfNfYKbLaxmw3qk0

### 1.4 Artist (User) Logic

- **Released tracks for submission**
  - Only tracks where: `user_id = current user` AND `status IN ('Released', 'Modify Released')`.
  - Support optional search by title, artists, primary_genre.
  - Return paginated list (e.g. for “Invia brani” tab).

- **Editorial playlists by platform**
  - GET by platform: return rows from `editorial_playlists` where `platform = $platform` and `is_active = true`, ordered by `sort_order` then `name`. Return as array of `['name' => ..., 'url' => ...]`.
  - If no platform or “all”: return grouped by platform (same structure).

- **Submit to playlist**
  - Input: `track_id`, `platform`, `playlist_name`.
  - Validation:
    - Track must exist, belong to current user, and have status `Released` or `Modify Released`.
    - A row must exist in `editorial_playlists` with that `platform` and `name` (and `is_active = true`). Use that row's `url` as `playlist_url`.
  - Uniqueness: do **not** allow a second submission with same `(user_id, track_id, platform, playlist_name)`.
  - On success: create row in `playlist_submissions` with `status = 'Waiting'`, `submission_date = now()`, `playlist_url` from the `editorial_playlists` row.

- **My submissions**
  - List rows where `user_id = current user`, optional filter by `status`.
  - Paginate; include track (title, artists, cover_art, primary_genre) and playlist info.
  - Used for “In attesa” (all statuses) and “Playlist Raggiunte” (status = Published only).

### 1.5 Admin Logic (Playlist Submissions)

- **List all submissions**
  - Filters: `platform`, `status`, text `search` (match track title/artists OR user full_name/email OR playlist_name).
  - Paginate; include track and user data.

- **Update status**
  - Allowed values: `Waiting`, `Processing`, `Published`, `Rejected`.
  - When setting to `Published`: set `review_date = now()`, and optionally init `streams = 0`, `listeners = 0`.
  - When setting to any non-Waiting: set `review_date = now()`.

- **Update streams (and listeners)**
  - Only for submissions with `status = 'Published'`.
  - Accept `streams` and optionally `listeners` (non-negative integers); update the row.

### 1.6 Admin Logic (Manage Editorial Playlists — Add / Edit / Delete)

Admin must have a **dedicated page** to manage editorial playlists: **add**, **edit**, and **delete** playlist name and playlist link per platform.

- **List editorial playlists**
  - Table or list with columns: platform, name, url (truncated or as link), is_active, sort_order, actions (Edit, Delete).
  - Filter by platform (Spotify, Apple Music, Amazon Music).
  - Sort by platform then sort_order then name.

- **Add new playlist**
  - Form fields: platform (dropdown), name (text), url (text), sort_order (number, optional), is_active (checkbox, default true).
  - Validation: platform required; name required; url required and valid URL format; unique (platform, name).
  - On save: insert into `editorial_playlists`.

- **Edit playlist**
  - Same fields as add: allow changing name, url, sort_order, is_active. Optionally allow changing platform (if changed, enforce unique (platform, name)).
  - Existing `playlist_submissions` keep their stored `playlist_name` and `playlist_url` (copied at submit time); editing the catalog only affects future submissions.

- **Delete playlist**
  - Delete row from `editorial_playlists`. Existing submissions keep their stored name/url; no cascade. Optionally show a warning if the playlist name appears in any submission.

- **Routes and sidebar**
  - Provide a distinct admin route and page, e.g. `GET /admin/editorial-playlists/catalog` or `GET /admin/playlist-catalog`, for this CRUD. Admin sidebar: under "Editorial Playlists" (or similar) include both "Submissions" (existing list of artist submissions) and "Playlist catalog" (or "Manage playlists") linking to this add/edit/delete page.

### 1.7 Access Control

- Artist playlist page is gated: only users with “company” or equivalent premium flag can access (redirect others to a “not eligible” page).
- Admin endpoints and pages require admin role/guard.

---

## 2. Radio Promotion

### 2.1 Overview

Artists request **radio promotion** for a **single track** or for **one track from an album**. Each promotion has status: **pending → published** (28-day campaign) or **rejected** / **finished**. When **published**, set `published_date` and `finish_date = published_date + 28 days`. A **cron/scheduler** (or manual endpoint) sets status to **finished** when `finish_date` has passed. Published promotions can be shown on a **public “podcast” page** with a **like** button (authenticated users or guests via a UUID).

### 2.2 Data Model

**Table: `radio_networks`**

| Column      | Type / Constraint |
|-------------|-------------------|
| id          | bigint PK         |
| name        | string, required  |
| cover_image | string (storage path), nullable |
| is_active   | boolean, default true |
| created_by  | FK users, nullable |
| created_at, updated_at | timestamps |

**Table: `radio_promotions`**

| Column          | Type / Constraint |
|-----------------|-------------------|
| id              | bigint PK         |
| user_id         | FK users, required |
| track_id        | FK tracks, required |
| track_index     | int, nullable (null = single; 0,1,2... = album track index) |
| radio_network_id| FK radio_networks, nullable |
| status          | enum: `pending`, `published`, `rejected`, `finished`; default `pending` |
| request_date    | datetime, default now |
| published_date  | datetime, nullable |
| finish_date     | datetime, nullable |
| updated_by      | FK users, nullable |
| admin_notes     | text, default ''  |
| is_active       | boolean, default true |
| likes           | int unsigned, default 0 |
| liked_by        | JSON array of user IDs |
| liked_by_guests | JSON array of strings (guest UUIDs) |
| created_at, updated_at | timestamps |

**Business rules:**

- When admin sets status to **published**: set `published_date = now()`, `finish_date = now() + 28 days`.
- One active promotion per (user, track, track_index): do **not** allow a new row with `status IN ('pending','published')` for the same user_id + track_id + track_index (treat null and 0,1,2… distinctly for album tracks).

### 2.3 Artist (User) Logic

- **Tracks for promotion (singles)**
  - Tracks where: `user_id = current user`, `status IN ('Released','Modify Released')`, `release_type = 'single'`.
  - Pagination + optional search (title, artists, genre).

- **Albums for promotion**
  - Same filters but `release_type = 'album'`.
  - Return album metadata (album_title, artists, cover_art, etc.).

- **Album tracks**
  - Given an album id: ensure album belongs to user and is released.
  - Return list of tracks from the album’s track list, each with: track index, title, duration, cover (album cover), artists (album artist), album_title. Frontend uses this so user picks **one track** from the album.

- **Create promotion request**
  - Input: `track_id`, optional `track_index` (for album tracks).
  - Validation:
    - Track exists, belongs to user, is released.
    - If track is album: `track_index` must be present and valid index in album tracks.
    - If track is single: `track_index` must be null (or omitted).
  - Uniqueness: no existing row with same user_id, track_id, track_index (null for single) and status in (`pending`, `published`).
  - Create row with `status = 'pending'`, `request_date = now()`.

- **My requests**
  - List promotions for current user; include track (and for album, the specific track title/album title via track_index).
  - For status `published`, compute days_remaining and progress_percentage from published_date and finish_date (28-day window).

### 2.4 Public “Podcast” Page

- **Get promotion by ID**
  - Return promotion with track (and if album, specific track info). Allow viewing for any status if needed for admin debugging; typically only “published” is shown publicly.

- **Toggle like**
  - If user authenticated: add/remove user id in `liked_by`.
  - If guest: require a `guest_uuid` in request; add/remove in `liked_by_guests`.
  - Recompute `likes = count(liked_by) + count(liked_by_guests)` and persist.

### 2.5 Admin Logic

- **List all promotions**
  - Filters: status, search (user name/email). Paginate. Include user and track; for album tracks include resolved track title and album title from track_index.

- **Update status**
  - Allowed: `pending`, `published`, `rejected`, `finished`.
  - When changing to **published** from another status: set `published_date = now()`, `finish_date = now() + 28 days`, and `updated_by = current admin`. Optionally allow `admin_notes`.

- **Update expired (cron or manual)**
  - Find rows where `status = 'published'` and `finish_date <= now()`.
  - Set `status = 'finished'`.

### 2.6 Radio Networks (Admin)

- CRUD for `radio_networks`: list, create, update, delete. Used to show logos/names on the artist radio-promotion page. Public endpoint to list active networks (e.g. for “our radio network” section).

### 2.7 Access Control

- Artist radio promotion page: gated by company/premium (same as playlists).
- Public podcast and like: no auth required for view; like supports either auth or guest_uuid.
- Admin: admin-only.

---

## 3. Concerts Live

### 3.1 Overview

Admins create **concert live** events (name, city, date, slots_available). Artists **request a slot** by providing their **artist name**; admin can **confirm** or **cancel** the request. When confirmed, **slots_booked** on the concert is incremented; when un-confirmed, it is decremented. Concerts with no slots left can be marked inactive or hidden. Requests for past concert dates can be auto-marked **finished**.

### 3.2 Data Model

**Table: `concert_lives`**

| Column           | Type / Constraint |
|------------------|-------------------|
| id               | bigint PK         |
| name             | string, required, max 200 |
| city             | string, required, max 100 |
| concert_date     | datetime, required |
| slots_available  | int, required, 1–1000 |
| slots_booked     | int, default 0, min 0 |
| is_active        | boolean, default true |
| created_by       | FK users, required |
| created_at, updated_at | timestamps |

**Computed (accessors or DB):** `slots_remaining = slots_available - slots_booked`, `booking_percentage = round(slots_booked / slots_available * 100)`.

**Table: `concert_live_requests`**

| Column           | Type / Constraint |
|------------------|-------------------|
| id               | bigint PK         |
| user_id          | FK users, required |
| concert_live_id  | FK concert_lives, required |
| artist_name      | string, required, max 100 |
| status           | enum: `pending`, `confirmed`, `cancelled`, `finished`; default `pending` |
| request_date     | datetime, default now |
| admin_notes      | text, nullable    |
| updated_by       | FK users, nullable |
| is_active        | boolean, default true |
| created_at, updated_at | timestamps |

### 3.3 Artist (User) Logic

- **List upcoming concerts**
  - Where `is_active = true` and `concert_date >= now()`. Order by concert_date. Return name, city, concert_date, slots_available, slots_booked, slots_remaining, booking_percentage.

- **Request a slot**
  - Input: `concert_live_id`, `artist_name` (non-empty).
  - Validation:
    - Concert exists, is_active, and concert_date is in the future.
    - `slots_booked < slots_available`.
    - No existing **active** request for this user and this concert (same user_id + concert_live_id, is_active = true).
  - Create request with `status = 'pending'`.

- **My requests**
  - List requests for current user (is_active = true); include concert_live (name, city, concert_date). Show status and admin_notes.

### 3.4 Admin Logic

- **Concert lives CRUD**
  - List: pagination + search by name/city.
  - Create: name, city, concert_date (must be future), slots_available (1–1000). slots_booked = 0.
  - Update: same fields; ensure slots_available >= slots_booked; concert_date if changed must stay in future.
  - Delete: only if `slots_booked = 0`.

- **List all requests**
  - Filter by status. Include user and concert_live. When returning list, if a request has status `confirmed` and its concert_live.concert_date is in the past, auto-update that request’s status to `finished` (and optionally persist).

- **Update request status**
  - Allowed: `pending`, `confirmed`, `cancelled` (and `finished` if set by logic).
  - When changing **to** `confirmed` (from non-confirmed):
    - Ensure concert still has capacity: `slots_booked < slots_available`.
    - Increment `concert_lives.slots_booked` by 1.
    - If after increment `slots_booked >= slots_available`, set `concert_lives.is_active = false`.
  - When changing **from** `confirmed` to something else:
    - Decrement `concert_lives.slots_booked` by 1 (floor at 0).
    - If `slots_booked < slots_available`, set `concert_lives.is_active = true`.
  - Save request with new status, optional admin_notes, updated_by.

### 3.5 Access Control

- Artist concert-live page: gated by company/premium.
- Public list of concerts: can be unauthenticated (only active future concerts).
- Admin: admin-only.

---

## 4. Vevo Accounts

### 4.1 Overview

Artists **request a VEVO account** (artist name, contact email, phone, release name, biography). **Non-company** users are limited to **one** Vevo account/request per account; **company** users can have multiple. Admins **approve** or **reject**; when approved, admin can set **vevo_channel_url**. Store approval/rejection timestamps and which admin performed the action.

### 4.2 Data Model

**Table: `vevo_accounts`** (single table for request + approved account)

| Column           | Type / Constraint |
|------------------|-------------------|
| id               | bigint PK         |
| user_id          | FK users, required |
| artist_name      | string, required  |
| contact_email    | string, required  |
| telephone        | string, nullable  |
| release_name     | string, nullable  |
| biography        | text, required, min length 50 |
| status           | enum: `pending`, `approved`, `rejected`; default `pending` |
| admin_notes      | text, nullable    |
| vevo_channel_url | string, nullable  |
| approved_at      | datetime, nullable |
| approved_by      | FK users, nullable |
| rejected_at      | datetime, nullable |
| rejected_by      | FK users, nullable |
| created_at, updated_at | timestamps |

**Business rule:** For users where `is_company = false`, enforce at most one row per user (application-level check before insert).

### 4.3 Artist (User) Logic

- **Submit request**
  - Input: `artist_name`, `contact_email`, `telephone` (optional), `release_name` (optional), `biography` (required, min 50 chars).
  - Validation: required fields present; biography length >= 50.
  - If user is **not** company: check that no row exists for this user_id; if one exists, return error (e.g. “You can't request more than one Vevo account…”).
  - Create row with `status = 'pending'`.

- **My accounts**
  - List all vevo_accounts for current user, newest first. Show status, artist_name, contact_email, biography snippet, admin_notes, vevo_channel_url (if approved), timestamps.

### 4.4 Admin Logic

- **List all**
  - Pagination; filters: status, search (artist_name, contact_email, user full_name, user email). Join user for display.

- **Get one**
  - By id; include user and approved_by / rejected_by for timeline.

- **Update (full)**
  - Allow editing: artist_name, contact_email, telephone, release_name, biography (still min 50), status, admin_notes, vevo_channel_url.
  - When status changes:
    - To `approved`: set `approved_at = now()`, `approved_by = current admin`; clear rejected_at/rejected_by.
    - To `rejected`: set `rejected_at = now()`, `rejected_by = current admin`; clear approved_at/approved_by.
    - To `pending`: clear approved_* and rejected_*.

- **Update status (shortcut)**
  - Only update status (and optionally admin_notes, vevo_channel_url) with the same status-transition side effects as above.

- **Delete**
  - Soft delete or hard delete one vevo_account by id.

### 4.5 Access Control

- Artist vevo page: gated by company/premium; non-company users see “already have one request” and cannot submit again if they have one.
- Admin: admin-only.

---

## 5. Implementation Checklist for Cursor

When implementing in Laravel:

1. **Migrations**  
   Create migrations for: `editorial_playlists` (platform, name, url, sort_order, is_active); `playlist_submissions`; `radio_networks`, `radio_promotions`; `concert_lives`, `concert_live_requests`; `vevo_accounts`. Add indexes as described (e.g. unique (platform, name) on editorial_playlists; user_id, status, platform on playlist_submissions; etc.).

2. **Editorial playlists seed**  
   Create `EditorialPlaylistSeeder` that inserts all playlist names and URLs from section 1.3 (Spotify, Apple Music, Amazon Music) into `editorial_playlists`. Run from `DatabaseSeeder`. Artist-facing "editorial playlists by platform" and submit validation use this table; admin can add/edit/delete via the playlist catalog page.

3. **Models**  
   Eloquent models with relationships, fillable, casts (JSON for liked_by, liked_by_guests), and accessors (e.g. slots_remaining, booking_percentage, days_remaining for radio).

4. **Controllers**  
   - Playlist: artist (released tracks, editorial list from DB, submit, my submissions); admin (list submissions, update status, update streams); admin **playlist catalog** (list editorial_playlists, add, edit, delete playlist name and url).  
   - Radio: artist (tracks, albums, album tracks, create request, my requests); public (show promotion, toggle like); admin (list, update status, update expired); radio networks CRUD + public list.  
   - Concert: public list of concerts; artist (request slot, my requests); admin (concert CRUD, list requests, update request status).  
   - Vevo: artist (create request, my accounts); admin (list, show, update, update status, delete).

5. **Routes**  
   Map API routes to match the behavior above (e.g. `POST /api/playlists/submit`, `GET /api/playlists/submissions`, `PATCH /api/admin/playlists/submissions/{id}/status`, etc.). Use auth middleware for artist endpoints and admin middleware for admin.

6. **Validation**  
   Apply the same rules: playlist name must exist in `editorial_playlists` for the chosen platform; one submission per (user, track, platform, playlist); one active radio promotion per (user, track, track_index); concert slot availability and one request per user per concert; Vevo one-per-user for non-company; biography min 50 chars.

7. **Blade views**  
   Build dashboard pages: Playlists (tabs: send, waiting, reached), Radio Promotion (hero, networks, modal to pick track/album track, list requests), Concert Live (upcoming events, request modal, my requests), Vevo (form, my accounts). Build admin pages: Editorial Playlists (submissions table + filters + status/streams actions), **Editorial Playlist Catalog** (list playlists by platform, add new, edit name/url/sort_order/is_active, delete), Radio Requests (list + status/notes), Concert Lives (table + CRUD), Live Requests (list + status/notes), Vevo Accounts (list, view, edit, approve/reject, delete).

8. **Scheduler**  
   Schedule a daily (or hourly) command to set `radio_promotions.status = 'finished'` where `status = 'published'` and `finish_date <= now()`.

9. **Gating**  
   Restrict artist access to Playlists, Radio, Concert Live, and Vevo to company/premium users; redirect others to a “not eligible” page.

10. **Copy and UX**  
    Use the same Italian (or your app) labels and flows: “Invia brani”, “In attesa”, “Playlist Raggiunte”, “Richiedi promozione radiofonica”, “Candidati”, “Richiedi Account VEVO”, status labels (In attesa, Pubblicato, Respinto, Confermato, etc.) so the Laravel app matches the reference app’s functionality and feel.

---

*End of prompt. Implement only these four modules with the logic above; do not alter other parts of the Laravel application.*
