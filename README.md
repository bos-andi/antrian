## Persiapan Project Antrian

1. Local Server Xampp 
2. Composer
3. Git
4. Node.js
5. php version >= 8.2

## Setup Project Antrian

Perhatikan untuk menjalankan atau mensetup project ini.

1. Buat database terlebih dahulu
2. Konfigurasikan file .env dengan database yang telah dibuat
3. Import Database dengan file antrian.sql pada projek
7. Jalankan perintah `php artisan serve` untuk menjalankan projek
8. Buka browser dan kunjungi link http://127.0.0.1:8000
9. Login dengan email (admin@andidev.id) dan password (andidev.id)

Aplikasi siap di gunakan....

## Tutorial Deploy ke VPS

Panduan lengkap untuk deploy aplikasi Laravel Antrian ke VPS (Ubuntu/Debian).

### Prasyarat VPS

1. **VPS dengan Ubuntu 20.04/22.04 atau Debian 11/12**
2. **Akses root atau user dengan sudo**
3. **Domain name (opsional, bisa menggunakan IP)**

### Langkah 1: Persiapan Server

#### 1.1 Update Sistem
```bash
sudo apt update && sudo apt upgrade -y
```

#### 1.2 Install Dependencies
```bash
sudo apt install -y software-properties-common curl wget git unzip
```

### Langkah 2: Install Web Server (Nginx)

#### 2.1 Install Nginx
```bash
sudo apt install -y nginx
```

#### 2.2 Start dan Enable Nginx
```bash
sudo systemctl start nginx
sudo systemctl enable nginx
```

#### 2.3 Verifikasi Nginx
Buka browser dan akses IP VPS Anda. Jika muncul halaman default Nginx, berarti berhasil.

### Langkah 3: Install PHP dan Extensions

#### 3.1 Install PHP 8.2 dan Extensions
```bash
sudo apt install -y php8.2-fpm php8.2-cli php8.2-common php8.2-mysql php8.2-zip php8.2-gd php8.2-mbstring php8.2-curl php8.2-xml php8.2-bcmath php8.2-intl
```

#### 3.2 Verifikasi PHP
```bash
php -v
```

### Langkah 4: Install MySQL/MariaDB

#### 4.1 Install MySQL
```bash
sudo apt install -y mysql-server
```

#### 4.2 Secure MySQL Installation
```bash
sudo mysql_secure_installation
```
Ikuti instruksi:
- Set root password (atau tekan Enter jika tidak ingin password)
- Hapus anonymous users? **Y**
- Disallow root login remotely? **Y**
- Remove test database? **Y**
- Reload privilege tables? **Y**

#### 4.3 Buat Database dan User
```bash
sudo mysql -u root -p
```

Kemudian jalankan perintah SQL berikut:
```sql
CREATE DATABASE antrian CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'antrian_user'@'localhost' IDENTIFIED BY 'password_anda_yang_kuat';
GRANT ALL PRIVILEGES ON antrian.* TO 'antrian_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

**Catatan:** Ganti `password_anda_yang_kuat` dengan password yang kuat!

### Langkah 5: Install Composer

#### 5.1 Download Composer
```bash
cd ~
curl -sS https://getcomposer.org/installer | php
```

#### 5.2 Pindahkan Composer ke Global
```bash
sudo mv composer.phar /usr/local/bin/composer
sudo chmod +x /usr/local/bin/composer
```

#### 5.3 Verifikasi Composer
```bash
composer --version
```

### Langkah 6: Install Node.js dan NPM

#### 6.1 Install Node.js 20.x
```bash
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install -y nodejs
```

#### 6.2 Verifikasi Node.js dan NPM
```bash
node -v
npm -v
```

### Langkah 7: Clone dan Setup Project

#### 7.1 Clone Repository
```bash
cd /var/www
sudo git clone https://github.com/bos-andi/antrian.git
sudo chown -R www-data:www-data antrian
cd antrian
```

#### 7.2 Install Dependencies
```bash
composer install --optimize-autoloader --no-dev
npm install
npm run build
```

#### 7.3 Setup Environment File
```bash
cp .env.example .env
nano .env
```

Edit konfigurasi berikut di file `.env`:
```env
APP_NAME="Antrian"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=http://your-domain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=antrian
DB_USERNAME=antrian_user
DB_PASSWORD=password_anda_yang_kuat
```

#### 7.4 Generate Application Key
```bash
php artisan key:generate
```

#### 7.5 Import Database
```bash
mysql -u antrian_user -p antrian < antrian.sql
```

#### 7.6 Setup Storage dan Permissions
```bash
php artisan storage:link
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

