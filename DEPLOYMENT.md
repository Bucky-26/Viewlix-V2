# Viewlix v2 - Hostinger Cloud Deployment Guide

## Prerequisites
- Hostinger Cloud VPS (Ubuntu 22.04/24.04 recommended)
- Domain name pointed to your VPS IP
- Git repository (GitHub/GitLab) with your project

---

## Step 1: Push Your Project to Git

```bash
# Initialize git (if not already)
git init
git add .
git commit -m "Initial commit - Viewlix v2"

# Add remote and push
git remote add origin https://github.com/yourusername/viewlix_v2.git
git branch -M main
git push -u origin main
```

### Important: Create `.gitignore`
Make sure these are in your `.gitignore`:
```
/node_modules
/public/build
/public/hot
/public/storage
/storage/*.key
/vendor
.env
.env.backup
.env.production
.phpactor.json
.phpunit.result.cache
Homestead.json
Homestead.yaml
auth.json
npm-debug.log
yarn-error.log
/.fleet
/.idea
/.vscode
```

---

## Step 2: Hostinger Cloud VPS Setup

### 2.1 Access Your VPS
1. Go to Hostinger hPanel → VPS → Manage
2. Copy your SSH credentials
3. Connect via SSH:
```bash
ssh root@your-vps-ip
```

### 2.2 Update System & Install Required Software

```bash
# Update system
apt update && apt upgrade -y

# Install required packages
apt install -y nginx mysql-server php8.3 php8.3-fpm php8.3-mysql php8.3-mbstring php8.3-xml php8.3-bcmath php8.3-curl php8.3-zip php8.3-gd unzip git curl

# Install Composer
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer

# Install Node.js (for npm)
curl -fsSL https://deb.nodesource.com/setup_20.x | bash -
apt install -y nodejs
```

---

## Step 3: MySQL Database Setup

```bash
# Secure MySQL installation
mysql_secure_installation

# Login to MySQL
mysql -u root -p

# Create database and user
CREATE DATABASE viewlix_db;
CREATE USER 'viewlix_user'@'localhost' IDENTIFIED BY 'YourStrongPassword123!';
GRANT ALL PRIVILEGES ON viewlix_db.* TO 'viewlix_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

---

## Step 4: Clone Your Project

```bash
# Create web directory
mkdir -p /var/www
cd /var/www

# Clone your repository
git clone https://github.com/yourusername/viewlix_v2.git viewlix

# Set permissions
cd viewlix
chown -R www-data:www-data /var/www/viewlix
chmod -R 755 /var/www/viewlix
chmod -R 775 /var/www/viewlix/storage
chmod -R 775 /var/www/viewlix/bootstrap/cache
```

---

## Step 5: Install Dependencies

```bash
cd /var/www/viewlix

# Install PHP dependencies
composer install --optimize-autoloader --no-dev

# Install Node dependencies and build
npm install
npm run build
```

---

## Step 6: Configure Laravel Environment

```bash
# Copy environment file
cp .env.example .env

# Generate app key
php artisan key:generate

# Edit .env file
nano .env
```

### Production `.env` Configuration:
```env
APP_NAME=Viewlix
APP_ENV=production
APP_KEY=  # Generated above
APP_DEBUG=false
APP_URL=https://yourdomain.com

LOG_CHANNEL=daily
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=viewlix_db
DB_USERNAME=viewlix_user
DB_PASSWORD=YourStrongPassword123!

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-gmail@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-gmail@gmail.com
MAIL_FROM_NAME="Viewlix"

TMDB_API_KEY=your_tmdb_api_key
TMDB_ACCESS_TOKEN=your_tmdb_access_token
TMDB_BASE_URL=https://api.themoviedb.org/3
TMDB_IMAGE_BASE_URL=https://image.tmdb.org/t/p

SESSION_DRIVER=database
CACHE_DRIVER=file
QUEUE_CONNECTION=database
```

---

## Step 7: Run Laravel Setup Commands

```bash
cd /var/www/viewlix

# Run migrations
php artisan migrate --force

