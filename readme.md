# Package Name: hummel/php-frame
## I tried to respect the SOLID convention and follow the best practices.
### For CDA and other PHP projects.
## Installation:

```bash
composer require hummel/php-frame
```

### If you want contribute to the projet please do so on [Github](https://github.com/HummelJulien/php-frame).
#### Ask me and I will add you as a ***collaborator***
```bash
composer require hummel/php-frame --prefer-source
```


### First time setup:

- Copy the content of folder resources to your root project folder
- Config two files in config folder: `pdo.yaml` and `routes.yaml`
- Config your apache server to use as document root the folder public


example of apache config:
```apacheconf
<VirtualHost *:80>
    ServerName localhost
    DocumentRoot /var/www/html/public
    <Directory /var/www/html/public>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

## Routes:
example: config/routes.yaml

```yaml
demo:                                                         # route name
  routeUri: /user/{id}                                        # route uri with capture parameters
  controller: Hummel\PhpFrame\Controllers\DemoController      # controller class name
  action: showOnce                                            # call methode
  methode: GET/POST                                           # get/post/put/delete
  parameters:                         # parameters for the methode
      id:
        type: int             # int, string, float
        regex: /^[0-9]{1,3}$/ # regex for validation
```

## Model:
Models are the main data structure of the application.
Is auto bind to the database if you repect the convention of my frame.
```php
<?php

namespace App\Models;

use Hummel\PhpFrame\Models\OrmAbstract;

class Model extends OrmAbstract
{
    protected string $tableName;

    public function __construct()
    {
        $this->tableName = 'your_table_name';
        parent::__construct();
    }
}
```
### Than you can use the model:
```php
<?php
    $models = (new Models())->getAll();
    
    $model = (new Models())->getOneBy(['column_name' => "value"]);

    $model = (new Models())->getOneBy(['column_name_1' => "value", 'column_name_2' => 'value']);

	$model = (new Models())->getBy(['column_name_1' => "value", 'column_name_2' => 'value']);

    $model->column_name = 'value';

	$model->save();

	$model->delete();

    $model->withObject(JoinableInterface $model, string $cond_table_primary, string $cond_table_secondary);

```

## Controller: example for /user/{id}
The parameters are passed to the methode as an associative array.
that is contianed in: ```$this->route->getParams()['id'];```.

```php
<?php

namespace App\Controllers;

use Hummel\PhpFrame\Controllers\BaseController;
use App\Models\Model;
use App\Models\Comment;

class DemoController extends BaseController
{
    public function index()
    {
        $id = $this->route->getParams()['id'];
        $users = (new Model())->getAll();

        $comment = new Comment();
        $comment->getOneBy(['email' => "prout"]);

        $this->render('path/to/your/template.php', [
            'usersTemplateVariable' => $users,
        ]);
    }
}
```

## template:

The `}}` tag is replaced by ```\<?php``` and the `%}` tag is replaced by `?>`, this is for logical PHP code

The party `{{` it by `\<?=` and `}}` by `?>`, this party is to perform echo

```twig
<body class="container pt-5">
    <h1>All users</h1>
        <p>
        {% if (isset($usersTemplateVariable) && is_array($usersTemplateVariable)): %}
            {% foreach ($usersTemplateVariable as $user) { %}
                <p>{{ $user->email }}</p>
            {% } %}

        {% else: %}
            L'email n'est pas défini
        {% endif %}
        </p>
</body>
```

To perform this
```php
<body class="container pt-5">
    <h1>All users</h1>
        <p>
        <?php  if (isset($usersTemplateVariable) && is_array($usersTemplateVariable)): ; ?>
            <?php  foreach ($usersTemplateVariable as $user) { ; ?>
                <p><?=  $user->email ; ?></p>
            <?php  } ; ?>

        <?php  else: ; ?>
            L'email n'est pas défini
        <?php  endif ; ?>
        </p>
</body>
```
