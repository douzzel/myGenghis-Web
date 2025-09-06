<?php

$config_file = file_exists(__DIR__ . "/../config.php") ? "config.php" : "config.sample.php";
require_once(__DIR__ . "/../{$config_file}");

class MYSQL
{

	private static $isEnabled = false;

	//singleton
	public static function query($sql)
	{
		new self();

		// DB Config is editable in config.php file (see config.sample.php)
		$base = mysqli_connect(MYSQL_HOST, MYSQL_USER, MYSQL_PASSWORD, MYSQL_DB);

		$base->set_charset("utf8mb4");
		$query = mysqli_query($base, $sql) or die(mysqli_error($base));
		if ($query === false) {
			self::error('Dans la requête : <br />' . $sql, mysqli_error($base));
		}

		return $query;
	}

	public static function fetchArray($query): ?array
	{
		return mysqli_fetch_array($query);
	}

	public static function fetchRow($query): ?array
	{
		return mysqli_fetch_row($query);
	}

	public static function fetchAssoc($query): ?array
	{
		return mysqli_fetch_assoc($query);
	}

	public static function freeResult($query): void
	{
		mysqli_free_result($query);
	}

	public static function numRows($query)
	{
		return mysqli_num_rows($query);
	}

	public static function isRows($query)
	{
		return (self::numRows($query) != 0);
	}

	public static function selectOneRow($sql): ?array
	{
		$query = self::query($sql);
		$row = self::fetchArray($query);
		self::freeResult($query);
		return $row;
	}

	public static function selectOneValue($sql, $index = 0): ?string
	{
		$query = self::query($sql);
		$row = self::fetchRow($query);
		self::freeResult($query);
		return $row ? $row[$index] : '';
	}

	private static function error($txt, $erreur)
	{
		trigger_error('Erreur MySQL : ' . $txt . '<br />Nom erreur : ' . $erreur . '<br />', E_USER_ERROR);
	}

	public static function mysqli_field_name($result, $field_offset)
	{
		$properties = mysqli_fetch_field_direct($result, $field_offset);
		return is_object($properties) ? $properties->name : null;
	}
}

// 'ERP'-BSM
class ERPMYSQL
{
	private static $isEnabled = false;
	public static function query($sql)
	{
		new self();
		$base = mysqli_connect(MYSQL_HOST, MYSQL_USER, MYSQL_PASSWORD, MYSQL_USER . '_myg_demo_graphene_erp');
		$base->set_charset("utf8");
		$query = mysqli_query($base, $sql) or die(mysqli_error($base));
		if ($query === false) {
			self::error('Dans la requête : <br />' . $sql, mysqli_error($base));
		}
		return $query;
	}
	public static function fetchArray($query)
	{
		return mysqli_fetch_array($query);
	}
	public static function fetchRow($query)
	{
		return mysqli_fetch_row($query);
	}
	public static function fetchAssoc($query)
	{
		return mysqli_fetch_assoc($query);
	}
	public static function freeResult($query)
	{
		mysqli_free_result($query);
	}
	public static function numRows($query)
	{
		return mysqli_num_rows($query);
	}

	public static function isRows($query)
	{
		return (self::numRows($query) != 0);
	}
	public static function selectOneRow($sql)
	{
		$query = self::query($sql);
		$row = self::fetchArray($query);
		self::freeResult($query);
		return $row;
	}
	public static function selectOneValue($sql, $index = 0)
	{
		$query = self::query($sql);
		$row = self::fetchRow($query);
		self::freeResult($query);
		return $row[$index];
	}
	private static function error($txt, $erreur)
	{
		trigger_error('Erreur MySQL : ' . $txt . '<br />Nom erreur : ' . $erreur . '<br />', E_USER_ERROR);
	}
	public static function mysqli_field_name($result, $field_offset)
	{
		$properties = mysqli_fetch_field_direct($result, $field_offset);
		return is_object($properties) ? $properties->name : null;
	}
}

