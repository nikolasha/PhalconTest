<?php

date_default_timezone_set('Europe/Moscow');

require __DIR__ . '/../app/source/Application.php';

AppTest\Application::init()->run();
