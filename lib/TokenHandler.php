<?php
/*
IronChannel is licensed under the Apache License 2.0 license
https://github.com/trp-solutions/IronChannel/blob/main/LICENSE
*/
declare(strict_types=1);
namespace TRP\IronChannel;

class TokenHandler {
	private static int $grace = 0;
	private static string $host = 'localhost';
	private static int $port = 11211;
	private static string $prefix = 'IronChannel_';
	private static ?\Closure $refreshfunc = null;

	public static function get(string $key) : string {
		$m = new \Memcached();
		$m->addServer(self::$host, self::$port);

		$token = $m->get(self::$prefix.$key);
		if($m->getResultCode()===\Memcached::RES_SUCCESS) {
			$m->quit();
			return $token;
		}
		elseif($m->getResultCode()===\Memcached::RES_NOTFOUND) {
			$renew = self::refresh($key);
			$m->set(self::$prefix.$key,$renew->get(),$renew->exp() - self::$grace);
			$m->quit();
			return $renew->get();
		}
		else {
			throw new \Exception('TRP\IronChannel\TokenHandler::get Memcached error',500);
		}
	}

	public static function set(string $key,Token $input) : void {
		$m = new \Memcached();
		$m->addServer(self::$host, self::$port);
		$m->set(self::$prefix.$key,$input->get(),$input->exp() - self::$grace);
		$m->quit();
	}

	private static function refresh(string $key) : Token {
		if(self::$refreshfunc === null) {
			throw new \Exception('TRP\IronChannel\TokenHandler::refresh callback not configured',500);
		}
		$return = (self::$refreshfunc)($key);
		if(!$return instanceof Token) {
			throw new \Exception('TRP\IronChannel\TokenHandler::refresh callback must return TRP\IronChannel\Token',500);
		}
		return $return;
	}

	public static function host(string $host) : void {
		self::$host = $host;
	}
	public static function port(int $port) : void {
		self::$port = $port;
	}
	public static function grace(int $grace) : void {
		self::$grace = $grace;
	}
	public static function prefix(string $prefix) : void {
		self::$prefix = $prefix;
	}
	public static function setrefresh(callable $refreshfunc) : void {
		self::$refreshfunc = \Closure::fromCallable($refreshfunc);
	}
}
