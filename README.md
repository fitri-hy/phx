# PHX
Fast | Secure | Powerful

<p align="center">
  <img src="./public/images/logo/logo.png" alt="Logo" width="200">
</p>

### Required

- Nodejs
- Composer
- PHP 8.0+

### Starter

```
git clone https://github.com/fitri-hy/phx.git
cd phx
npm run phx-install
npm run phx-start

# Live
npm run phx-live
```

### Folder Structure
```bash
phx_framework/  
│── core/
│   ├── PHXController.php
│   ├── PHXDatabase.php
│   ├── PHXFramework.php
│   ├── PHXLayout.php
│   ├── PHXLogger.php
│   └── PHXRouter.php
│  
├── public/
│   ├── css/
│   ├── images/
│   ├── js/
│   ├── .htaccess
│   ├── index.php
│   ├── manifest.json
│   └── service-worker.js
│  
├── routes/
│   ├── api.php
│   └── web.php
│  
├── src/
│   ├── api/
│   │   └── Welcome.php
│   ├── components/
│   │   └── PWA.php
│   ├── pages/
│   │   ├── NotFoundPage.php
│   │   └── HomePage.php
│   └── App.php
│
├── vendor/
│
├── .htaccess
├── gulpfile.js
├── composer.json
├── package.json
└── README.md
```

### Use Database
```php
<?php
// import
use Core\PHXDatabase;

// initialization
PHXDatabase::getInstance();
```

### Use CSRF Token
```php
// Generate
$csrfToken = $_SESSION['csrf_token'] ?? '';

// in rendering
[input type:'hidden' name:'csrf_token' value:'" . $csrfToken . "']
```

```php
// Verification
<?php
private static function verifyCsrfToken() {
    return $_SERVER['REQUEST_METHOD'] === 'GET' || ($_SESSION['csrf_token'] ?? '') === ($_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');
}
```

### Use Logger
```php
use Core\PHXLogger;
PHXLogger::displayError('Error Message');
```