// Nextcloud
class NCMYSQL
{
	private static $isEnabled = false;
	public static function query($sql)
	{
		new self();
		$base = mysqli_connect(MYSQL_HOST, MYSQL_USER, MYSQL_PASSWORD, MYSQL_USER . '_myg_demo_graphene_nextcloud');
		$base->set_charset("utf8");
		$query = mysqli_query($base, $sql) or die(mysqli_error($base));
		if ($query === false) {
			self::error('Dans la requête : <br />' . $sql, mysqli_error($base));
		}
		return $query;
	}
	public static function fetchArray($query)
	{
		return mysqli_fetch_array($query);
	}
	public static function fetchRow($query)
	{
		return mysqli_fetch_row($query);
	}
	public static function fetchAssoc($query)
	{
		return mysqli_fetch_assoc($query);
	}
	public static function freeResult($query)
	{
		mysqli_free_result($query);
	}
	public static function numRows($query)
	{
		return mysqli_num_rows($query);
	}

	public static function isRows($query)
	{
		return (self::numRows($query) != 0);
	}
	public static function selectOneRow($sql)
	{
		$query = self::query($sql);
		$row = self::fetchArray($query);
		self::freeResult($query);
		return $row;
	}
	public static function selectOneValue($sql, $index = 0)
	{
		$query = self::query($sql);
		$row = self::fetchRow($query);
		self::freeResult($query);
		return $row[$index];
	}
	private static function error($txt, $erreur)
	{
		trigger_error('Erreur MySQL : ' . $txt . '<br />Nom erreur : ' . $erreur . '<br />', E_USER_ERROR);
	}
	public static function mysqli_field_name($result, $field_offset)
	{
		$properties = mysqli_fetch_field_direct($result, $field_offset);
		return is_object($properties) ? $properties->name : null;
	}
}

// Livezilla
class LZMYSQL
{
	private static $isEnabled = false;
	public static function query($sql)
	{
		new self();
		$base = mysqli_connect(MYSQL_HOST, MYSQL_USER, MYSQL_PASSWORD, MYSQL_USER . '_myg_demo_graphene_livezilla');
		$base->set_charset("utf8");
		$query = mysqli_query($base, $sql) or die(mysqli_error($base));
		if ($query === false) {
			self::error('Dans la requête : <br />' . $sql, mysqli_error($base));
		}
		return $query;
	}
	public static function fetchArray($query)
	{
		return mysqli_fetch_array($query);
	}
	public static function fetchRow($query)
	{
		return mysqli_fetch_row($query);
	}
	public static function fetchAssoc($query)
	{
		return mysqli_fetch_assoc($query);
	}
	public static function freeResult($query)
	{
		mysqli_free_result($query);
	}
	public static function numRows($query)
	{
		return mysqli_num_rows($query);
	}
	public static function isRows($query)
	{
		return (self::numRows($query) != 0);
	}
	public static function selectOneRow($sql)
	{
		$query = self::query($sql);
		$row = self::fetchArray($query);
		self::freeResult($query);
		return $row;
	}
	public static function selectOneValue($sql, $index = 0)
	{
		$query = self::query($sql);
		$row = self::fetchRow($query);
		self::freeResult($query);
		return $row[$index];
	}
	private static function error($txt, $erreur)
	{
		trigger_error('Erreur MySQL : ' . $txt . '<br />Nom erreur : ' . $erreur . '<br />', E_USER_ERROR);
	}
	public static function mysqli_field_name($result, $field_offset)
	{
		$properties = mysqli_fetch_field_direct($result, $field_offset);
		return is_object($properties) ? $properties->name : null;
	}
}

// Akaunting
class AKMYSQL
{
	private static $isEnabled = false;
	public static function query($sql)
	{
		new self();
		$base = mysqli_connect(MYSQL_HOST, MYSQL_USER, MYSQL_PASSWORD, MYSQL_USER . '_myg_demo_graphene_akaunting');
		$base->set_charset("utf8");
		$query = mysqli_query($base, $sql) or die(mysqli_error($base));
		if ($query === false) {
			self::error('Dans la requête : <br />' . $sql, mysqli_error($base));
		}
		return $query;
	}
	public static function fetchArray($query)
	{
		return mysqli_fetch_array($query);
	}
	public static function fetchRow($query)
	{
		return mysqli_fetch_row($query);
	}
	public static function fetchAssoc($query)
	{
		return mysqli_fetch_assoc($query);
	}
	public static function freeResult($query)
	{
		mysqli_free_result($query);
	}
	public static function numRows($query)
	{
		return mysqli_num_rows($query);
	}
	public static function isRows($query)
	{
		return (self::numRows($query) != 0);
	}
	public static function selectOneRow($sql)
	{
		$query = self::query($sql);
		$row = self::fetchArray($query);
		self::freeResult($query);
		return $row;
	}
	public static function selectOneValue($sql, $index = 0)
	{
		$query = self::query($sql);
		$row = self::fetchRow($query);
		self::freeResult($query);
		return $row[$index];
	}
	private static function error($txt, $erreur)
	{
		trigger_error('Erreur MySQL : ' . $txt . '<br />Nom erreur : ' . $erreur . '<br />', E_USER_ERROR);
	}
	public static function mysqli_field_name($result, $field_offset)
	{
		$properties = mysqli_fetch_field_direct($result, $field_offset);
		return is_object($properties) ? $properties->name : null;
	}
}

