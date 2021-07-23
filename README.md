AWS S3 для WebSpace Engine
====
######(Плагин)

Плагин для интеграции с файловым хранилищем через протокол AWS S3.

#### Установка
Docker + BASH:
```
% ./composer install
```

Поместить в папку `plugin` и подключить в `index.php` добавив строку:
```php
// s3proto plugin
$plugins->register(new \Plugin\S3Proto\S3ProtoPlugin($container));
```

#### License
Licensed under the MIT license. See [License File](LICENSE.md) for more information.
