# dot-js-php-render

Uses for render doT templates in PHP

Uses example:

<?php
    $doT = doT::instance(DOCROOT.'/modules/Work/views/PR/Products/doT/option-content.php', 1);
    $doT -> assign([
      'id' => 2,
      'options' => 'options-list'
    ]);
    echo $doT-> render('option-content-tmpl');
?>
