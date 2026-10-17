<?php

require_once __DIR__ . '/../../config/config.php';

class Database
{
	private static ?PDO $connection = null;

	public static function getConnection(): PDO
	{
		if (self::$connection === null) {
			$dsn = sprintf(
				'pgsql:host=%s;port=%s;dbname=%s',
				DB_HOST,
				DB_PORT,
				DB_NAME
			);

			self::$connection = new PDO($dsn, DB_USER, DB_PASSWORD, [
				PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
				PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
				PDO::ATTR_EMULATE_PREPARES => false,
			]);
		}

		return self::$connection;
	}
}
