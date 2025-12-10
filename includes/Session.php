<?php
// Simple session wrapper for admin authentication
class Session
{
	public static function start(): void
	{
		if (session_status() === PHP_SESSION_NONE) {
			session_start();
		}
	}

	public static function set(string $key, $value): void
	{
		self::start();
		$_SESSION[$key] = $value;
	}

	public static function get(string $key, $default = null)
	{
		self::start();
		return $_SESSION[$key] ?? $default;
	}

	public static function destroy(): void
	{
		if (session_status() !== PHP_SESSION_NONE) {
			session_unset();
			session_destroy();
		}
	}
}
