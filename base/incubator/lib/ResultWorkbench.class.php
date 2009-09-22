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

/**
 * Result workbench, used to process the sql results.
 * Portions of code (c) Dmitry Koterov
 * @ingroup PlainQuery
 */
abstract class ResultWorkbench
{
	/**
	 * Field alias.
	 * Marks a sql column that shoud be used as a key in hash transformed from the set of the
	 * resulting rows
	 *
	 * Example:
	 * <code>
	 *  $field = new SqlColumn::create("my_field")
	 * 		->setAlias(ResultWorkbench::ALIAS_ARRAY_KEY);
	 * </code>
	 */
	const ALIAS_ARRAY_KEY = 'generic_array_key';

	/**
	 * Field alias.
	 * Marks a sql column thas shoud be used as a relation in the hash-based forest transformed
	 * from the resulting array.
	 */
	const ALIAS_PARENT_KEY = 'generic_parent_key';

	/**
	 * @var DB
	 */
	private $db;

	final function __construct(DB $db)
	{
		$this->db = $db;
	}

	/**
	 * @return DB
	 */
	final function getDB()
	{
		return $this->db;
	}

	/**
	 * Result postprocessor, that transforms the results to hash.
	 */
	protected function prepareResult($rows)
	{
		// Process ARRAY_KEY feature.
        if (is_array($rows) && $rows)
        {
            // Find ARRAY_KEY* AND PARENT_KEY fields in field list.
            $pk = null;
            $ak = array();
            foreach (current($rows) as $fieldName => $dummy)
            {
                if (0 == strncasecmp($fieldName, self::ALIAS_ARRAY_KEY, strlen(self::ALIAS_ARRAY_KEY)))
                {
                    $ak[] = $fieldName;
                }
                else if (0 == strncasecmp($fieldName, self::ALIAS_PARENT_KEY, strlen(self::ALIAS_PARENT_KEY)))
                {
                    $pk = $fieldName;
                }
            }

            natsort($ak); // sort ARRAY_KEY* using natural comparision
            if ($ak)
            {
                // Tree-based array? Fields: ARRAY_KEY, PARENT_KEY
                if ($pk !== null)
                {
                    return $this->prepareResult2Forest($rows, $ak[0], $pk);
                }
                // Key-based array? Fields: ARRAY_KEY.
                return $this->prepareResult2Hash($rows, $ak);
            }
        }
        return $rows;
	}

	/**
	 * Transforms the resulting collection of rows into hash, using the key from a column marked
	 * by ARG_ARRAY_KEY placeholder.
	 */
	private function prepareResult2Hash(array $rows, $arrayKeys)
	{
        $arrayKeys = (array)$arrayKeys;
        $result = array();
        foreach ($rows as $row)
        {
            // Iterate over all of ARRAY_KEY* fields and build array dimensions.
            $current =& $result;
            foreach ($arrayKeys as $ak)
            {
                $key = $row[$ak];
                unset($row[$ak]); // remove ARRAY_KEY* field from result row
                if ($key !== null)
                {
                    $current =& $current[$key];
                }
                else
                {
                    // IF ARRAY_KEY field === null, use array auto-indices.
                    $tmp = array();
                    $current[] =& $tmp;
                    $current =& $tmp;
                    unset($tmp); // we use tmp, because don't know the value of auto-index
                }
            }
            $current = $row; // save the row in last dimension
        }

        return $result;
	}

	/**
	 * Transforms the resulting collection of rows into the hash that represents a forest, where
	 * ID's are values of column marked by ARG_ARRAY_KEY placeholder and PARENT_ID's are values of
	 * column marked by ARG_PARENT_KEY placeholder.
	 */
	private function prepareResult2Forest(array $rows, $idName, $pidName)
	{
        $children = array(); // children of each ID
        $ids = array();

        // Collect who are children of whom.
        foreach ($rows as $i => $r)
        {
            $row =& $rows[$i];
            $id = $row[$idName];
            if ($id === null)
            {
                // Rows without an ID are totally invalid and makes the result tree to
                // be empty (because PARENT_ID = null means "a root of the tree"). So
                // skip them totally.
                continue;
            }
            $pid = $row[$pidName];
            if ($id == $pid)
            {
            	$pid = null;
            }
            $children[$pid][$id] =& $row;
            if (!isset($children[$id]))
            {
            	$children[$id] = array();
            }
            $row['childNodes'] =& $children[$id];
            $ids[$id] = true;
        }
        // Root elements are elements with non-found PIDs.
        $forest = array();
        foreach ($rows as $i => $_)
        {
            $row =& $rows[$i];
            $id = $row[$idName];
            $pid = $row[$pidName];
            if ($pid == $id)
            {
            	$pid = null;
            }
            if (!isset($ids[$pid]))
            {
                $forest[$row[$idName]] =& $row;
            }
            unset($row[$idName]);
            unset($row[$pidName]);
        }
        return $forest;
	}
}

?>