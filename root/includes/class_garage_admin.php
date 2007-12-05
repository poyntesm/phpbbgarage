<?php
/***************************************************************************
 *                              class_garage_admin.php
 *                            -------------------
 *   begin                : Friday, 06 May 2005
 *   copyright            : (C) Esmond Poynton
 *   email                : esmond.poynton@gmail.com
 *   description          : Provides Vehicle Garage System For phpBB
 *
 *   $Id$
 *
 ***************************************************************************/

/***************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 ***************************************************************************/
if (!defined('IN_PHPBB'))
{
	die('Hacking attempt');
}

class garage_admin
{

	var $classname = "garage_admin";

	/*========================================================================*/
	// Inserts Category Into DB
	// Usage: insert_category(array());
	/*========================================================================*/
	function insert_category($data)
	{
		global $db;

		$sql = "INSERT INTO ". GARAGE_CATEGORIES_TABLE ." 
			(title, field_order)
			VALUES 
			('" . $data['title'] . "', " . $data['field_order'] . " )";
	
		if(!$result = $db->sql_query($sql))
		{
			message_die(GENERAL_ERROR, 'Could Not Insert New Category', '', __LINE__, __FILE__, $sql);
		}

		return;
	}

	/*========================================================================*/
	// Count The Modification Categories Within The Garage
	// Usage: count_categories();
	/*========================================================================*/
	function count_categories()
	{
		global $db;

	        // Get the total count of mods in the garage
		$sql = "SELECT count(*) AS total 
			FROM " . GARAGE_CATEGORIES_TABLE;

		if(!$result = $db->sql_query($sql))
		{
			message_die(GENERAL_ERROR, 'Error Counting Categories', '', __LINE__, __FILE__, $sql);
		}

        	$row = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		return $row['total'];
	}

	/*========================================================================*/
	// Inserts Category Into DB
	// Usage: insert_category();
	/*========================================================================*/
	function gzip_PrintFourChars($val)
	{
		for ($i = 0; $i < 4; $i ++)
		{
			$return .= chr($val % 256);
			$val = floor($val / 256);
		}
		return $return;
	} 

	/*========================================================================*/
	// Used for grabbing the sequences for postgres...
	// Usage: pg_get_sequences('line feed', 'backup type');
	/*========================================================================*/
	function pg_get_sequences($crlf, $backup_type)
	{
		global $db;
	
		$get_seq_sql = "SELECT relname FROM pg_class WHERE NOT relname ~ 'pg_.*'
			AND relkind = 'S' ORDER BY relname";
	
		$seq = $db->sql_query($get_seq_sql);
	
		if( !$num_seq = $db->sql_numrows($seq) )
		{
	
			$return_val = "# No Sequences Found $crlf";
	
		}
		else
		{
			$return_val = "# Sequences $crlf";
			$i_seq = 0;
	
			while($i_seq < $num_seq)
			{
				$row = $db->sql_fetchrow($seq);
				$sequence = $row['relname'];
	
				$get_props_sql = "SELECT * FROM $sequence";
				$seq_props = $db->sql_query($get_props_sql);
	
				if($db->sql_numrows($seq_props) > 0)
				{
					$row1 = $db->sql_fetchrow($seq_props);
	
					if($backup_type == 'structure')
					{
						$row['last_value'] = 1;
					}
	
					$return_val .= "CREATE SEQUENCE $sequence start " . $row['last_value'] . ' increment ' . $row['increment_by'] . ' maxvalue ' . $row['max_value'] . ' minvalue ' . $row['min_value'] . ' cache ' . $row['cache_value'] . "; $crlf";
	
				}  // End if numrows > 0
	
				if(($row['last_value'] > 1) && ($backup_type != 'structure'))
				{
					$return_val .= "SELECT NEXTVALE('$sequence'); $crlf";
					unset($row['last_value']);
				}
	
				$i_seq++;
	
			} // End while..
	
		} // End else...
	
		return $returnval;
	
	} // End function...
	
