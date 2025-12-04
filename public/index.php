<?php 

use App\App;
use Dotenv\Dotenv;

include_once '../vendor/autoload.php';

$dotenv = Dotenv::createUnsafeImmutable(dirname(__DIR__));
$dotenv->load();

$app = new App();
$app->start();

