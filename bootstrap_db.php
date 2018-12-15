<?php

use Illuminate\Database\Capsule\Manager as Capsule;

$capsule = new Capsule;

$capsule->addConnection(\Config::database);

$capsule->setAsGlobal();

$capsule->bootEloquent();