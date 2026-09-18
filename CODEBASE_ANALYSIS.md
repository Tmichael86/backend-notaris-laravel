# Analisis Codebase Laravel & Rancangan Migrasi ke AdonisJS v6

## 1. Ringkasan Codebase Laravel (`notaris.blitaris.test`)
Sistem ini merupakan aplikasi Notaris & PPAT yang dibangun dengan Laravel. Berdasarkan hasil eksplorasi direktori `app/Models` dan `database/migrations`, sistem mencakup manajemen:
- **Transaksi**: Inti sistem (Pekerjaan Akta Notaris/PPAT).
- **Master Data**: Pemohon, Petugas, Pekerjaan, Kategori Pekerjaan, Jenis Pajak, dll.
- **Finansial**: Pendapatan, Pengeluaran, Riwayat Pembayaran.
- **Operasional**: Manajemen Materai, Sidebar akses, dan Konfigurasi Umum.

## 2. Skema Entitas Database (Rancangan AdonisJS Lucid)
Untuk mendukung arsitektur *Offline-First*, setiap tabel utama (transaksi, pemohon, dll) di AdonisJS akan menggunakan struktur berikut:
- `uuid` (Primary/Unique Index)
- `is_dirty` (Boolean, flag untuk sync)
- `last_synced_at` (Timestamp)

## 3. DTO & Interface TypeScript (AdonisJS v6)

### Contoh DTO untuk Pemohon
```typescript
export interface CreatePemohonDTO {
  uuid?: string
  nama: string
  alamat?: string
  jenisKelamin?: number
  noTelp?: string
  nik?: string
  status: number
}
```

### Contoh DTO untuk Transaksi
```typescript
export interface CreateTransaksiDTO {
  uuid?: string
  noAkta: string
  tanggalDaftar: string
  biayaLayanan: number
  total: number
  pemohonId: number
  statusId: number
  isDirty: boolean
}
```

## 4. Struktur Migration AdonisJS Lucid
```typescript
// migrations/xxxx_create_pemohon_table.ts
export default class extends BaseSchema {
  protected tableName = 'pemohon'
  async up() {
    this.schema.createTable(this.tableName, (table) => {
      table.increments('id')
      table.uuid('uuid').unique().notNullable()
      table.string('nama').notNullable()
      table.boolean('is_dirty').defaultTo(true)
      table.timestamp('last_synced_at').nullable()
      table.timestamp('deleted_at').nullable()
      table.timestamps()
    })
  }
}
```

## 5. Contoh Model Lucid ORM
```typescript
// app/models/pemohon.ts
export default class Pemohon extends BaseModel {
  @column({ isPrimary: true }) declare id: number
  @column() declare uuid: string
  @hasMany(() => Transaksi) declare transaksi: HasMany<typeof Transaksi>
}
```

## 6. Rancangan Alur SyncController
- `POST /api/v1/sync/push`: Menerima array data dari client, update record jika `uuid` ada, atau create jika baru.
- `GET /api/v1/sync/pull`: Mengembalikan record dengan `updated_at > lastSyncTimestamp` atau `is_dirty === true`.
