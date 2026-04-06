# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## What this app is

PCP Locations is a CodeIgniter 4 web app (PHP 8.2+, MySQL) for browsing UK telephone exchange cabinet (PCP) locations. Data is scraped from cablocations.co.uk, imported via CSV, and served through both a public web UI and a token-authenticated REST API.

## Commands

```bash
# Run tests
composer test
# or
./vendor/bin/phpunit

# Run a single test file
./vendor/bin/phpunit tests/unit/SomeTest.php

# Database migrations
php spark migrate

# Database seeding (imports cablocations.csv into the cabinets table)
php spark db:seed CabinetSeeder
php spark db:seed AdminUserSeeder

# Built-in dev server (not for production)
php spark serve

# Scrape fresh data from cablocations.co.uk → scraper/cablocations.csv + .json
cd scraper && python3 scraper.py              # London DBs only (CL LN LW LS WE WR)
cd scraper && python3 scraper.py CL LN        # specific DBs
cd scraper && python3 scraper.py --all        # all DBs
```

## Architecture

### Data model

The entire dataset lives in a single `cabinets` table. Each row is one cabinet. An exchange is represented by grouping rows on `(db, exchange)`. The exchange building itself is stored as a special row with `cab = 'EXCH'` — this is the source of exchange-level coordinates. Every query that needs exchange lat/lng uses `MAX(CASE WHEN UPPER(cab) = 'EXCH' THEN lat END)`.

`db` is a short region code (e.g. `CL`, `LN`); `db_name` is its human-readable name. Exchange and cabinet names are stored uppercased.

### Route / auth layers

Three overlapping filter layers protect routes (defined in `app/Config/Filters.php`):

- `auth` — applied globally (except `/login` and `api/v1/*`); requires an active session
- `editor` — session role must be `editor` or `admin`; gates cabinet/exchange create/edit routes
- `admin` — session role must be `admin`; gates user management and API key management
- `apikey` — applied to `api/v1/*`; validates `X-API-Key` header against the `api_keys` table (no session)

### Controllers

- `Home` — public web UI: exchange search (AJAX), nearby exchanges (GPS/AJAX), exchange detail page, cabinet detail page
- `CabinetController` — editor-only CRUD for exchanges and cabinets
- `AuthController` — login/logout with session
- `UsersController` — admin-only user management
- `ApiKeysController` — admin-only API key management
- `ApiController` — REST API (`/api/v1/`) authenticated by API key

### REST API

Base path: `/api/v1/`. All routes require `X-API-Key` header.

| Endpoint | Description |
|---|---|
| `GET /api/v1/exchanges` | Search exchanges (`?q=`, `?limit=`) |
| `GET /api/v1/exchanges/{db}/{exchange}` | Exchange detail with cabinets |
| `GET /api/v1/cabinets/{id}` | Single cabinet |
| `GET /api/v1/nearby?lat=&lng=&limit=` | Nearest exchanges by Haversine |
| `GET /api/v1/search?exchange=&cab=` | Find a specific cabinet |

There is also a parallel set of internal AJAX endpoints on `Home` (`/api/exchanges`, `/api/nearby`) used by the web UI — these are session-authenticated (via the global `auth` filter) and return slightly different shapes.

### Views

Views use CodeIgniter's native PHP templating. Layouts are in `app/Views/layouts/`. The `totalCount` variable (total cabinet rows) is passed to every view for display in the UI.

### Scraper

`scraper/scraper.py` is a standalone async Python script (aiohttp) that authenticates against the cablocations.co.uk API and exports data to `scraper/cablocations.csv` and `scraper/cablocations.json`. The CSV is then fed into `CabinetSeeder`. The scraper credentials (`cab`/`cab123`) are public credentials for that third-party API.

### Environment setup

Copy `.env.example` (or `env`) to `.env` and configure:
- `app.baseURL`
- `database.default.*` connection settings
- `CI_ENVIRONMENT = development` for debug toolbar
