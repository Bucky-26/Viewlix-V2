# Deploying Viewlix to Hostinger Cloud Startup

## Prerequisites
- Hostinger Cloud Startup hosting (with Composer available)
- GitHub repository connected
- Local development environment with Node.js

---

## Step 1: Prepare Files Locally (Before Deployment)

### 1.1 Build Frontend Assets
Since Hostinger doesn't have Node.js/npm, you must build assets locally:
```bash
npm install
npm run build
```

This creates the `public/build/` folder with compiled CSS and JS.

### 1.2 Files to Commit to GitHub
- ✅ `public/build/` - Must be committed (no npm on Hostinger)
- ❌ `vendor/` - Keep in .gitignore (Hostinger has Composer)

The `.gitignore` is already configured correctly for this.

---

## Step 2: GitHub Repository Setup

### 2.1 Push Your Code
```bash
git add .
git commit -m "Production build with assets"
git push origin main
```

---

## Step 3: Hostinger Setup

### 3.1 Connect GitHub Repository
1. Login to Hostinger hPanel
2. Go to **Websites** → Your website → **Git**
3. Connect your GitHub repository
4. Select the `main` branch
5. Set deployment path: `/public_html` or your domain folder

### 3.2 Install Composer Dependencies
After the code is pulled from GitHub, run via SSH or Terminal:
```bash
cd public_html
composer install --optimize-autoloader --no-dev
```

Or use the deployment helper (see Step 9).

### 3.3 Set Document Root
The document root should point to the `public` folder:
- Go to **Websites** → **Advanced** → **Website Folder**
- Set it to: `public_html/public` (or your path + `/public`)

### 3.3 Create `.htaccess` in Root (if needed)
If you can't change document root, create `.htaccess` in the main folder:
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
```

---

## Step 4: Environment Configuration

### 4.1 Create `.env` File via File Manager
Go to Hostinger File Manager and create `.env` in your project root:

```env
APP_NAME=Viewlix
APP_ENV=production
APP_KEY=base64:YOUR_APP_KEY_HERE
APP_DEBUG=false
APP_URL=https://yourdomain.com

LOG_CHANNEL=stack
LOG_LEVEL=error

# Database (from Hostinger MySQL)
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_user
DB_PASSWORD=your_database_password

# Session & Cache
SESSION_DRIVER=file
CACHE_DRIVER=file
QUEUE_CONNECTION=sync

# Firebase Configuration
FIREBASE_API_KEY="your_firebase_api_key"
FIREBASE_AUTH_DOMAIN="viewlix-v2.firebaseapp.com"
FIREBASE_DATABASE_URL="https://viewlix-v2-default-rtdb.firebaseio.com"
FIREBASE_PROJECT_ID="viewlix-v2"
FIREBASE_STORAGE_BUCKET="viewlix-v2.firebasestorage.app"
FIREBASE_MESSAGING_SENDER_ID="706887870364"
FIREBASE_APP_ID="your_firebase_app_id"
FIREBASE_MEASUREMENT_ID="your_measurement_id"
FIREBASE_VAPID_KEY="your_vapid_key"

# TMDB API
TMDB_API_KEY=your_tmdb_api_key
TMDB_BASE_URL=https://api.themoviedb.org/3
TMDB_IMAGE_BASE_URL=https://image.tmdb.org/t/p

# Mail (optional)
MAIL_MAILER=smtp
MAIL_HOST=smtp.hostinger.com
MAIL_PORT=465
MAIL_USERNAME=your_email@yourdomain.com
MAIL_PASSWORD=your_email_password
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS=your_email@yourdomain.com
MAIL_FROM_NAME="${APP_NAME}"
```

### 4.2 Generate App Key
Run locally and copy the key:
```bash
php artisan key:generate --show
```
Paste the output into `.env` as `APP_KEY`.

---

## Step 5: Firebase Service Account

### 5.1 Upload Service Account File
1. Go to Hostinger File Manager
2. Navigate to `storage/app/firebase/`
3. Create the folder if it doesn't exist
4. Upload `service-account.json`

---

## Step 6: Database Setup

### 6.1 Create MySQL Database in Hostinger
1. Go to **Databases** → **MySQL Databases**
2. Create a new database
3. Note the database name, username, and password
4. Update `.env` with these credentials

### 6.2 Run Migrations via SSH or Cron
If SSH is available:
```bash
cd public_html
php artisan migrate --force
```

If no SSH, create a temporary migration route:

**routes/web.php** (remove after use):
```php
Route::get('/run-migrations-secret-key-12345', function () {
    Artisan::call('migrate', ['--force' => true]);
    return 'Migrations completed: ' . Artisan::output();
});
```

Visit: `https://yourdomain.com/run-migrations-secret-key-12345`

