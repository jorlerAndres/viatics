<?php

use Psr\Container\ContainerInterface;

$container->set('db', function (ContainerInterface $c) {

    $config = $c->get('db_settings');
});