# Cache configuration for performance
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Create storage link
php artisan storage:link
```

---

## Step 8: Configure Nginx

```bash
# Create Nginx configuration
nano /etc/nginx/sites-available/viewlix
```

### Nginx Configuration:
```nginx
server {
    listen 80;
    listen [::]:80;
    server_name yourdomain.com www.yourdomain.com;
    root /var/www/viewlix/public;

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
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### Enable the site:
```bash
# Enable site
ln -s /etc/nginx/sites-available/viewlix /etc/nginx/sites-enabled/

# Remove default site
rm /etc/nginx/sites-enabled/default

# Test configuration
nginx -t

# Restart Nginx
systemctl restart nginx
```

---

## Step 9: Install SSL Certificate (Let's Encrypt)

```bash
# Install Certbot
apt install -y certbot python3-certbot-nginx

# Get SSL certificate
certbot --nginx -d yourdomain.com -d www.yourdomain.com

# Test auto-renewal
certbot renew --dry-run
```

---

## Step 10: Set Up Queue Worker (Optional but Recommended)

For email sending and background jobs:

```bash
# Create systemd service
nano /etc/systemd/system/viewlix-worker.service
```

### Queue Worker Service:
```ini
[Unit]
Description=Viewlix Queue Worker
After=network.target

[Service]
User=www-data
Group=www-data
Restart=always
ExecStart=/usr/bin/php /var/www/viewlix/artisan queue:work --sleep=3 --tries=3 --max-time=3600

[Install]
WantedBy=multi-user.target
```

### Enable and start:
```bash
systemctl daemon-reload
systemctl enable viewlix-worker
systemctl start viewlix-worker
```

---

## Step 11: Set Up Cron for Laravel Scheduler

```bash
# Edit crontab
crontab -e

# Add this line:
* * * * * cd /var/www/viewlix && php artisan schedule:run >> /dev/null 2>&1
```

---

## Deployment Checklist ✅

- [ ] VPS provisioned with Ubuntu
- [ ] Domain DNS pointed to VPS IP
- [ ] MySQL database created
- [ ] Project cloned to `/var/www/viewlix`
- [ ] Composer dependencies installed
- [ ] NPM build completed
- [ ] `.env` configured with production values
- [ ] Migrations run
- [ ] Laravel caches created
- [ ] Storage linked
- [ ] Nginx configured and running
- [ ] SSL certificate installed
- [ ] Queue worker running (optional)
- [ ] Cron job set up (optional)

---

## Updating Your Site

When you push changes to GitHub:

```bash
cd /var/www/viewlix

# Pull latest changes
git pull origin main

# Install any new dependencies
composer install --optimize-autoloader --no-dev
npm install && npm run build

# Run new migrations
php artisan migrate --force

# Clear and rebuild caches
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Restart queue worker (if using)
systemctl restart viewlix-worker
```

---

## Troubleshooting

### Permission Issues
```bash
chown -R www-data:www-data /var/www/viewlix
chmod -R 775 /var/www/viewlix/storage
chmod -R 775 /var/www/viewlix/bootstrap/cache
```

### 500 Error - Check Logs
```bash
tail -f /var/www/viewlix/storage/logs/laravel.log
tail -f /var/log/nginx/error.log
```

### Clear All Caches
```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

### Test Email Configuration
```bash
php artisan tinker
>>> Mail::raw('Test email', function($m) { $m->to('your@email.com')->subject('Test'); });
```

---

## Security Recommendations

1. **Firewall**: Only allow ports 22, 80, 443
   ```bash
   ufw allow OpenSSH
   ufw allow 'Nginx Full'
   ufw enable
   ```

2. **Change SSH Port** (optional but recommended)

3. **Regular Updates**:
   ```bash
   apt update && apt upgrade -y
   ```

4. **Backup Database**:
   ```bash
   mysqldump -u viewlix_user -p viewlix_db > backup.sql
   ```

---

## Support

- Laravel Docs: https://laravel.com/docs
- Hostinger Support: https://support.hostinger.com
- TMDB API Docs: https://developers.themoviedb.org/3
