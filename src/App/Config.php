<?php
$container->set('db_settings', function () {
    return (object)[
        "DB_NAME" => "dashboardproject",
        "DB_PASS" => "",
        "DB_CHAR" => "utf8",
        "DB_HOST" => "prueba.net",
        "DB_USER" => "root",
    ];
});
