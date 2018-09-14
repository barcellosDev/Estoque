<?php
const br = "<br>";
date_default_timezone_set('America/Sao_Paulo');

abstract class Cnx
{
	public static $conn;

	public static function conecta()
	{
		self::$conn = new PDO("mysql:host=localhost;dbname=estoque", "root", "");

		if (self::$conn == true)
		{
			return self::$conn;
		} else
		{
			echo "Ocorreu algo de errrado na conexão ):";
		}
	}
}
?>