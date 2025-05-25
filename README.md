# Smartlink Tes
## Petunjuk Instalasi

1. jalankan dalam terminal "composer install"
2. jalankan dalam terminal "npm install & npm run dev"
3. jalankan dalam terminal untuk mengcopy env "cp .env.example .env"
4. generate application key dengan menjalankan "php artisan key:generate"
5. edit konfigurasi database dalam .env
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=namadb
    DB_USERNAME=root
    DB_PASSWORD=

6. migrate database dan seed database dengan menjalankan 'php artisan migrate --seed'
7. jalankan dalam servel lokal anda seperti xampp atau sebagainya, atau bisa dengan menjalankan 'php artisan serve' dan akan dijalankan pada http://127.0.0.1:8000
