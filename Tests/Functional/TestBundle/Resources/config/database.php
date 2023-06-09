<?php

$container->loadFromExtension('doctrine', ['dbal' => ['driver' => 'pdo_sqlite', 'path' => tempnam(sys_get_temp_dir(), 'database')]]);
