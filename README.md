### Task Management API — Backend Intern Test
## Kandidat

# Nama: ANS
# Role: Backend Intern Candidate

# Tech Stack / Tools:
- Laravel 10
- PHP 8.1
- MySQL
- PhpSpreadsheet (Export Excel)
- Postman (API Testing)
- GitHub

# Cara Menjalankan Project
1. Install dependencies
 composer install

2. Copy environment
cp .env.example .env

3. Generate key
php artisan key:generate

4. Buat database & sesuaikan .env
 DB_DATABASE=tasks_db

5. Jalankan migration
php artisan migrate

6. Jalankan server
php artisan serve

### API Endpoints
1. Get all tasks

GET /api/tasks

2. Create task

POST /api/tasks

Body contoh:
{
"title": "Finish report",
"assignee": "Jane",
"due_date": "2025-12-01",
"time_tracked": 2,
"status": "pending",
"priority": "medium"
}

3. Delete task

DELETE /api/tasks/hapus/{id}

4. Delete ALL tasks

DELETE /api/tasks/hapus-all

5. Export tasks to Excel

GET /api/tasks/export

Filter yang tersedia:

- title
- assignee (multi)
- start, end (due_date range)
- min, max (time_tracked)
- status (multi)

priority (multi)

Contoh:
/api/tasks/export?priority=low,high&status=pending

📦 Export Excel

Hasil export berisi:
- Semua tasks yang sesuai filter
- Total tasks
- Total time tracked
- Format tabel otomatis