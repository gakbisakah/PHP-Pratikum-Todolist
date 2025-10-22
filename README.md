# Pratikum Pabwe4 Aplikasi Todolist

Repository ini menggunakan **PostgreSQL** sebagai database utama untuk aplikasi To-Do List.  
Berikut adalah skrip SQL untuk membuat tabel `todo` beserta semua kolom yang sesuai dengan kode PHP pada controller dan model.

## 📦 1. Buat Database
Buat database PostgreSQL terlebih dahulu:

```sql
CREATE DATABASE todo
    WITH 
    OWNER = postgres
    ENCODING = 'UTF8'
    LC_COLLATE = 'en_US.UTF-8'
    LC_CTYPE = 'en_US.UTF-8'
    TABLESPACE = pg_default
    CONNECTION LIMIT = -1;

## 🧱 2. Buat Tabel todo

Tabel ini menyimpan daftar tugas (to-do) dengan kolom yang sesuai dengan kode PHP pada TodoModel.

CREATE TABLE IF NOT EXISTS todo (
    id SERIAL PRIMARY KEY,
    title VARCHAR(255) NOT NULL UNIQUE,
    description TEXT,
    is_finished BOOLEAN NOT NULL DEFAULT FALSE,
    position INT NOT NULL DEFAULT 1,
    created_at TIMESTAMP WITHOUT TIME ZONE NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMP WITHOUT TIME ZONE NOT NULL DEFAULT NOW()
);

🧰 3. Trigger Otomatis Update updated_at

Agar kolom updated_at otomatis ter-update setiap kali ada perubahan data.

CREATE OR REPLACE FUNCTION update_updated_at_column()
RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at = NOW();
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

DROP TRIGGER IF EXISTS set_timestamp_on_todo ON todo;

CREATE TRIGGER set_timestamp_on_todo
BEFORE UPDATE ON todo
FOR EACH ROW
EXECUTE FUNCTION update_updated_at_column();

## Menjalankan Aplikasi
php -S localhost:8000 -t public

## Logs
- [14/10/2025] Menginisialisasi proyek
