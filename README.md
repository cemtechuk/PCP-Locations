# PCP Locations

A web application for browsing and locating UK telephone exchange PCP (Primary Cross-connection Point) cabinets. Built with CodeIgniter 4 / PHP 8.2 and MySQL.

**No data is included.** This is a blank container — bring your own cabinet dataset. See [Importing Your Data](#importing-your-data) for the expected format.

---

## What it does

PCP cabinets are the green street-side boxes that connect homes and businesses to their local telephone exchange. This app lets you search for exchanges, browse every cabinet on that exchange, see addresses and GPS coordinates, and view cabinet locations on a map.

---

## Features

### Public — Exchange Search

The home page has a live search box. Results appear as you type (minimum 2 characters) via a debounced AJAX call. Each result shows the exchange name, region, cabinet count, and a Google Maps link to the exchange building. Results animate in with a Samaritan-style text scramble effect.

Keyboard navigation is supported: arrow keys move through results, Enter navigates to the selected exchange, Escape dismisses the list.

### Public — Nearby Exchanges

On page load the browser requests the user's GPS location. If granted, the 3 nearest exchanges are shown beneath the search box with their distance in kilometres, calculated using the Haversine formula against each exchange building's coordinates. These also scramble in on load.

### Public — Exchange Detail

URL: `/exchange/{db}/{exchange}` (e.g. `/exchange/CL/HACKNEY`)

Lists all cabinets for that exchange ordered alphanumerically by cabinet ID. Each row shows the cabinet ID, street address, any notes, and a Google Maps link when coordinates are available. A filter input lets you narrow the list by cabinet ID or address fragment.

An **Export CSV** button downloads the current exchange's cabinets as a CSV file.

### Public — Cabinet Detail

URL: `/cabinet/{id}`

Full detail for a single cabinet:

- Exchange, cabinet ID, region, address, coordinates
- Live distance from the user's current GPS position (computed client-side)
- Interactive Leaflet map (CartoDB tiles) with a custom hollow red triangle marker and a pulsing circle
- Google Maps and Street View deep links
- Breadcrumb navigation back to the exchange

---

### Editor / Admin — Content Management

Users with the `editor` or `admin` role can create and edit data directly from the UI:

- **Add Exchange** — creates the exchange with an `EXCH` anchor row that stores the exchange building's coordinates. The anchor row is required for the GPS nearby feature and map pin to work.
- **Add Cabinet** — adds a new cabinet row to an existing exchange.
- **Edit Cabinet** — update any cabinet's ID, address, coordinates, and notes.
- **Delete Exchange** — removes all rows for an exchange (the anchor row plus every cabinet). Requires confirmation. Available on the exchange detail page.

---

### Admin — Dashboard

URL: `/dashboard`

At-a-glance statistics:

| Stat | Description |
|------|-------------|
| Cabinets | Total rows in the cabinets table |
| Exchanges | Distinct exchange count |
| Regions | Distinct region (db) count |
| Users | Total user accounts |
| Active Keys | API keys not revoked |
| API Today | Requests logged today |
| API 7 Days | Requests logged in the last 7 days |

Below the stats:

- **Recent Activity** — last 20 audit log entries, colour-coded by action (green = created, grey = updated, red = deleted)
- **Top Exchanges** — exchanges ranked by cabinet count; rows are clickable
- **API Key Usage** — request count per key with last-used timestamp

An **Export All CSV** button in the page header downloads the entire dataset.

---

### Admin — User Management

URL: `/users`

Create, edit, and delete user accounts. Fields: username, email, bcrypt password, role. See [Authentication & Roles](#authentication--roles) for what each role can do.

---

### Admin — Settings

URL: `/settings`

Three tabs:

**General**

| Setting | Description |
|---------|-------------|
| Site Title | Shown in the nav bar and browser tab |
| Logo | Upload an image (PNG, JPG, GIF, SVG, WEBP) to replace the text brand in the nav. Removable. |
| Favicon | Upload a file (ICO, PNG, SVG) or paste an SVG string directly — a live preview renders as you type |
| Scramble Speed | Controls how fast the text-scramble animation plays. Scale of 1 (Fast) to 5 (Glacial). |
| Viewer Rate Limit | Maximum requests per hour for all `viewer` accounts |
| Guest Rate Limit | Maximum requests per hour for all `guest` accounts |

**Import**

Upload a CSV file to bulk-import cabinet records. The file is validated and inserted in the background. See [Importing Your Data](#importing-your-data) for the required format.

**API Keys**

Generate, view, revoke, and permanently delete REST API keys. Each key can have an optional per-hour rate limit — blank means unlimited. The table shows each key's name, creation date, last-used time, and request cap.

---

### All Users — Profile

URL: `/profile`

Every logged-in user has a profile page. The red **Logout** button lives here.

Users with the `viewer`, `user`, `editor`, or `admin` role can update their own username, email, and password from this page (leaving the password field blank keeps the current one).

**Guest accounts are read-only** — the edit form is not shown and the update endpoint is blocked. An admin must make any changes to a guest account via the Users management page.

---

## REST API

All endpoints are under `/api/v1/` and require an `X-API-Key` header. No session is needed.

```
X-API-Key: your-key-here
```

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/v1/exchanges` | List/search exchanges. Params: `q` (name filter), `limit` (default 20, max 100) |
| GET | `/api/v1/exchanges/{db}/{exchange}` | Exchange detail with all its cabinets |
| GET | `/api/v1/cabinets/{id}` | Single cabinet by ID |
| GET | `/api/v1/nearby` | Nearest exchanges. Params: `lat`, `lng` (required), `limit` (default 3, max 20) |
| GET | `/api/v1/search` | Find a cabinet. Params: `exchange` (fuzzy name match), `cab` (exact, case-insensitive) |

All responses are JSON. Exchange and cabinet objects include a `url` field with the canonical web UI link. Error responses use appropriate HTTP status codes (400, 404, 429).

**Rate limiting:** If a key has a rate limit set, requests exceeding that limit within a rolling 60-minute window return HTTP 429 with a JSON error body.

**Request logging:** Every authenticated API request is logged to the `api_logs` table and reflected on the dashboard.

---

## Authentication & Roles

| Role | Access |
|------|--------|
| *(unauthenticated)* | Public read-only pages only |
| `guest` | Logged-in, browse-only, with a dedicated hourly request cap. Can only be created by an admin. Cannot edit their own profile. |
| `viewer` | Logged-in, browse-only, with a separate configurable hourly request cap. Can edit their own profile. |
| `user` | Logged-in, browse-only, no rate limiting |
| `editor` | All of the above + create/edit/delete exchanges and cabinets |
| `admin` | Full access: all editor permissions + user management + settings + dashboard |

The login page is at `/login`. After login, users are redirected to the page they were originally trying to reach. The session stores `user_id`, `username`, `role`, and `logged_in`.

API routes bypass session auth entirely and use the `X-API-Key` header instead.

Rate limits for `guest` and `viewer` use a rolling 60-minute window stored in the application cache. The following routes are always exempt regardless of limit: `/` (home page), `/login`, and `/logout`.

When a rate-limited user visits the home page:
- The search input is disabled with a faded red style; the placeholder shows the minutes remaining until the window resets
- The nearby exchanges GPS fetch is suppressed

Navigating to any other page while over the limit shows a styled 429 page (or JSON for AJAX calls) with the remaining time and links to go home or log out.

---

## Data Model

Everything lives in a single `cabinets` table. The hierarchy is:

```
Region (db / db_name)  →  Exchange  →  Cabinet (cab)
      CL / Central London   HACKNEY       P1, P2, EXCH, ...
```

Each row is one cabinet. An exchange's own building is stored as a special row with `cab = 'EXCH'` — this is where exchange-level coordinates come from. All queries that need exchange lat/lng use `MAX(CASE WHEN UPPER(cab) = 'EXCH' THEN lat END)`.

Cabinet IDs and exchange names are stored uppercased.

Supporting tables:

| Table | Purpose |
|-------|---------|
| `users` | User accounts with bcrypt passwords and roles |
| `api_keys` | REST API keys with optional per-key rate limits |
| `api_logs` | Every API request (key, endpoint, IP, timestamp) |
| `audit_log` | Every create/update/delete action on cabinet data |
| `settings` | Key-value store for admin-configurable settings |

---

## Importing Your Data

Cabinet data can be imported two ways:

### Via the admin UI

Go to **Settings → Import**, select your CSV file, and submit. Records are inserted in batches and duplicates are not checked — clear the `cabinets` table first if you need a clean slate.

### Via the CLI seeder

Place your CSV at `scraper/cablocations.csv`, then run:

```bash
php spark db:seed CabinetSeeder
```

The seeder inserts records in batches of 500 and prints progress as it runs.

---

### CSV format

The file must have a header row with these exact column names:

| Column | Type | Required | Description |
|--------|------|----------|-------------|
| `db` | string (max 5) | Yes | Short region code, e.g. `CL`, `LN` |
| `db_name` | string (max 50) | Yes | Human-readable region name, e.g. `Central London` |
| `exchange` | string (max 100) | Yes | Exchange name, e.g. `HACKNEY`. Stored uppercased. |
| `cab` | string (max 20) | Yes | Cabinet ID, e.g. `P1`, `P2`. Use `EXCH` for the exchange building. Stored uppercased. |
| `address` | string | No | Street address |
| `lat` | decimal | No | Latitude (WGS84), e.g. `51.54412300` |
| `long` | decimal | No | Longitude (WGS84), e.g. `-0.05531200` |
| `notes` | string | No | Any additional notes |

Example:

```csv
db,db_name,exchange,cab,address,lat,long,notes
CL,Central London,HACKNEY,EXCH,electrical house 1 pitfield street london,51.53020100,-0.08204400,
CL,Central London,HACKNEY,P1,queensbridge road london,51.54412300,-0.05531200,
CL,Central London,HACKNEY,P2,the triangle london,51.53832500,-0.07163200,
```

> **The `EXCH` row is required for full functionality.** Each exchange should have one row with `cab = EXCH` representing the exchange building. Its coordinates are used for the map pin and GPS nearby feature. Exchanges without an `EXCH` row will still list their cabinets but won't have a precise location.

---

## Setup

**Requirements:** PHP 8.2+, MySQL 5.7+ or MariaDB 10.3+, Composer, `intl` and `mbstring` PHP extensions.

```bash
composer install
cp env .env
# Edit .env — set app.baseURL and database.default.* credentials
php spark migrate
php spark db:seed AdminUserSeeder   # creates the default admin account
php spark serve                     # dev server at http://localhost:8080
```

The default admin credentials are set in `app/Database/Seeds/AdminUserSeeder.php`.

---

## Tech Stack

- **Backend:** CodeIgniter 4, PHP 8.2
- **Database:** MySQL
- **Frontend:** Bootstrap 5.3, Share Tech Mono (Google Fonts), vanilla JS
- **Maps:** Leaflet 1.9 with CartoDB light tiles
- **Tests:** PHPUnit 10
