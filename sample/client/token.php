<?php
/*
IronChannel is licensed under the Apache License 2.0 license
https://github.com/trp-solutions/IronChannel/blob/main/LICENSE
*/
declare(strict_types=1);
require_once __DIR__.'/../../lib/autoload.php';
require_once __DIR__.'/function.php';

function generate_token() {
	sample_header('Generating Token');
	$characters = '02X';
	$string = '';
	for($i = 0; $i < 6; $i++) {
		$string .= $characters[rand(0, strlen($characters)-1)];
	}
	return new \TRP\IronChannel\Token($string,10);
}

\TRP\IronChannel\TokenHandler::setrefresh('generate_token');

sample_start();

sample_header('Token');
$token = \TRP\IronChannel\TokenHandler::get('sample_1');
echo 'Current: '.$token.PHP_EOL;
exit;
