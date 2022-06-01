<?php
require DIR_DOMPDF . '/../vendor/autoload.php';
require DIR_DOMPDF . '/Client/Server.php';

use GuzzleHttp\Tests\Ring\Client\Server;

Server::start();

register_shutdown_function(function () {
    Server::stop();
});
