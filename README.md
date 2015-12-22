# Установка
Добавить в зависимости composer
"mediarise/cache-manager": "dev-master"

Добавить настройку в компоненты Yii2
```php
'components' => [
    'dataCache' => [
        'class' => 'mediarise\MRCacheManager',
        'hostname' => 'localhost',
        'port' => 6379,
        'database' => 0
    ],
]
```