**IMPORTANT: Remove this route after running!**

---

## Step 7: Storage & Permissions

### 7.1 Create Storage Link
Create a temporary route (remove after use):
```php
Route::get('/create-storage-link-secret', function () {
    Artisan::call('storage:link');
    return 'Storage linked!';
});
```

### 7.2 Set Folder Permissions
Via File Manager, set these folders to 755 or 775:
- `storage/`
- `storage/framework/`
- `storage/framework/cache/`
- `storage/framework/sessions/`
- `storage/framework/views/`
- `storage/logs/`
- `bootstrap/cache/`

---

## Step 8: Caching & Optimization

### 8.1 Create Optimization Route (remove after use)
```php
Route::get('/optimize-app-secret', function () {
    Artisan::call('config:cache');
    Artisan::call('route:cache');
    Artisan::call('view:cache');
    return 'Application optimized!';
});
```

---

## Step 9: Auto-Deployment with GitHub

### 9.1 Set Up Webhook (Optional)
Hostinger can auto-pull from GitHub when you push:
1. Go to **Git** settings in hPanel
2. Enable auto-deploy
3. Every push to `main` will update the site

### 9.2 Post-Deployment Script
Create `deploy.php` in root (for manual trigger):
```php
<?php
// Secret key to prevent unauthorized access
$secret = 'your-secret-deployment-key-here';

if (!isset($_GET['key']) || $_GET['key'] !== $secret) {
    die('Unauthorized');
}

// Clear caches
echo shell_exec('php artisan config:clear 2>&1');
echo shell_exec('php artisan cache:clear 2>&1');
echo shell_exec('php artisan view:clear 2>&1');

// Rebuild caches
echo shell_exec('php artisan config:cache 2>&1');
echo shell_exec('php artisan route:cache 2>&1');
echo shell_exec('php artisan view:cache 2>&1');

echo "\n\nDeployment complete!";
```

Access: `https://yourdomain.com/deploy.php?key=your-secret-deployment-key-here`

---

## Step 10: Update .gitignore for Production

Update `.gitignore` to include built assets:
```gitignore
/node_modules
/.pnp
.pnp.js

/storage/*.key

/.idea
/.vscode
.phpunit.result.cache
Homestead.json
Homestead.yaml

npm-debug.log*
yarn-debug.log*
yarn-error.log*

.env
.env.backup

# Keep these for production (comment out):
# /public/build
# /vendor
```

---

## Deployment Checklist

- [ ] Run `npm run build` locally
- [ ] Run `composer install --no-dev` locally
- [ ] Commit `public/build/` and `vendor/` to GitHub
- [ ] Push to GitHub
- [ ] Connect GitHub repo in Hostinger
- [ ] Set document root to `public` folder
- [ ] Create `.env` file with production settings
- [ ] Upload `service-account.json` to `storage/app/firebase/`
- [ ] Create MySQL database and update `.env`
- [ ] Run migrations
- [ ] Create storage link
- [ ] Set folder permissions
- [ ] Run optimization commands
- [ ] Remove temporary routes
- [ ] Test the application

---

## Troubleshooting

### 500 Internal Server Error
- Check `.env` exists and has correct values
- Check `storage/` and `bootstrap/cache/` are writable
- Check Laravel logs: `storage/logs/laravel.log`

### Assets Not Loading
- Verify `public/build/` folder exists
- Check `public/build/manifest.json` exists
- Clear browser cache

### Database Connection Error
- Verify database credentials in `.env`
- Check database exists in Hostinger
- Try `127.0.0.1` instead of `localhost` for DB_HOST

### Firebase Not Working
- Verify `storage/app/firebase/service-account.json` exists
- Check Firebase config values in `.env`
- Check Firebase rules are deployed

---

## Updating the Site

1. Make changes locally
2. Run `npm run build` if frontend changed
3. Commit and push to GitHub
4. Hostinger auto-pulls (or manually pull)
5. Visit optimization URL to clear caches