	/*========================================================================*/
	// Will Return The "CREATE TABLE syntax For Postgres
	// Usage: get_table_def_postgresql('table name', 'line feed');
	/*========================================================================*/
	function get_table_def_postgresql($table, $crlf)
	{
		global $drop, $db;
	
		$schema_create = "";
		//
		// Get a listing of the fields, with their associated types, etc.
		//
	
		$field_query = "SELECT a.attnum, a.attname AS field, t.typname as type, a.attlen AS length, a.atttypmod as lengthvar, a.attnotnull as notnull
			FROM pg_class c, pg_attribute a, pg_type t
			WHERE c.relname = '$table'
				AND a.attnum > 0
				AND a.attrelid = c.oid
				AND a.atttypid = t.oid
			ORDER BY a.attnum";
		$result = $db->sql_query($field_query);
	
		if(!$result)
		{
			message_die(GENERAL_ERROR, "Failed in get_table_def (show fields)", "", __LINE__, __FILE__, $field_query);
		} // end if..
	
		if ($drop == 1)
		{
			$schema_create .= "DROP TABLE $table;$crlf";
		} // end if
	
		// Ok now we actually start building the SQL statements to restore the tables
		$schema_create .= "CREATE TABLE $table($crlf";
	
		while ($row = $db->sql_fetchrow($result))
		{
			// Get the data from the table
			$sql_get_default = "SELECT d.adsrc AS rowdefault
				FROM pg_attrdef d, pg_class c
				WHERE (c.relname = '$table')
					AND (c.oid = d.adrelid)
					AND d.adnum = " . $row['attnum'];
			$def_res = $db->sql_query($sql_get_default);
	
			if (!$def_res)
			{
				unset($row['rowdefault']);
			}
			else
			{
				$row['rowdefault'] = @pg_result($def_res, 0, 'rowdefault');
			}
	
			if ($row['type'] == 'bpchar')
			{
				// Internally stored as bpchar, but isn't accepted in a CREATE TABLE statement.
				$row['type'] = 'char';
			}
	
			$schema_create .= '	' . $row['field'] . ' ' . $row['type'];
	
			if (eregi('char', $row['type']))
			{
				if ($row['lengthvar'] > 0)
				{
					$schema_create .= '(' . ($row['lengthvar'] -4) . ')';
				}
			}
	
			if (eregi('numeric', $row['type']))
			{
				$schema_create .= '(';
				$schema_create .= sprintf("%s,%s", (($row['lengthvar'] >> 16) & 0xffff), (($row['lengthvar'] - 4) & 0xffff));
				$schema_create .= ')';
			}
	
			if (!empty($row['rowdefault']))
			{
				$schema_create .= ' DEFAULT ' . $row['rowdefault'];
			}
	
			if ($row['notnull'] == 't')
			{
				$schema_create .= ' NOT NULL';
			}
	
			$schema_create .= ",$crlf";
	
		}

		// Get the listing of primary keys.
		$sql_pri_keys = "SELECT ic.relname AS index_name, bc.relname AS tab_name, ta.attname AS column_name, i.indisunique AS unique_key, i.indisprimary AS primary_key
			FROM pg_class bc, pg_class ic, pg_index i, pg_attribute ta, pg_attribute ia
			WHERE (bc.oid = i.indrelid)
				AND (ic.oid = i.indexrelid)
				AND (ia.attrelid = i.indexrelid)
				AND	(ta.attrelid = bc.oid)
				AND (bc.relname = '$table')
				AND (ta.attrelid = i.indrelid)
				AND (ta.attnum = i.indkey[ia.attnum-1])
			ORDER BY index_name, tab_name, column_name ";
		$result = $db->sql_query($sql_pri_keys);
	
		if(!$result)
		{
			message_die(GENERAL_ERROR, "Failed in get_table_def (show fields)", "", __LINE__, __FILE__, $sql_pri_keys);
		}
	
		while ( $row = $db->sql_fetchrow($result))
		{
			if ($row['primary_key'] == 't')
			{
				if (!empty($primary_key))
				{
					$primary_key .= ', ';
				}
	
				$primary_key .= $row['column_name'];
				$primary_key_name = $row['index_name'];
	
			}
			else
			{
				// We have to store this all this info because it is possible to have a multi-column key...
				// we can loop through it again and build the statement
				$index_rows[$row['index_name']]['table'] = $table;
				$index_rows[$row['index_name']]['unique'] = ($row['unique_key'] == 't') ? ' UNIQUE ' : '';
				$index_rows[$row['index_name']]['column_names'] .= $row['column_name'] . ', ';
			}
		}
	
		if (!empty($index_rows))
		{
			while(list($idx_name, $props) = each($index_rows))
			{
				$props['column_names'] = ereg_replace(", $", "" , $props['column_names']);
				$index_create .= 'CREATE ' . $props['unique'] . " INDEX $idx_name ON $table (" . $props['column_names'] . ");$crlf";
			}
		}
	
		if (!empty($primary_key))
		{
			$schema_create .= "	CONSTRAINT $primary_key_name PRIMARY KEY ($primary_key),$crlf";
		}
	
		// Generate constraint clauses for CHECK constraints
		$sql_checks = "SELECT rcname as index_name, rcsrc
			FROM pg_relcheck, pg_class bc
			WHERE rcrelid = bc.oid
				AND bc.relname = '$table'
				AND NOT EXISTS (
					SELECT *
						FROM pg_relcheck as c, pg_inherits as i
						WHERE i.inhrelid = pg_relcheck.rcrelid
							AND c.rcname = pg_relcheck.rcname
							AND c.rcsrc = pg_relcheck.rcsrc
							AND c.rcrelid = i.inhparent
				)";
		$result = $db->sql_query($sql_checks);
	
		if (!$result)
		{
			message_die(GENERAL_ERROR, "Failed in get_table_def (show fields)", "", __LINE__, __FILE__, $sql_checks);
		}
	
		// Add the constraints to the sql file.
		while ($row = $db->sql_fetchrow($result))
		{
			$schema_create .= '	CONSTRAINT ' . $row['index_name'] . ' CHECK ' . $row['rcsrc'] . ",$crlf";
		}
	
		$schema_create = ereg_replace(',' . $crlf . '$', '', $schema_create);
		$index_create = ereg_replace(',' . $crlf . '$', '', $index_create);
	
		$schema_create .= "$crlf);$crlf";
	
		if (!empty($index_create))
		{
			$schema_create .= $index_create;
		}
	
		// Ok now we've built all the sql return it to the calling function.
		return (stripslashes($schema_create));
	
	}
	
