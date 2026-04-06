# PCP Locations

A web application for browsing and locating UK telephone exchange PCP (Primary Cross-connection Point) cabinets. Built with CodeIgniter 4 / PHP 8.2 and MySQL.

**No data is included.** This is a blank container — bring your own cabinet dataset. See [Importing Your Data](#importing-your-data) below for the expected format.

---

## What it does

PCP cabinets are the green street-side boxes that connect homes and businesses to their local telephone exchange. This app lets you search for exchanges, browse every cabinet on that exchange, see addresses and GPS coordinates, and view cabinet locations on a map — all in one place.

---

## Features

### Public — Exchange Search
The home page provides a live search box. As you type (minimum 2 characters), results appear in real time via a debounced AJAX call. Each result shows the exchange name, region, cabinet count, and a direct Google Maps link to the exchange building.

Keyboard navigation is supported: arrow keys move through results, Enter navigates, Escape dismisses.

### Public — Nearby Exchanges
On page load the browser requests the user's GPS location. If granted, the 3 nearest exchanges are shown beneath the search box, each with their distance in kilometres calculated using the Haversine formula against the exchange building's coordinates.

### Public — Exchange Detail
URL: `/exchange/{db}/{exchange}` (e.g. `/exchange/CL/HACKNEY`)

Shows all cabinets for that exchange in a sortable table (ordered alphanumerically by cabinet ID). Includes cabinet name, street address, notes, and a Google Maps link for cabinets that have coordinates. A filter input lets you narrow results by cabinet number or address fragment.

### Public — Cabinet Detail
URL: `/cabinet/{id}`

Shows full detail for a single cabinet:
- Exchange, cabinet ID, region, address, coordinates
- Live distance from the user's current GPS location (computed client-side)
- Interactive map (Leaflet + CartoDB tiles) with a custom hollow red triangle marker and a subtle pulse circle
- Google Maps and Street View links
- Breadcrumb navigation back to the exchange

### Editor / Admin — Content Management
Users with the `editor` or `admin` role can:
- Create a new exchange (creates an `EXCH` anchor row that provides the exchange's own coordinates)
- Add cabinets to an existing exchange
- Edit any cabinet's details (cab ID, address, coordinates, notes)

These controls appear inline in the UI — an "Add Exchange" button on the home page and "Add Cabinet" / "Edit" buttons on the relevant detail pages.

### Admin — Dashboard
URL: `/dashboard`

An at-a-glance overview of the system: total cabinet records, exchanges, regions, users, and API keys. Shows recent audit activity (creates/updates/deletes), top exchanges by cabinet count, and API key usage. Includes a button to export the entire dataset as a CSV.

### Admin — User Management
URL: `/users` (also accessible from the Admin nav dropdown)

Admins can create, edit, and delete user accounts. Each user has a `username`, `email`, `password` (bcrypt), and a `role` (`viewer`, `user`, `editor`, or `admin`).

The `viewer` role can log in and browse the site but earns no extra write permissions. A per-hour request cap for viewers is configurable in Settings → General.

### Admin — Settings
URL: `/settings`

Three-tab admin page:
- **General** — site title, logo (file upload), favicon (file upload or paste an SVG string with live preview), text-scramble animation speed (1–5 scale), viewer hourly request limit
- **Import** — upload a CSV file to bulk-import cabinet data without touching the CLI
- **API Keys** — generate, view, revoke, and delete REST API keys; each key can have an optional per-hour rate limit

### All users — Profile
URL: `/profile`

Every logged-in user can update their own username, email, and password. The logout button lives here.

---

## REST API

All endpoints are under `/api/v1/` and require the `X-API-Key` header. No session is needed.

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/v1/exchanges` | Search exchanges. Params: `q` (name filter), `limit` (default 20, max 100) |
| GET | `/api/v1/exchanges/{db}/{exchange}` | Full exchange detail with all its cabinets |
| GET | `/api/v1/cabinets/{id}` | Single cabinet by ID |
| GET | `/api/v1/nearby` | Nearest exchanges by GPS. Params: `lat`, `lng`, `limit` (default 3, max 20) |
| GET | `/api/v1/search` | Find a cabinet. Params: `exchange` (fuzzy), `cab` (exact, case-insensitive) |

All responses are JSON. Exchange and cabinet objects include a `url` field with the canonical web page link.

---

## Authentication & Roles

| Role | Access |
|------|--------|
| *(unauthenticated)* | Public read-only pages only |
| `guest` | Browse-only with a separate configurable hourly request cap; can only be created by an admin |
| `viewer` | Browse-only with a configurable hourly request cap |
| `user` | Browse-only, no rate limiting |
| `editor` | All of the above + create/edit exchanges and cabinets |
| `admin` | All of the above + user management + settings + dashboard |

The login page is at `/login`. After login, users are redirected to the page they were trying to reach. The session stores `user_id`, `username`, `role`, and `logged_in`.

API routes bypass session auth entirely and use the `X-API-Key` header instead.

---

## Data Model

Everything lives in a single `cabinets` table. The hierarchy is:

```
Region (db / db_name)  →  Exchange  →  Cabinet (cab)
      CL / Central London   HACKNEY       P1, P2, EXCH, ...
```

Each row is one cabinet. An exchange's own building is stored as a special row with `cab = 'EXCH'` — this is where exchange-level coordinates come from. All queries that need exchange lat/lng use `MAX(CASE WHEN UPPER(cab) = 'EXCH' THEN lat END)`.

Cabinet IDs and exchange names are stored uppercased. Cabinets are sorted alphanumerically (`P1`, `P2`, ... `P10` not `P1`, `P10`, `P2`).

---

## Importing Your Data

The app ships with no cabinet data. You need to provide a CSV file named `cablocations.csv` in the project root, then run the seeder to import it.

### CSV format

The file must have a header row with these columns in order:

| Column | Type | Required | Description |
|--------|------|----------|-------------|
| `db` | string (max 5) | Yes | Short region/database code, e.g. `CL`, `LN` |
| `db_name` | string (max 50) | Yes | Human-readable region name, e.g. `Central London` |
| `exchange` | string (max 100) | Yes | Exchange name, e.g. `HACKNEY`. Stored uppercased. |
| `cab` | string (max 20) | Yes | Cabinet identifier, e.g. `P1`, `P2`. Use `EXCH` for the exchange building itself. Stored uppercased. |
| `address` | string | No | Street address of the cabinet |
| `lat` | decimal | No | Latitude (WGS84), e.g. `51.54412300` |
| `long` | decimal | No | Longitude (WGS84), e.g. `-0.05531200` |
| `notes` | string | No | Any additional notes |

Example rows:

```csv
db,db_name,exchange,cab,address,lat,long,notes
CL,Central London,HACKNEY,EXCH,electrical house 1 pitfield street london,51.53020100,-0.08204400,
CL,Central London,HACKNEY,P1,queensbridge road london,51.54412300,-0.05531200,
CL,Central London,HACKNEY,P2,the triangle london,51.53832500,-0.07163200,
```

> **The `EXCH` row is important.** Each exchange should have one row with `cab = EXCH` representing the exchange building. This row's coordinates are used as the exchange's location for the map link and the GPS nearby feature. Exchanges without an `EXCH` row will still work but won't have a precise map pin.

### Importing

Place your `cablocations.csv` in the project root, then run:

```bash
php spark db:seed CabinetSeeder
```

The seeder inserts records in batches of 500 and will print progress as it runs. Re-running it will insert duplicates — truncate the `cabinets` table first if you need a clean import.

---

## Setup

**Requirements:** PHP 8.2+, MySQL, Composer, `intl` and `mbstring` PHP extensions.

```bash
composer install
cp env .env
# Edit .env — set app.baseURL and database.default.* credentials
php spark migrate
php spark db:seed AdminUserSeeder   # creates default admin account
php spark serve                     # dev server at http://localhost:8080
```

---

## Tech Stack

- **Backend:** CodeIgniter 4.7, PHP 8.2
- **Database:** MySQL (`cabinets`, `users`, `api_keys`, `api_logs`, `audit_log`, `settings`)
- **Frontend:** Bootstrap 5.3, Share Tech Mono (Google Fonts), vanilla JS
- **Maps:** Leaflet 1.9 with CartoDB light tiles
- **Tests:** PHPUnit 10
