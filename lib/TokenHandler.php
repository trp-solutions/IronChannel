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
	private static ?\Closure $refreshfunc = null;

	public static function get(string $key) : string {
		$m = new \Memcached();
		$m->addServer(self::$host, self::$port);

		$token = $m->get($key);
		if($m->getResultCode()===\Memcached::RES_SUCCESS) {
			$m->quit();
			return $token;
		}
		elseif($m->getResultCode()===\Memcached::RES_NOTFOUND) {
			$renew = self::refresh();
			$m->set($key,$renew->get(),$renew->exp()-self::$grace);
			$m->quit();
			return $renew->get();
		}
		else {
			throw new \Exception('TRP\IronChannel\TokenHandler::get Memcached error',500);
		}
	}

	private static function refresh() : Token {
		if(self::$refreshfunc === null) {
			throw new \Exception('TRP\IronChannel\TokenHandler::refresh callback not configured',500);
		}
		$return = (self::$refreshfunc)();
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
	public static function setrefresh(callable $refreshfunc) : void {
		self::$refreshfunc = \Closure::fromCallable($refreshfunc);
	}
}