	/*========================================================================*/
	// Create the 'CREATE TABLE' statements For MySQL
	// Usage: get_table_def_mysql('table name', 'line feed');
	/*========================================================================*/
	function get_table_def_mysql($table, $crlf)
	{
		global $drop, $db;
	
		$schema_create = "";
		$field_query = "SHOW FIELDS FROM $table";
		$key_query = "SHOW KEYS FROM $table";
	
		// If the user has selected to drop existing tables when doing a restore.
		// Then we add the statement to drop the tables....
		if ($drop == 1)
		{
			$schema_create .= "DROP TABLE IF EXISTS $table;$crlf";
		}
	
		$schema_create .= "CREATE TABLE $table($crlf";
	
		// Ok lets grab the fields...
		$result = $db->sql_query($field_query);
		if(!$result)
		{
			message_die(GENERAL_ERROR, "Failed in get_table_def (show fields)", "", __LINE__, __FILE__, $field_query);
		}
	
		while ($row = $db->sql_fetchrow($result))
		{
			$schema_create .= '	' . $row['Field'] . ' ' . $row['Type'];
	
			if(!empty($row['Default']))
			{
				$schema_create .= ' DEFAULT \'' . $row['Default'] . '\'';
			}
	
			if($row['Null'] != "YES")
			{
				$schema_create .= ' NOT NULL';
			}
	
			if($row['Extra'] != "")
			{
				$schema_create .= ' ' . $row['Extra'];
			}
	
			$schema_create .= ",$crlf";
		}
		// Drop the last ',$crlf' off ;)
		$schema_create = ereg_replace(',' . $crlf . '$', "", $schema_create);
	
		// Get any Indexed fields from the database...
		$result = $db->sql_query($key_query);
		if(!$result)
		{
			message_die(GENERAL_ERROR, "FAILED IN get_table_def (show keys)", "", __LINE__, __FILE__, $key_query);
		}
	
		while($row = $db->sql_fetchrow($result))
		{
			$kname = $row['Key_name'];
	
			if(($kname != 'PRIMARY') && ($row['Non_unique'] == 0))
			{
				$kname = "UNIQUE|$kname";
			}
	
			if(!is_array($index[$kname]))
			{
				$index[$kname] = array();
			}
	
			$index[$kname][] = $row['Column_name'];
		}
	
		while(list($x, $columns) = @each($index))
		{
			$schema_create .= ", $crlf";
	
			if($x == 'PRIMARY')
			{
				$schema_create .= '	PRIMARY KEY (' . implode($columns, ', ') . ')';
			}
			elseif (substr($x,0,6) == 'UNIQUE')
			{
				$schema_create .= '	UNIQUE ' . substr($x,7) . ' (' . implode($columns, ', ') . ')';
			}
			else
			{
				$schema_create .= "	KEY $x (" . implode($columns, ', ') . ')';
			}
		}
	
		$schema_create .= "$crlf);";
	
		if(get_magic_quotes_runtime())
		{
			return(stripslashes($schema_create));
		}
		else
		{
			return($schema_create);
		}
	
	} // End get_table_def_mysql
	
