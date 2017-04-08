## dot-js-php-render

#### License - BSD-3-Clause
#### Uses for render doT templates in PHP

#### Use example:

```php
$doT = doT::instance(DOCROOT.'/modules/Work/views/PR/Products/doT/option-content.php', 1); // or first code of template in first arg
$doT -> assign([
 'id' => 2,
 'options' => 'options-list'
]);
echo $doT-> render('option-content-tmpl'); //find in file id content or just content
```

#### To install with composer
```
 composer require yatsenkolesh/dot-js-php-render
```