class PHPCALSQL
{
	private static $isEnabled = false;
	public static function query($sql)
	{
		new self();
		$base = mysqli_connect(MYSQL_HOST, MYSQL_USER, MYSQL_PASSWORD, MYSQL_USER . '_myg_demo_graphene_calendar');
		$base->set_charset("utf8");
		$query = mysqli_query($base, $sql) or die(mysqli_error($base));
		if ($query === false) {
			self::error('Dans la requête : <br />' . $sql, mysqli_error($base));
		}
		return $query;
	}
	public static function fetchArray($query)
	{
		return mysqli_fetch_array($query);
	}
	public static function fetchRow($query)
	{
		return mysqli_fetch_row($query);
	}
	public static function fetchAssoc($query)
	{
		return mysqli_fetch_assoc($query);
	}
	public static function freeResult($query)
	{
		mysqli_free_result($query);
	}
	public static function numRows($query)
	{
		return mysqli_num_rows($query);
	}
	public static function isRows($query)
	{
		return (self::numRows($query) != 0);
	}
	public static function selectOneRow($sql)
	{
		$query = self::query($sql);
		$row = self::fetchArray($query);
		self::freeResult($query);
		return $row;
	}
	public static function selectOneValue($sql, $index = 0)
	{
		$query = self::query($sql);
		$row = self::fetchRow($query);
		self::freeResult($query);
		return $row[$index];
	}
	private static function error($txt, $erreur)
	{
		trigger_error('Erreur MySQL : ' . $txt . '<br />Nom erreur : ' . $erreur . '<br />', E_USER_ERROR);
	}
	public static function mysqli_field_name($result, $field_offset)
	{
		$properties = mysqli_fetch_field_direct($result, $field_offset);
		return is_object($properties) ? $properties->name : null;
	}
}

class PHPROJECTSQL
{
	private static $isEnabled = false;
	public static function query($sql)
	{
		new self();
		$base = mysqli_connect(MYSQL_HOST, MYSQL_USER, MYSQL_PASSWORD, MYSQL_USER . '_myg_demo_graphene_phproject');
		$base->set_charset("utf8");
		$query = mysqli_query($base, $sql) or die(mysqli_error($base));
		if ($query === false) {
			self::error('Dans la requête : <br />' . $sql, mysqli_error($base));
		}
		return $query;
	}
	public static function fetchArray($query)
	{
		return mysqli_fetch_array($query);
	}
	public static function fetchRow($query)
	{
		return mysqli_fetch_row($query);
	}
	public static function fetchAssoc($query)
	{
		return mysqli_fetch_assoc($query);
	}
	public static function freeResult($query)
	{
		mysqli_free_result($query);
	}
	public static function numRows($query)
	{
		return mysqli_num_rows($query);
	}
	public static function isRows($query)
	{
		return (self::numRows($query) != 0);
	}
	public static function selectOneRow($sql)
	{
		$query = self::query($sql);
		$row = self::fetchArray($query);
		self::freeResult($query);
		return $row;
	}
	public static function selectOneValue($sql, $index = 0)
	{
		$query = self::query($sql);
		$row = self::fetchRow($query);
		self::freeResult($query);
		return $row[$index];
	}
	private static function error($txt, $erreur)
	{
		trigger_error('Erreur MySQL : ' . $txt . '<br />Nom erreur : ' . $erreur . '<br />', E_USER_ERROR);
	}
	public static function mysqli_field_name($result, $field_offset)
	{
		$properties = mysqli_fetch_field_direct($result, $field_offset);
		return is_object($properties) ? $properties->name : null;
	}
}
