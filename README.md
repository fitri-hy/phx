### Folder Structure
```bash
phx_framework/  
│── core/
│   ├── PHXController.php
│   ├── PHXFramework.php
│   ├── PHXLayout.php
│   └─ PHXRouter.php
│  
├── public/
│   └─ index.php
│  
├── routes/
│   ├── api.php
│   └─ web.php
│  
├── src/
│   ├── api/
│   │   └── Welcome.php
│   ├── components/
│   │   └── Button.php
│   ├── pages/
│   │   ├── NotFoundPage.php
│   │   └── HomePage.php
│   └── App.php
│
├── vendor/
│
├── .htaccess
├── composer.json
└── README.md
```

### Use Database
```php
<?php
use Core\PHXDatabase;
PHXDatabase::getInstance();
```

### Use CSRF Token
```php
// Generate
$csrfToken = $_SESSION['csrf_token'] ?? '';
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




