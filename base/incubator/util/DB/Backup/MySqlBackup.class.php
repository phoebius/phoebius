<?php
/* ***********************************************************************************************
 *
 * Phoebius Framework
 *
 * **********************************************************************************************
 *
 * Copyright notice
 *
 ************************************************************************************************/

class MySqlBackup extends DBBackup
{
	/**
	 * @var IWriter
	 */
	private $stream;
	private $tables;

	/**
	 * @var DB
	 */
	private $db;

	/**
	 * @see DBBackup::make()
	 */
	function make($storeStructure, $storeData)
	{
		$this->stream = new FileWriter($this->getTarget());
		$this->db = DB::create($this->getDBConnector());
		if (!$this->getDB()->isConnected())
		{
			$this->getDB()->connect();
		}

		$this->begin();

		if ($storeStructure)
		{
			$this->storeStructure();
		}

		if ($storeData)
		{
			$this->storeData();
		}

		$this->end();
	}

	/**
	 * @return DB
	 */
	private function getDB()
	{
		return $this->db;
	}

	private function storeStructure()
	{
		$this->addMultiLineComment('Storing the structure');
		$this->storeTableSchemas();
		$this->addLine();
	}

	private function storeTableSchemas()
	{
		$db = $this->getDB();
		$dialect = $db->getDialect();

		foreach ((array)$this->getTables() as $table)
		{
			$tableNameEscaped = $dialect->quoteIdentifier($table);

			$this->addComment("Storing table $table");
			$this->addQuery("DROP TABLE IF EXISTS {$tableNameEscaped}");

			$createTableSchema = $db->rawGetRow("SHOW CREATE TABLE {$tableNameEscaped}");
			$createTableQuery = end($createTableSchema);
			$createTableQuery = preg_replace(
				'/(default CURRENTIME_FORMATSTAMP on update CURRENTIME_FORMATSTAMP|DEFAULT CHARSET=\w+)/i',
				'/*!40101 \\1 */',
				$createTableQuery);
			$this->addQuery($createTableQuery);
		}
	}

	private function getTables()
	{
		if (!$this->tables)
		{
			$this->tables = $this->getDB()->rawGetColumn('SHOW TABLES');
		}

		return $this->tables;
	}

	private function storeData()
	{
		$this->addMultiLineComment('Storing the contents');
		$this->storeTableData();
		$this->addLine();
	}

	private function quoteRelationIdentifier($id)
	{
		if (is_array($id))
		{
			$cols = array();
			foreach($id as $_)
			{
				$cols[] = $this->quoteRelationIdentifier($_);
			}

			return $cols;
		}

		return $this->getDB()->getDialect()->quoteIdentifier($id);
	}

	private function quoteValues(array $values)
	{
		$quoted = array();
		$dialect = $this->getDB()->getDialect();
		foreach($values as $_)
		{
			$quoted = $dialect->quoteValue($_);
		}

		return $quoted;
	}

	private function storeTableData()
	{
		$db = $this->getDB();
		$dialect = $db->getDialect();

		foreach ((array)$this->getTables() as $table)
		{
			$tableNameEscaped = $dialect->quoteIdentifier($table);
			$resultId = $db->rawQuery("SELECT * FROM {$tableNameEscaped}");
			while( $row = mysql_fetch_assoc($resultId) )
			{
				$query = array();
				$query[] = "INSERT INTO {$tableNameEscaped} (";
				$query[] = join(', ', $this->quoteRelationIdentifier(array_keys($row)));
				$query[] = ') VALUES (';
				$query[] = join(', ', $this->quoteValues($row));
				$query[] = ")";

				$this->addQuery(join("", $query));
				unset($query);
			}

			mysql_free_result($resultId);
		}
	}

	/**
	 * @return MySqlBackup
	 */
	private function begin()
	{
		$date = date('d.m.Y H:i:s');
		$about = <<<EOT
 === Simple MySql backup ===
 Created on $date
 Database: {$this->getDB()->getDriver()->getName()}.{$this->getDB()->getDbName()}
 ===========================
EOT;
		$this->addMultiLineComment($about);
		$this->addQuery('SET foreign_key_checks = 0');
		$this->addLine();

		return $this;
	}

	/**
	 * @return MySqlBackup
	 */
	private function end()
	{
		$this->addQuery('SET foreign_key_checks = 1');

		return $this;
	}

	/**
	 * @return MySqlBackup
	 */
	private function addQuery($string)
	{
		$this
			->addLine()
			->add($string)
			->add(';')
			->addLine();

		return $this;
	}

	/**
	 * @return MySqlBackup
	 */
	private function addComment($string)
	{
		Assert::isFalse(strpos("\r"));
		Assert::isFalse(strpos("\n"));

		$this
			->add('-- ')->addLine()
			->add('-- ')->add($string)->addLine()
			->add('-- ')->addLine();

		return $this;
	}

	/**
	 * @return MySqlBackup
	 */
	private function addMultiLineComment($string)
	{
		$this
			->add('/*')->addLine()
			->add($string)->addLine()
			->add('*/')->addLine();

		return $this;
	}

	/**
	 * @return MySqlBackup
	 */
	private function addLine()
	{
		$this->add("\r\n");

		return $this;
	}

	/**
	 * @return MySqlBackup
	 */
	private function add($string)
	{
		$this->stream->write($string);

		return $this;
	}
}

?>