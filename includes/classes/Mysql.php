<?php

class Mysql {
	function __construct($host, $user, $pass, $db) {
		$opt = [
		    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
		    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
		    PDO::ATTR_EMULATE_PREPARES   => false,
		];
		$this->pdo = new PDO("mysql:host={$host};dbname={$db};charset=utf8", $user, $pass, $opt);
	}

	function run($query, $args) {
		try {
			$stmt = $this->pdo->prepare($query);
		} catch(PDOException $e) {
			throw $e;
			return false;
		}

		if (count($args)) {
			foreach($args as $key => $arr) {
				if ($arr['type'] === 'int') {
					$stmt->bindValue($key, $arr['val'], PDO::PARAM_INT);
				} else {
					$stmt->bindValue($key, $arr['val'], PDO::PARAM_STR);
				}
			}
		}

		$stmt->execute();

		$array = array();
		while ($result = $stmt->fetch()) {
			$array[] = $result;
		}

		if (count($array)) {
			return $array;
		}
		return false;
	}

	function insert($values, $into) {
		$query = "INSERT {$into} (";

		$count = count($values);

		$i = 0;
		foreach ($values as $key => $value) {
			$i += 1;

			$query .= $value['col'];

			if ($i !== $count) {
				$query .= ",";
			}
		}

		$query .= ") VALUES (";

		$i = 0;
		foreach ($values as $key => $value) {
			$i += 1;

			$query .= $key;

			if ($i !== $count) {
				$query .= ",";
			}
		}

		$query .= ")";

		$stmt = $this->pdo->prepare($query);

		foreach($values as $key => $value) {
			if ($value['type'] === 'int') {
				$stmt->bindValue($key, $value['val'], PDO::PARAM_INT);
			} else {
				$stmt->bindValue($key, $value['val'], PDO::PARAM_STR);
			}
		}

		$stmt->execute();

		return $this->pdo->lastInsertId();
	}
}


?>