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
│   ├── PHXORM.php
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
### Use PHX Markup
```php
USE_PHX_ENGINE=true

# set to false to use html markup
```

**Example PHX Markup**
```php
[!PHX html]
[html lang:'id']
[head]
    [meta charset:'UTF-8']
    [meta name:'viewport' content:'width:device-width, initial-scale:1.0']
    [title]Page Title[/title]
[/head]
[body id:'main' class:'container']
    [h1]Hello, World![/h1]
    [p]This is a basic HTML page.[/p]
    [a class:'hover?text-green-600' href:'https@google.com']Go to link[/a]
[/body]
[/html]
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

### Use ORM

| **Method**                      | **Description**                                                                                                                                                             | **Usage Example**                                                                                                                                 |
|----------------------------------|-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------|---------------------------------------------------------------------------------------------------------------------------------------------------|
| `getTableName()`                 | Retrieves the name of the table associated with the current class, based on the class name.                                                                                  | `$model->getTableName();`                                                                                                                          |
| `save()`                         | Saves the current data (attributes) to the associated table.                                                                                                               | `$model->save();`                                                                                                                                 |
| `all()`                          | Retrieves all records from the associated table.                                                                                                                             | `$model->all();`                                                                                                                                  |
| `find($id)`                      | Retrieves a record by its ID.                                                                                                                                                 | `$model->find(1);`                                                                                                                                 |
| `setAttributes($attributes)`     | Sets the attributes to be saved or updated in the table.                                                                                                                     | `$model->setAttributes(['name' => 'John', 'email' => 'john@example.com']);`                                                                   |
| `update($id, $attributes)`       | Updates the record identified by its ID with the specified attributes.                                                                                                       | `$model->update(1, ['name' => 'John Doe']);`                                                                                                    |
| `delete($id)`                    | Deletes the record identified by its ID.                                                                                                                                      | `$model->delete(1);`                                                                                                                             |
| `where($conditions)`             | Executes a query with a `WHERE` condition (e.g., multiple conditions with `AND`).                                                                                           | `$model->where(['name' => 'John', 'age' => 30]);`                                                                                              |
| `join($table, $onCondition, $type)` | Executes a `JOIN` query with the specified table and on-condition. Optionally supports different join types (`INNER`, `LEFT`, etc.).                                           | `$model->join('posts', 'users.id = posts.user_id', 'INNER');`                                                                                   |
| `groupBy($columns)`              | Executes a `GROUP BY` query on the specified columns.                                                                                                                        | `$model->groupBy(['category']);`                                                                                                                 |
| `having($groupByColumns, $havingConditions)` | Executes a `HAVING` query after a `GROUP BY`, with the specified conditions.                                                                                                   | `$model->having(['category'], ['COUNT(*) > 5']);`                                                                                              |
| `orderBy($columns, $direction)`  | Executes an `ORDER BY` query on the specified columns, sorting in ascending (`ASC`) or descending (`DESC`) order.                                                           | `$model->orderBy(['name'], 'ASC');`                                                                                                              |
| `limit($limit, $offset)`         | Executes a query with a `LIMIT` and `OFFSET` to restrict the number of rows returned.                                                                                       | `$model->limit(10, 20);`                                                                                                                         |
| `distinct($columns)`             | Executes a `SELECT DISTINCT` query on the specified columns.                                                                                                                | `$model->distinct(['name']);`                                                                                                                    |
| `count($column)`                 | Executes a `COUNT` query to count the number of records, optionally for a specific column.                                                                                   | `$model->count();` (counts all rows) or `$model->count('name');` (counts distinct values of 'name')                                              |
| `raw($sql, $params)`             | Executes a raw SQL query with the provided parameters and returns the result.                                                                                               | `$model->raw('SELECT * FROM users WHERE age > :age', ['age' => 25]);`                                                                          |

### Example Usage for Common Tasks

```php
<?php
namespace Src\Pages;

use Core\PHXController;
use Src\App;
use Core\PHXORM;

class Examples {
  public function GetAll() {
    return implode('', array_map(fn($fruit) =>"
      [li]
        [span]{$fruit['title']}[/span] 
        [span]{$fruit['price']}[/span]
      [/li]
    ", (new class extends PHXORM {
      protected $table = 'fruits';
      protected $primaryKey = 'id';
      protected $attributes = ['title', 'price'];
    })->GetAll()));
  }
  public function index() {
    $fruitList = $this->GetAll();
    PHXController::render("
      [div]
        $fruitList
      [/div]
    ");
  }
}
```

**Insert New Record**:  
  ```php
  $user = new User();
  $user->setAttributes(['name' => 'John Doe', 'email' => 'john@example.com']);
  $user->save();
  ```

**Fetch All Records**:  
  ```php
  $users = $user->all();
  ```

**Find a Record by ID**:  
  ```php
  $user = $user->find(1);
  ```

**Update a Record**:  
  ```php
  $user->update(1, ['name' => 'John Doe Updated']);
  ```

**Delete a Record**:  
  ```php
  $user->delete(1);
  ```

**Run a Custom Query with WHERE**:  
  ```php
  $users = $user->where(['name' => 'John', 'age' => 30]);
  ```

**Run a JOIN Query**:  
  ```php
  $usersWithPosts = $user->join('posts', 'users.id = posts.user_id', 'LEFT');
  ```

**Group by Column**:  
  ```php
  $groupedUsers = $user->groupBy(['age']);
  ```

**Count Records**:  
  ```php
  $userCount = $user->count();
  ```
