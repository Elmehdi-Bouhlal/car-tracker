# Car Trucker

Real-time vehicle tracking built with Laravel, Inertia, React, MapLibre, Laravel Reverb, and Echo.

The application stores the latest state of every car, tracks cars by their unique `ident`, broadcasts position changes through WebSockets, displays every car on a map, and stores a paginated update history.

![Car Tracker Map](https://assets.el-mehdi.work/projects/exm-1.png)

## Logs of each car

![Car Logs](https://assets.el-mehdi.work/projects/exm-2.png)

## Requirements

- PHP 8.3 or later
- Composer
- Node.js and npm
- SQLite or MySQL

## Installation

```bash
git clone git@github.com:Elmehdi-Bouhlal/car-tracker.git car-trucker
cd car-trucker
composer setup
```

`composer setup` runs the complete installation:

1. Installs Composer dependencies.
2. Creates `.env` from `.env.example` when needed.
3. Creates the SQLite database file when needed.
4. Generates the Laravel application key.
5. Runs database migrations.
6. Installs frontend dependencies.
7. Builds the frontend.

The default `.env.example` uses SQLite. To use MySQL, configure the `DB_*` values in `.env` before running migrations.

## Start the project

```bash
composer dev
```

This single command starts:

- Laravel development server
- Vite development server
- Queue listener
- Laravel Reverb WebSocket server

The queue listener must run because the Reverb broadcast listener is queued after the database transaction commits.

Open `http://localhost:8000` after the processes start.

## Architecture

Backend requests follow this flow:

```text
Route
  -> Controller
  -> Form Request
  -> Service
  -> Repository
  -> Model
  -> Database
```

- Controllers return the HTTP or Inertia responses.
- Form Requests handle authorization, normalization, rules, and messages.
- The service contains application logic and transactions.
- Repositories contain database queries.
- Models define database records, casts, and relationships.
- `ident` is the primary key of the `car` table and uniquely identifies each car.

## API endpoints

| Method | Endpoint | Purpose |
| --- | --- | --- |
| `POST` | `/api/truckers` | Create a car telemetry record |
| `GET` | `/api/truckers/{ident}` | Return one car |
| `PATCH` | `/api/truckers/{ident}` | Update one car and create a history log |
| `GET` | `/api/truckers/{ident}/history` | Return paginated update history |

History pagination accepts these query parameters:

```text
page=1
per_page=15
```

Example:

```bash
curl "http://localhost:8000/api/truckers/111115555599999/history?page=1&per_page=15" \
    -H "Accept: application/json"
```

Successful API responses use:

```json
{
    "success": true,
    "data": {}
}
```

Error responses use:

```json
{
    "success": false,
    "error": "Error message"
}
```

## Car update history

Every update runs inside a database transaction:

1. Update the current row in `car`.
2. Insert an immutable row into `car_logs`.
3. Store the changed fields in `changes`.
4. Store the complete updated car in `snapshot`.
5. Dispatch the position event.

History is saved synchronously inside the transaction so a car update cannot succeed without its matching log.

## Real-time updates

The real-time flow is:

```text
API endpoint
  -> TruckerService
  -> CarPositionChanged event
  -> BroadcastCarPositionUpdated listener
  -> Laravel Reverb
  -> Laravel Echo
  -> useCarPositionUpdates
  -> React state
  -> MapLibre markers
```

The backend broadcasts on:

```text
Channel: cars.positions
Event:   car.position.updated
```

Create, retrieve, and update operations use the same event name. The payload contains an `action` value of `created`, `retrieved`, or `updated`.

The frontend configures Echo in `resources/js/plugins/echo.ts` and subscribes in `resources/js/composables/useCarPositionUpdates.ts`.

Cars received through Reverb are merged into React state by `ident`. Existing cars are updated and new cars are added.

## Frontend structure

```text
resources/js/
├── components/   React UI components
├── composables/  Reusable React hooks
├── config/       Static frontend configuration
├── pages/        Inertia pages
├── plugins/      Echo/Reverb configuration
├── services/     API requests
├── types/        TypeScript types
└── utils/        Shared formatting and error helpers
```

The map displays every car returned by Laravel. Clicking a car marker opens a modal that requests and displays its paginated update history.

## Database tables

### `car`

Stores the latest telemetry state for every car. The `ident` column is the unique primary key.

### `car_logs`

Stores every update with:

- Car identifier
- Changed values
- Complete car snapshot
- Creation timestamp

## Environment configuration

The important Reverb values are already documented in `.env.example`:

```dotenv
BROADCAST_CONNECTION=reverb
QUEUE_CONNECTION=database

REVERB_APP_ID=your-app-id
REVERB_APP_KEY=your-app-key
REVERB_APP_SECRET=your-app-secret
REVERB_HOST=localhost
REVERB_PORT=8080
REVERB_SCHEME=http

VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST="${REVERB_HOST}"
VITE_REVERB_PORT="${REVERB_PORT}"
VITE_REVERB_SCHEME="${REVERB_SCHEME}"
```

Laravel and the frontend must use matching Reverb values.

## Validation

Run every project check with:

```bash
composer verify
```

This runs:

- Laravel tests
- Laravel Pint check
- PHPStan
- Frontend formatting and linting
- TypeScript checking
- Production frontend build