#### 7.7 Optimize Laravel
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Langkah 8: Konfigurasi Nginx

#### 8.1 Buat File Konfigurasi Nginx
```bash
sudo nano /etc/nginx/sites-available/antrian
```

Tambahkan konfigurasi berikut:
```nginx
server {
    listen 80;
    server_name your-domain.com www.your-domain.com;
    root /var/www/antrian/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

**Catatan:** Ganti `your-domain.com` dengan domain Anda, atau gunakan IP VPS.

#### 8.2 Aktifkan Site
```bash
sudo ln -s /etc/nginx/sites-available/antrian /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

### Langkah 9: Setup SSL dengan Let's Encrypt (Opsional tapi Disarankan)

#### 9.1 Install Certbot
```bash
sudo apt install -y certbot python3-certbot-nginx
```

#### 9.2 Generate SSL Certificate
```bash
sudo certbot --nginx -d your-domain.com -d www.your-domain.com
```

Ikuti instruksi yang muncul. Certbot akan otomatis mengkonfigurasi Nginx untuk HTTPS.

### Langkah 10: Setup Queue Worker (Opsional)

Jika aplikasi menggunakan queue, setup supervisor untuk menjalankan queue worker:

#### 10.1 Install Supervisor
```bash
sudo apt install -y supervisor
```

#### 10.2 Buat Konfigurasi Supervisor
```bash
sudo nano /etc/supervisor/conf.d/antrian-worker.conf
```

Tambahkan:
```ini
[program:antrian-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/antrian/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/var/www/antrian/storage/logs/worker.log
stopwaitsecs=3600
```

#### 10.3 Start Supervisor
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start antrian-worker:*
```

### Langkah 11: Setup Firewall

#### 11.1 Install UFW (jika belum ada)
```bash
sudo apt install -y ufw
```

#### 11.2 Konfigurasi Firewall
```bash
sudo ufw allow OpenSSH
sudo ufw allow 'Nginx Full'
sudo ufw enable
```

### Langkah 12: Verifikasi Deployment

1. **Akses aplikasi di browser:**
   - HTTP: `http://your-domain.com` atau `http://IP_VPS`
   - HTTPS: `https://your-domain.com` (jika sudah setup SSL)

2. **Login dengan kredensial default:**
   - Email: `admin@gmail.com`
   - Password: `admin123`

3. **Ubah password admin setelah login pertama kali!**

### Troubleshooting

#### Error: 502 Bad Gateway
```bash
# Cek status PHP-FPM
sudo systemctl status php8.2-fpm
sudo systemctl restart php8.2-fpm
```

#### Error: Permission Denied
```bash
# Fix permissions
sudo chown -R www-data:www-data /var/www/antrian
sudo chmod -R 775 /var/www/antrian/storage
sudo chmod -R 775 /var/www/antrian/bootstrap/cache
```

#### Error: Database Connection
- Pastikan database sudah dibuat
- Cek username dan password di file `.env`
- Pastikan MySQL service berjalan: `sudo systemctl status mysql`

#### View Logs
```bash
# Laravel logs
tail -f /var/www/antrian/storage/logs/laravel.log

# Nginx error logs
sudo tail -f /var/log/nginx/error.log

# PHP-FPM logs
sudo tail -f /var/log/php8.2-fpm.log
```

### Maintenance Commands

```bash
# Clear cache
cd /var/www/antrian
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Re-optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Update application
git pull origin main
composer install --optimize-autoloader --no-dev
npm install && npm run build
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Tips Keamanan

1. **Ubah password default admin** setelah login pertama
2. **Gunakan SSL/HTTPS** untuk enkripsi data
3. **Update sistem secara berkala:** `sudo apt update && sudo apt upgrade`
4. **Backup database secara rutin**
5. **Gunakan firewall** (UFW) untuk membatasi akses
6. **Jangan set APP_DEBUG=true** di production

---

**Selamat! Aplikasi Antrian sudah berhasil di-deploy ke VPS Anda! 🎉**

