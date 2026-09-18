# ADVANCED_AGENT_INSTRUCTIONS.md

## 1. Project Rules & Constraints
- **Backend**: AdonisJS v6 (Node.js/TypeScript), API-only.
- **Frontend**: Flutter (Dart), Mobile-First, Offline-First.
- **Typing**: Strict TypeScript and Dart.
- **Offline Sync**:
  - Client generates `UUIDv4` for every new record.
  - Every table must include: `uuid` (UUID), `is_dirty` (boolean), `last_synced_at` (timestamp), `deleted_at` (timestamp).
  - Soft Deletes: Perform `deleted_at` updates. Never hard-delete.
- **Conflict Resolution**: Last-Write-Wins (LWW) based on `updated_at`.

## 2. Database Mapping Table

| Laravel Table | AdonisJS Model | Key Columns | Relasi |
| :--- | :--- | :--- | :--- |
| `pemohon` | `Pemohon` | id, uuid, nama, alamat, nik, is_dirty | `hasMany(Transaksi)` |
| `transaksi` | `Transaksi` | id, uuid, no_akta, total, pemohon_id, is_dirty | `belongsTo(Pemohon)` |
| `petugas` | `Petugas` | id, uuid, nama, username, is_dirty | - |
| `jenis_pekerjaan` | `JenisPekerjaan` | id, nama, is_dirty | `hasMany(Transaksi)` |

## 3. API Contract for Delta Sync Engine

### POST `/api/v1/sync/push`
Payload:
```json
{
  "client_id": "device_uuid",
  "data": [
    {
      "table": "transaksi",
      "records": [
        { "uuid": "...", "no_akta": "...", "is_dirty": true, "updated_at": "..." }
      ]
    }
  ]
}
```

### GET `/api/v1/sync/pull`
Query Params: `?since=2026-01-01T00:00:00Z`
Response:
```json
{
  "last_server_timestamp": "...",
  "changes": {
    "transaksi": [ ... ],
    "pemohon": [ ... ]
  }
}
```

## 4. Target Project Structure

### AdonisJS v6
- `app/controllers`: HTTP Handlers, `SyncController.ts`
- `app/models`: Lucid Models with `@column` and `@belongsTo`/`@hasMany`
- `app/dtos`: Request/Response interfaces for sync
- `app/services`: Business logic, `SyncService.ts`

### Flutter
- `lib/features`: Feature-first modular structure (e.g., `features/transaksi`)
- `lib/core/database`: Drift or Isar local database definitions
- `lib/core/sync`: Sync engine logic (Delta calculation, API calls)
