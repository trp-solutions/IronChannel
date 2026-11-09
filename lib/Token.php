<?php
/*
IronChannel is licensed under the Apache License 2.0 license
https://github.com/trp-solutions/IronChannel/blob/main/LICENSE
*/
declare(strict_types=1);
namespace TRP\IronChannel;

class Token {
	private string $value;
	private int $expiration;
	public function __construct(string $value, int $expiration) {
		$this->value = $value;
		$this->expiration = $expiration;
	}
	public function __toString() : string {
		return $this->value;
	}
	public function get() : string {
		return $this->value;
	}
	public function exp() : int {
		return $this->expiration;
	}
}