	/*========================================================================*/
	// Create the 'INSERT' statements For Postgre
	// Usage: get_table_content_postgresql('table name', 'handler');
	/*========================================================================*/
	function get_table_content_postgresql($table, $handler)
	{
		global $db, $garage_admin;
	
		// Grab all of the data from current table.
		$result = $db->sql_query("SELECT * FROM $table");
	
		if (!$result)
		{
			message_die(GENERAL_ERROR, "Failed in get_table_content (select *)", "", __LINE__, __FILE__, "SELECT * FROM $table");
		}
	
		$i_num_fields = $db->sql_numfields($result);
	
		for ($i = 0; $i < $i_num_fields; $i++)
		{
			$aryType[] = $db->sql_fieldtype($i, $result);
			$aryName[] = $db->sql_fieldname($i, $result);
		}
	
		$iRec = 0;
	
		while($row = $db->sql_fetchrow($result))
		{
			$schema_vals = '';
			$schema_fields = '';
			$schema_insert = '';
			//
			// Build the SQL statement to recreate the data.
			//
			for($i = 0; $i < $i_num_fields; $i++)
			{
				$strVal = $row[$aryName[$i]];
				if (eregi("char|text|bool", $aryType[$i]))
				{
					$strQuote = "'";
					$strEmpty = "";
					$strVal = addslashes($strVal);
				}
				elseif (eregi("date|timestamp", $aryType[$i]))
				{
					if (empty($strVal))
					{
						$strQuote = "";
					}
					else
					{
						$strQuote = "'";
					}
				}
				else
				{
					$strQuote = "";
					$strEmpty = "NULL";
				}
	
				if (empty($strVal) && $strVal != "0")
				{
					$strVal = $strEmpty;
				}
	
				$schema_vals .= " $strQuote$strVal$strQuote,";
				$schema_fields .= " $aryName[$i],";
	
			}
	
			$schema_vals = ereg_replace(",$", "", $schema_vals);
			$schema_vals = ereg_replace("^ ", "", $schema_vals);
			$schema_fields = ereg_replace(",$", "", $schema_fields);
			$schema_fields = ereg_replace("^ ", "", $schema_fields);
	
			//
			// Take the ordered fields and their associated data and build it
			// into a valid sql statement to recreate that field in the data.
			//
			$schema_insert = "INSERT INTO $table ($schema_fields) VALUES($schema_vals);";
	
			$garage_admin->$handler(trim($schema_insert));
		}
	
		return(true);
	
	}// end function get_table_content_postgres...
	
	/*========================================================================*/
	// Create the 'INSERT' statements For MySQL
	// Usage: get_table_content_mysql('table name', 'handler');
	/*========================================================================*/
	function get_table_content_mysql($table, $handler)
	{
		global $db, $garage_admin;
	
		// Grab the data from the table.
		if (!($result = $db->sql_query("SELECT * FROM $table")))
		{
			message_die(GENERAL_ERROR, "Failed in get_table_content (select *)", "", __LINE__, __FILE__, "SELECT * FROM $table");
		}
	
		// Loop through the resulting rows and build the sql statement.
		if ($row = $db->sql_fetchrow($result))
		{
			$garage_admin->$handler("\n#\n# Table Data for $table\n#\n");
			$field_names = array();
	
			// Grab the list of field names.
			$num_fields = $db->sql_numfields($result);
			$table_list = '(';
			for ($j = 0; $j < $num_fields; $j++)
			{
				$field_names[$j] = $db->sql_fieldname($j, $result);
				$table_list .= (($j > 0) ? ', ' : '') . $field_names[$j];
				
			}
			$table_list .= ')';
	
			do
			{
				// Start building the SQL statement.
				$schema_insert = "INSERT INTO $table $table_list VALUES(";
	
				// Loop through the rows and fill in data for each column
				for ($j = 0; $j < $num_fields; $j++)
				{
					$schema_insert .= ($j > 0) ? ', ' : '';
	
					if(!isset($row[$field_names[$j]]))
					{
						// If there is no data for the column set it to null.
						// There was a problem here with an extra space causing the
						// sql file not to reimport if the last column was null in
						// any table.  Should be fixed now :) JLH
						$schema_insert .= 'NULL';
					}
					elseif ($row[$field_names[$j]] != '')
					{
						$schema_insert .= '\'' . addslashes($row[$field_names[$j]]) . '\'';
					}
					else
					{
						$schema_insert .= '\'\'';
					}
				}
	
				$schema_insert .= ');';
	
				// Go ahead and send the insert statement to the handler function.
				$garage_admin->$handler(trim($schema_insert));
	
			}
			while ($row = $db->sql_fetchrow($result));
		}
	
		return(true);
	}

	/*========================================================================*/
	// Output All The Content
	// Usage: output_table_content('content');
	/*========================================================================*/
	function output_table_content($content)
	{
		global $tempfile;
	
		//fwrite($tempfile, $content . "\n");
		//$backup_sql .= $content . "\n";
		echo $content ."\n";
		return;
	}
}

$garage_admin = new garage_admin();

?>
