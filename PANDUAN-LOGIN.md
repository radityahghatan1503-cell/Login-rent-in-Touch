# Fitur Login Rent In-Touch — Cara Pasang

1. Salin semua isi folder ini ke proyek (timpa file yang sama):
   - app/Http/Controllers/AuthController.php
   - app/Http/Middleware/RoleMiddleware.php
   - app/Http/Kernel.php            (tambah alias 'role')
   - app/Models/User.php
   - app/Providers/RouteServiceProvider.php   (HOME = '/dashboard')
   - routes/web.php
   - database/migrations/2026_10_27_000001_add_role_to_users_table.php
   - database/seeders/DatabaseSeeder.php
   - public/css/rent.css
   - resources/views/layouts/auth.blade.php
   - resources/views/auth/login.blade.php, register.blade.php
   - resources/views/dashboard.blade.php
2. Atur .env (DB_DATABASE dll), lalu jalankan:
   php artisan migrate
   php artisan db:seed
   php artisan serve
3. Buka http://127.0.0.1:8000/login
   Akun demo (password: password123):
   pelanggan@rentintouch.test | mitra@rentintouch.test | admin@rentintouch.test
