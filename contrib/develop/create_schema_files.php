<?php
/** 
*
* @package phpBBGarage
* @version Id: create_schema_files.php,v 1.59 2007/07/31 20:27:39 davidmj Exp
* @version $Id: create_schema_files.php,v 1.59 2007/07/31 20:27:39 davidmj Exp $
* @copyright (c) 2006 phpBB Group 
* @copyright (c) 2007 Esmond Poynton
* @license http://opensource.org/licenses/gpl-license.php GNU Public License 
*
* This file creates new schema files for every database.
*
*/

@set_time_limit(0);

$schema_path = './';

if (!is_writable($schema_path))
{
	die('Schema path not writable');
}

$schema_data = get_schema_struct();
$dbms_type_map = array(
	'mysql_41'	=> array(
		'INT:'		=> 'int(%d)',
		'BINT'		=> 'bigint(20)',
		'UINT'		=> 'mediumint(8) UNSIGNED',
		'UINT:'		=> 'int(%d) UNSIGNED',
		'TINT:'		=> 'tinyint(%d)',
		'USINT'		=> 'smallint(4) UNSIGNED',
		'BOOL'		=> 'tinyint(1) UNSIGNED',
		'VCHAR'		=> 'varchar(255)',
		'VCHAR:'	=> 'varchar(%d)',
		'CHAR:'		=> 'char(%d)',
		'XSTEXT'	=> 'text',
		'XSTEXT_UNI'=> 'varchar(100)',
		'STEXT'		=> 'text',
		'STEXT_UNI'	=> 'varchar(255)',
		'TEXT'		=> 'text',
		'TEXT_UNI'	=> 'text',
		'MTEXT'		=> 'mediumtext',
		'MTEXT_UNI'	=> 'mediumtext',
		'TIMESTAMP'	=> 'int(11) UNSIGNED',
		'DECIMAL'	=> 'decimal(5,2)',
		'DECIMAL:'	=> 'decimal(%d,2)',
		'PDECIMAL'	=> 'decimal(6,3)',
		'PDECIMAL:'	=> 'decimal(%d,3)',
		'VCHAR_UNI'	=> 'varchar(255)',
		'VCHAR_UNI:'=> 'varchar(%d)',
		'VCHAR_CI'	=> 'varchar(255)',
		'VARBINARY'	=> 'varbinary(255)',
	),

	'mysql_40'	=> array(
		'INT:'		=> 'int(%d)',
		'BINT'		=> 'bigint(20)',
		'UINT'		=> 'mediumint(8) UNSIGNED',
		'UINT:'		=> 'int(%d) UNSIGNED',
		'TINT:'		=> 'tinyint(%d)',
		'USINT'		=> 'smallint(4) UNSIGNED',
		'BOOL'		=> 'tinyint(1) UNSIGNED',
		'VCHAR'		=> 'varbinary(255)',
		'VCHAR:'	=> 'varbinary(%d)',
		'CHAR:'		=> 'binary(%d)',
		'XSTEXT'	=> 'blob',
		'XSTEXT_UNI'=> 'blob',
		'STEXT'		=> 'blob',
		'STEXT_UNI'	=> 'blob',
		'TEXT'		=> 'blob',
		'TEXT_UNI'	=> 'blob',
		'MTEXT'		=> 'mediumblob',
		'MTEXT_UNI'	=> 'mediumblob',
		'TIMESTAMP'	=> 'int(11) UNSIGNED',
		'DECIMAL'	=> 'decimal(5,2)',
		'DECIMAL:'	=> 'decimal(%d,2)',
		'PDECIMAL'	=> 'decimal(6,3)',
		'PDECIMAL:'	=> 'decimal(%d,3)',
		'VCHAR_UNI'	=> 'blob',
		'VCHAR_UNI:'=> array('varbinary(%d)', 'limit' => array('mult', 3, 255, 'blob')),
		'VCHAR_CI'	=> 'blob',
		'VARBINARY'	=> 'varbinary(255)',
	),

	'firebird'	=> array(
		'INT:'		=> 'INTEGER',
		'BINT'		=> 'DOUBLE PRECISION',
		'UINT'		=> 'INTEGER',
		'UINT:'		=> 'INTEGER',
		'TINT:'		=> 'INTEGER',
		'USINT'		=> 'INTEGER',
		'BOOL'		=> 'INTEGER',
		'VCHAR'		=> 'VARCHAR(255) CHARACTER SET NONE',
		'VCHAR:'	=> 'VARCHAR(%d) CHARACTER SET NONE',
		'CHAR:'		=> 'CHAR(%d) CHARACTER SET NONE',
		'XSTEXT'	=> 'BLOB SUB_TYPE TEXT CHARACTER SET NONE',
		'STEXT'		=> 'BLOB SUB_TYPE TEXT CHARACTER SET NONE',
		'TEXT'		=> 'BLOB SUB_TYPE TEXT CHARACTER SET NONE',
		'MTEXT'		=> 'BLOB SUB_TYPE TEXT CHARACTER SET NONE',
		'XSTEXT_UNI'=> 'VARCHAR(100) CHARACTER SET UTF8',
		'STEXT_UNI'	=> 'VARCHAR(255) CHARACTER SET UTF8',
		'TEXT_UNI'	=> 'BLOB SUB_TYPE TEXT CHARACTER SET UTF8',
		'MTEXT_UNI'	=> 'BLOB SUB_TYPE TEXT CHARACTER SET UTF8',
		'TIMESTAMP'	=> 'INTEGER',
		'DECIMAL'	=> 'DOUBLE PRECISION',
		'DECIMAL:'	=> 'DOUBLE PRECISION',
		'PDECIMAL'	=> 'DOUBLE PRECISION',
		'PDECIMAL:'	=> 'DOUBLE PRECISION',
		'VCHAR_UNI'	=> 'VARCHAR(255) CHARACTER SET UTF8',
		'VCHAR_UNI:'=> 'VARCHAR(%d) CHARACTER SET UTF8',
		'VCHAR_CI'	=> 'VARCHAR(255) CHARACTER SET UTF8',
		'VARBINARY'	=> 'CHAR(255) CHARACTER SET NONE',
	),

	'mssql'		=> array(
		'INT:'		=> '[int]',
		'BINT'		=> '[float]',
		'UINT'		=> '[int]',
		'UINT:'		=> '[int]',
		'TINT:'		=> '[int]',
		'USINT'		=> '[int]',
		'BOOL'		=> '[int]',
		'VCHAR'		=> '[varchar] (255)',
		'VCHAR:'	=> '[varchar] (%d)',
		'CHAR:'		=> '[char] (%d)',
		'XSTEXT'	=> '[varchar] (1000)',
		'STEXT'		=> '[varchar] (3000)',
		'TEXT'		=> '[varchar] (8000)',
		'MTEXT'		=> '[text]',
		'XSTEXT_UNI'=> '[varchar] (100)',
		'STEXT_UNI'	=> '[varchar] (255)',
		'TEXT_UNI'	=> '[varchar] (4000)',
		'MTEXT_UNI'	=> '[text]',
		'TIMESTAMP'	=> '[int]',
		'DECIMAL'	=> '[float]',
		'DECIMAL:'	=> '[float]',
		'PDECIMAL'	=> '[float]',
		'PDECIMAL:'	=> '[float]',
		'VCHAR_UNI'	=> '[varchar] (255)',
		'VCHAR_UNI:'=> '[varchar] (%d)',
		'VCHAR_CI'	=> '[varchar] (255)',
		'VARBINARY'	=> '[varchar] (255)',
	),

	'oracle'	=> array(
		'INT:'		=> 'number(%d)',
		'BINT'		=> 'number(20)',
		'UINT'		=> 'number(8)',
		'UINT:'		=> 'number(%d)',
		'TINT:'		=> 'number(%d)',
		'USINT'		=> 'number(4)',
		'BOOL'		=> 'number(1)',
		'VCHAR'		=> 'varchar2(255)',
		'VCHAR:'	=> 'varchar2(%d)',
		'CHAR:'		=> 'char(%d)',
		'XSTEXT'	=> 'varchar2(1000)',
		'STEXT'		=> 'varchar2(3000)',
		'TEXT'		=> 'clob',
		'MTEXT'		=> 'clob',
		'XSTEXT_UNI'=> 'varchar2(300)',
		'STEXT_UNI'	=> 'varchar2(765)',
		'TEXT_UNI'	=> 'clob',
		'MTEXT_UNI'	=> 'clob',
		'TIMESTAMP'	=> 'number(11)',
		'DECIMAL'	=> 'number(5, 2)',
		'DECIMAL:'	=> 'number(%d, 2)',
		'PDECIMAL'	=> 'number(6, 3)',
		'PDECIMAL:'	=> 'number(%d, 3)',
		'VCHAR_UNI'	=> 'varchar2(765)',
		'VCHAR_UNI:'=> array('varchar2(%d)', 'limit' => array('mult', 3, 765, 'clob')),
		'VCHAR_CI'	=> 'varchar2(255)',
		'VARBINARY'	=> 'raw(255)',
	),

	'sqlite'	=> array(
		'INT:'		=> 'int(%d)',
		'BINT'		=> 'bigint(20)',
		'UINT'		=> 'INTEGER UNSIGNED', //'mediumint(8) UNSIGNED',
		'UINT:'		=> 'INTEGER UNSIGNED', // 'int(%d) UNSIGNED',
		'TINT:'		=> 'tinyint(%d)',
		'USINT'		=> 'INTEGER UNSIGNED', //'mediumint(4) UNSIGNED',
		'BOOL'		=> 'INTEGER UNSIGNED', //'tinyint(1) UNSIGNED',
		'VCHAR'		=> 'varchar(255)',
		'VCHAR:'	=> 'varchar(%d)',
		'CHAR:'		=> 'char(%d)',
		'XSTEXT'	=> 'text(65535)',
		'STEXT'		=> 'text(65535)',
		'TEXT'		=> 'text(65535)',
		'MTEXT'		=> 'mediumtext(16777215)',
		'XSTEXT_UNI'=> 'text(65535)',
		'STEXT_UNI'	=> 'text(65535)',
		'TEXT_UNI'	=> 'text(65535)',
		'MTEXT_UNI'	=> 'mediumtext(16777215)',
		'TIMESTAMP'	=> 'INTEGER UNSIGNED', //'int(11) UNSIGNED',
		'DECIMAL'	=> 'decimal(5,2)',
		'DECIMAL:'	=> 'decimal(%d,2)',
		'PDECIMAL'	=> 'decimal(6,3)',
		'PDECIMAL:'	=> 'decimal(%d,3)',
		'VCHAR_UNI'	=> 'varchar(255)',
		'VCHAR_UNI:'=> 'varchar(%d)',
		'VCHAR_CI'	=> 'varchar(255)',
		'VARBINARY'	=> 'blob',
	),

	'postgres'	=> array(
		'INT:'		=> 'INT4',
		'BINT'		=> 'INT8',
		'UINT'		=> 'INT4', // unsigned
		'UINT:'		=> 'INT4', // unsigned
		'USINT'		=> 'INT2', // unsigned
		'BOOL'		=> 'INT2', // unsigned
		'TINT:'		=> 'INT2',
		'VCHAR'		=> 'varchar(255)',
		'VCHAR:'	=> 'varchar(%d)',
		'CHAR:'		=> 'char(%d)',
		'XSTEXT'	=> 'varchar(1000)',
		'STEXT'		=> 'varchar(3000)',
		'TEXT'		=> 'varchar(8000)',
		'MTEXT'		=> 'TEXT',
		'XSTEXT_UNI'=> 'varchar(100)',
		'STEXT_UNI'	=> 'varchar(255)',
		'TEXT_UNI'	=> 'varchar(4000)',
		'MTEXT_UNI'	=> 'TEXT',
		'TIMESTAMP'	=> 'INT4', // unsigned
		'DECIMAL'	=> 'decimal(5,2)',
		'DECIMAL:'	=> 'decimal(%d,2)',
		'PDECIMAL'	=> 'decimal(6,3)',
		'PDECIMAL:'	=> 'decimal(%d,3)',
		'VCHAR_UNI'	=> 'varchar(255)',
		'VCHAR_UNI:'=> 'varchar(%d)',
		'VCHAR_CI'	=> 'varchar_ci',
		'VARBINARY'	=> 'bytea',
	),
);

// A list of types being unsigned for better reference in some db's
$unsigned_types = array('UINT', 'UINT:', 'USINT', 'BOOL', 'TIMESTAMP');
$supported_dbms = array('firebird', 'mssql', 'mysql_40', 'mysql_41', 'oracle', 'postgres', 'sqlite');

foreach ($supported_dbms as $dbms)
{
	$fp = fopen($schema_path . $dbms . '_schema.sql', 'wt');

	$line = '';

	// Write Header
	switch ($dbms)
	{
		case 'mysql_40':
		case 'mysql_41':
			$line = "#\n# \$I" . "d: $\n#\n\n";
		break;

		case 'firebird':
			$line = "#\n# \$I" . "d: $\n#\n\n";
			$line .= custom_data('firebird') . "\n";
		break;

		case 'sqlite':
			$line = "#\n# \$I" . "d: $\n#\n\n";
			$line .= "BEGIN TRANSACTION;\n\n";
		break;

		case 'mssql':
			$line = "/*\n\n \$I" . "d: $\n\n*/\n\n";
			$line .= "BEGIN TRANSACTION\nGO\n\n";
		break;

		case 'oracle':
			$line = "/*\n\n \$I" . "d: $\n\n*/\n\n";
			$line .= custom_data('oracle') . "\n";
		break;

		case 'postgres':
			$line = "/*\n\n \$I" . "d: $\n\n*/\n\n";
			$line .= "BEGIN;\n\n";
			$line .= custom_data('postgres') . "\n";
		break;
	}

	fwrite($fp, $line);

	foreach ($schema_data as $table_name => $table_data)
	{
		// Write comment about table
		switch ($dbms)
		{
			case 'mysql_40':
			case 'mysql_41':
			case 'firebird':
			case 'sqlite':
				fwrite($fp, "# Table: '{$table_name}'\n");
			break;

			case 'mssql':
			case 'oracle':
			case 'postgres':
				fwrite($fp, "/*\n\tTable: '{$table_name}'\n*/\n");
			break;
		}

		// Create Table statement
		$generator = $textimage = false;
		$line = '';

		switch ($dbms)
		{
			case 'mysql_40':
			case 'mysql_41':
			case 'firebird':
			case 'oracle':
			case 'sqlite':
			case 'postgres':
				$line = "CREATE TABLE {$table_name} (\n";
			break;

			case 'mssql':
				$line = "CREATE TABLE [{$table_name}] (\n";
			break;
		}

		// Table specific so we don't get overlap
		$modded_array = array(); 

		// Write columns one by one...
		foreach ($table_data['COLUMNS'] as $column_name => $column_data)
		{
			// Get type
			if (strpos($column_data[0], ':') !== false)
			{
				list($orig_column_type, $column_length) = explode(':', $column_data[0]);
				if (!is_array($dbms_type_map[$dbms][$orig_column_type . ':']))
				{
					$column_type = sprintf($dbms_type_map[$dbms][$orig_column_type . ':'], $column_length);
				}
				else
				{
					if (isset($dbms_type_map[$dbms][$orig_column_type . ':']['rule']))
					{
						switch ($dbms_type_map[$dbms][$orig_column_type . ':']['rule'][0])
						{
							case 'div':
								$column_length /= $dbms_type_map[$dbms][$orig_column_type . ':']['rule'][1];
								$column_length = ceil($column_length);
								$column_type = sprintf($dbms_type_map[$dbms][$orig_column_type . ':'][0], $column_length);
							break;
						}
					}

					if (isset($dbms_type_map[$dbms][$orig_column_type . ':']['limit']))
					{
						switch ($dbms_type_map[$dbms][$orig_column_type . ':']['limit'][0])
						{
							case 'mult':
								$column_length *= $dbms_type_map[$dbms][$orig_column_type . ':']['limit'][1];
								if ($column_length > $dbms_type_map[$dbms][$orig_column_type . ':']['limit'][2])
								{
									$column_type = $dbms_type_map[$dbms][$orig_column_type . ':']['limit'][3];
									$modded_array[$column_name] = $column_type;
								}
								else
								{
									$column_type = sprintf($dbms_type_map[$dbms][$orig_column_type . ':'][0], $column_length);
								}
							break;
						}
					}
				}
				$orig_column_type .= ':';
			}
			else
			{
				$orig_column_type = $column_data[0];
				$column_type = $dbms_type_map[$dbms][$column_data[0]];
				if ($column_type == 'text' || $column_type == 'blob')
				{
					$modded_array[$column_name] = $column_type;
				}
			}

			// Adjust default value if db-dependant specified
			if (is_array($column_data[1]))
			{
				$column_data[1] = (isset($column_data[1][$dbms])) ? $column_data[1][$dbms] : $column_data[1]['default'];
			}

			switch ($dbms)
			{
				case 'mysql_40':
				case 'mysql_41':
					$line .= "\t{$column_name} {$column_type} ";

					// For hexadecimal values do not use single quotes
					if (!is_null($column_data[1]) && substr($column_type, -4) !== 'text' && substr($column_type, -4) !== 'blob')
					{
						$line .= (strpos($column_data[1], '0x') === 0) ? "DEFAULT {$column_data[1]} " : "DEFAULT '{$column_data[1]}' ";
					}
					$line .= 'NOT NULL';

					if (isset($column_data[2]))
					{
						if ($column_data[2] == 'auto_increment')
						{
							$line .= ' auto_increment';
						}
						else if ($dbms === 'mysql_41' && $column_data[2] == 'true_sort')
						{
							$line .= ' COLLATE utf8_unicode_ci';
						}
					}

					$line .= ",\n";
				break;

				case 'sqlite':
					if (isset($column_data[2]) && $column_data[2] == 'auto_increment')
					{
						$line .= "\t{$column_name} INTEGER PRIMARY KEY ";
						$generator = $column_name;
					}
					else
					{
						$line .= "\t{$column_name} {$column_type} ";
					}

					$line .= 'NOT NULL ';
					$line .= (!is_null($column_data[1])) ? "DEFAULT '{$column_data[1]}'" : '';
					$line .= ",\n";
				break;

				case 'firebird':
					$line .= "\t{$column_name} {$column_type} ";

					if (!is_null($column_data[1]))
					{
						$line .= 'DEFAULT ' . ((is_numeric($column_data[1])) ? $column_data[1] : "'{$column_data[1]}'") . ' ';
					}

					$line .= 'NOT NULL';

					// This is a UNICODE column and thus should be given it's fair share
					if (preg_match('/^X?STEXT_UNI|VCHAR_(CI|UNI:?)/', $column_data[0]))
					{
						$line .= ' COLLATE UNICODE';
					}

					$line .= ",\n";

					if (isset($column_data[2]) && $column_data[2] == 'auto_increment')
					{
						$generator = $column_name;
					}
				break;

				case 'mssql':
					if ($column_type == '[text]')
					{
						$textimage = true;
					}

					$line .= "\t[{$column_name}] {$column_type} ";

					if (!is_null($column_data[1]))
					{
						// For hexadecimal values do not use single quotes
						if (strpos($column_data[1], '0x') === 0)
						{
							$line .= 'DEFAULT (' . $column_data[1] . ') ';
						}
						else
						{
							$line .= 'DEFAULT (' . ((is_numeric($column_data[1])) ? $column_data[1] : "'{$column_data[1]}'") . ') ';
						}
					}

					if (isset($column_data[2]) && $column_data[2] == 'auto_increment')
					{
						$line .= 'IDENTITY (1, 1) ';
					}

					$line .= 'NOT NULL';
					$line .= " ,\n";
				break;

				case 'oracle':
					$line .= "\t{$column_name} {$column_type} ";
					$line .= (!is_null($column_data[1])) ? "DEFAULT '{$column_data[1]}' " : '';

					// In Oracle empty strings ('') are treated as NULL.
					// Therefore in oracle we allow NULL's for all DEFAULT '' entries
					$line .= ($column_data[1] === '') ? ",\n" : "NOT NULL,\n";

					if (isset($column_data[2]) && $column_data[2] == 'auto_increment')
					{
						$generator = $column_name;
					}
				break;

				case 'postgres':
					$line .= "\t{$column_name} {$column_type} ";

					if (isset($column_data[2]) && $column_data[2] == 'auto_increment')
					{
						$line .= "DEFAULT nextval('{$table_name}_seq'),\n";

						// Make sure the sequence will be created before creating the table
						$line = "CREATE SEQUENCE {$table_name}_seq;\n\n" . $line;
					}
					else
					{
						$line .= (!is_null($column_data[1])) ? "DEFAULT '{$column_data[1]}' " : '';
						$line .= "NOT NULL";

						// Unsigned? Then add a CHECK contraint
						if (in_array($orig_column_type, $unsigned_types))
						{
							$line .= " CHECK ({$column_name} >= 0)";
						}

						$line .= ",\n";
					}
				break;
			}
		}

		switch ($dbms)
		{
			case 'firebird':
				// Remove last line delimiter...
				$line = substr($line, 0, -2);
				$line .= "\n);;\n\n";
			break;

			case 'mssql':
				$line = substr($line, 0, -2);
				$line .= "\n) ON [PRIMARY]" . (($textimage) ? ' TEXTIMAGE_ON [PRIMARY]' : '') . "\n";
				$line .= "GO\n\n";
			break;
		}

		// Write primary key
		if (isset($table_data['PRIMARY_KEY']))
		{
			if (!is_array($table_data['PRIMARY_KEY']))
			{
				$table_data['PRIMARY_KEY'] = array($table_data['PRIMARY_KEY']);
			}

			switch ($dbms)
			{
				case 'mysql_40':
				case 'mysql_41':
				case 'postgres':
					$line .= "\tPRIMARY KEY (" . implode(', ', $table_data['PRIMARY_KEY']) . "),\n";
				break;

				case 'firebird':
					$line .= "ALTER TABLE {$table_name} ADD PRIMARY KEY (" . implode(', ', $table_data['PRIMARY_KEY']) . ");;\n\n";
				break;

				case 'sqlite':
					if ($generator === false || !in_array($generator, $table_data['PRIMARY_KEY']))
					{
						$line .= "\tPRIMARY KEY (" . implode(', ', $table_data['PRIMARY_KEY']) . "),\n";
					}
				break;

				case 'mssql':
					$line .= "ALTER TABLE [{$table_name}] WITH NOCHECK ADD \n";
					$line .= "\tCONSTRAINT [PK_{$table_name}] PRIMARY KEY  CLUSTERED \n";
					$line .= "\t(\n";
					$line .= "\t\t[" . implode("],\n\t\t[", $table_data['PRIMARY_KEY']) . "]\n";
					$line .= "\t)  ON [PRIMARY] \n";
					$line .= "GO\n\n";
				break;

				case 'oracle':
					$line .= "\tCONSTRAINT pk_{$table_name} PRIMARY KEY (" . implode(', ', $table_data['PRIMARY_KEY']) . "),\n";
				break;
			}
		}

		switch ($dbms)
		{
			case 'oracle':
				// UNIQUE contrains to be added?
				if (isset($table_data['KEYS']))
				{
					foreach ($table_data['KEYS'] as $key_name => $key_data)
					{
						if (!is_array($key_data[1]))
						{
							$key_data[1] = array($key_data[1]);
						}

						if ($key_data[0] == 'UNIQUE')
						{
							$line .= "\tCONSTRAINT u_phpbb_{$key_name} UNIQUE (" . implode(', ', $key_data[1]) . "),\n";
						}
					}
				}

				// Remove last line delimiter...
				$line = substr($line, 0, -2);
				$line .= "\n)\n/\n\n";
			break;

			case 'postgres':
				// Remove last line delimiter...
				$line = substr($line, 0, -2);
				$line .= "\n);\n\n";
			break;

			case 'sqlite':
				// Remove last line delimiter...
				$line = substr($line, 0, -2);
				$line .= "\n);\n\n";
			break;
		}

		// Write Keys
		if (isset($table_data['KEYS']))
		{
			foreach ($table_data['KEYS'] as $key_name => $key_data)
			{
				if (!is_array($key_data[1]))
				{
					$key_data[1] = array($key_data[1]);
				}

				switch ($dbms)
				{
					case 'mysql_40':
					case 'mysql_41':
						$line .= ($key_data[0] == 'INDEX') ? "\tKEY" : '';
						$line .= ($key_data[0] == 'UNIQUE') ? "\tUNIQUE" : '';
						foreach ($key_data[1] as $key => $col_name)
						{
							if (isset($modded_array[$col_name]))
							{
								switch ($modded_array[$col_name])
								{
									case 'text':
									case 'blob':
										$key_data[1][$key] = $col_name . '(255)';
									break;
								}
							}
						}
						$line .= ' ' . $key_name . ' (' . implode(', ', $key_data[1]) . "),\n";
					break;

					case 'firebird':
						$line .= ($key_data[0] == 'INDEX') ? 'CREATE INDEX' : '';
						$line .= ($key_data[0] == 'UNIQUE') ? 'CREATE UNIQUE INDEX' : '';

						$line .= ' ' . $table_name . '_' . $key_name . ' ON ' . $table_name . '(' . implode(', ', $key_data[1]) . ");;\n";
					break;

					case 'mssql':
						$line .= ($key_data[0] == 'INDEX') ? 'CREATE  INDEX' : '';
						$line .= ($key_data[0] == 'UNIQUE') ? 'CREATE  UNIQUE  INDEX' : '';
						$line .= " [{$key_name}] ON [{$table_name}]([" . implode('], [', $key_data[1]) . "]) ON [PRIMARY]\n";
						$line .= "GO\n\n";
					break;

					case 'oracle':
						if ($key_data[0] == 'UNIQUE')
						{
							continue;
						}

						$line .= ($key_data[0] == 'INDEX') ? 'CREATE INDEX' : '';
						
						$line .= " {$table_name}_{$key_name} ON {$table_name} (" . implode(', ', $key_data[1]) . ")\n";
						$line .= "/\n";
					break;

					case 'sqlite':
						$line .= ($key_data[0] == 'INDEX') ? 'CREATE INDEX' : '';
						$line .= ($key_data[0] == 'UNIQUE') ? 'CREATE UNIQUE INDEX' : '';

						$line .= " {$table_name}_{$key_name} ON {$table_name} (" . implode(', ', $key_data[1]) . ");\n";
					break;

					case 'postgres':
						$line .= ($key_data[0] == 'INDEX') ? 'CREATE INDEX' : '';
						$line .= ($key_data[0] == 'UNIQUE') ? 'CREATE UNIQUE INDEX' : '';

						$line .= " {$table_name}_{$key_name} ON {$table_name} (" . implode(', ', $key_data[1]) . ");\n";
					break;
				}
			}
		}

		switch ($dbms)
		{
			case 'mysql_40':
				// Remove last line delimiter...
				$line = substr($line, 0, -2);
				$line .= "\n);\n\n";
			break;

			case 'mysql_41':
				// Remove last line delimiter...
				$line = substr($line, 0, -2);
				$line .= "\n) CHARACTER SET `utf8` COLLATE `utf8_bin`;\n\n";
			break;

			// Create Generator
			case 'firebird':
				if ($generator !== false)
				{
					$line .= "\nCREATE GENERATOR {$table_name}_gen;;\n";
					$line .= 'SET GENERATOR ' . $table_name . "_gen TO 0;;\n\n";

					$line .= 'CREATE TRIGGER t_' . $table_name . ' FOR ' . $table_name . "\n";
					$line .= "BEFORE INSERT\nAS\nBEGIN\n";
					$line .= "\tNEW.{$generator} = GEN_ID({$table_name}_gen, 1);\nEND;;\n\n";
				}
			break;

			case 'oracle':
				if ($generator !== false)
				{
					$line .= "\nCREATE SEQUENCE {$table_name}_seq\n/\n\n";

					$line .= "CREATE OR REPLACE TRIGGER t_{$table_name}\n";
					$line .= "BEFORE INSERT ON {$table_name}\n";
					$line .= "FOR EACH ROW WHEN (\n";
					$line .= "\tnew.{$generator} IS NULL OR new.{$generator} = 0\n";
					$line .= ")\nBEGIN\n";
					$line .= "\tSELECT {$table_name}_seq.nextval\n";
					$line .= "\tINTO :new.{$generator}\n";
					$line .= "\tFROM dual;\nEND;\n/\n\n";
				}
			break;
		}

		fwrite($fp, $line . "\n");
	}

	$line = '';

	// Write custom function at the end for some db's
	switch ($dbms)
	{
		case 'mssql':
			$line = "\nCOMMIT\nGO\n\n";
		break;

		case 'sqlite':
			$line = "\nCOMMIT;";
		break;

		case 'postgres':
			$line = "\nCOMMIT;";
		break;
	}

	fwrite($fp, $line);
	fclose($fp);
}


/**
* Define the basic structure
* The format:
*		array('{TABLE_NAME}' => {TABLE_DATA})
*		{TABLE_DATA}:
*			COLUMNS = array({column_name} = array({column_type}, {default}, {auto_increment}))
*			PRIMARY_KEY = {column_name(s)}
*			KEYS = array({key_name} = array({key_type}, {column_name(s)})),
*
*	Column Types:
*	INT:x		=> SIGNED int(x)
*	BINT		=> BIGINT
*	UINT		=> mediumint(8) UNSIGNED
*	UINT:x		=> int(x) UNSIGNED
*	TINT:x		=> tinyint(x)
*	USINT		=> smallint(4) UNSIGNED (for _order columns)
*	BOOL		=> tinyint(1) UNSIGNED
*	VCHAR		=> varchar(255)
*	CHAR:x		=> char(x)
*	XSTEXT_UNI	=> text for storing 100 characters (topic_title for example)
*	STEXT_UNI	=> text for storing 255 characters (normal input field with a max of 255 single-byte chars) - same as VCHAR_UNI
*	TEXT_UNI	=> text for storing 3000 characters (short text, descriptions, comments, etc.)
*	MTEXT_UNI	=> mediumtext (post text, large text)
*	VCHAR:x		=> varchar(x)
*	TIMESTAMP	=> int(11) UNSIGNED
*	DECIMAL		=> decimal number (5,2)
*	DECIMAL:x	=> decimal number (x,2)
*	PDECIMAL	=> precision decimal number (6,3)
*	PDECIMAL:x	=> precision decimal number (x,3)
*	VCHAR_UNI	=> varchar(255) BINARY
*	VCHAR_CI	=> varchar_ci for postgresql, others VCHAR
*/
function get_schema_struct()
{
	$schema_data = array();

	$schema_data['phpbb_garage_vehicles'] = array(
		'COLUMNS'		=> array(
			'id'			=> array('UINT', NULL, 'auto_increment'),
			'user_id'		=> array('UINT', 0),
			'made_year'		=> array('UINT', '2007'),
			'engine_type'		=> array('TINT:2', 0),
			'colour'		=> array('XSTEXT_UNI', ''),
			'mileage'		=> array('UINT', 0),
			'mileage_unit'		=> array('VCHAR:32', 'Miles'),
			'price'			=> array('UINT', 0),
			'currency'		=> array('VCHAR:32', 'EUR'),
			'comments'		=> array('MTEXT_UNI', ''),
			'views'			=> array('UINT', 0),
			'date_created'		=> array('TIMESTAMP', 0),
			'date_updated'		=> array('TIMESTAMP', 0),
			'make_id'		=> array('UINT', 0),
			'model_id'		=> array('UINT', 0),
			'main_vehicle'		=> array('BOOL', 0),
			'weighted_rating'	=> array('DECIMAL:4', 0),
			'bbcode_bitfield'	=> array('VCHAR:255', ''),
			'bbcode_uid'		=> array('VCHAR:5', ''),
			'pending'		=> array('BOOL', 0),
		),
		'PRIMARY_KEY'	=> 'id',
		'KEYS'			=> array(
			'date_created'		=> array('INDEX', 'filetime'),
			'date_updated'		=> array('INDEX', 'post_msg_id'),
			'user_id'		=> array('INDEX', 'topic_id'),
			'views'			=> array('INDEX', 'poster_id'),
		),
	);

	$schema_data['phpbb_garage_business'] = array(
		'COLUMNS'		=> array(
			'id'			=> array('UINT', NULL, 'auto_increment'),
			'title'			=> array('XSTEXT_UNI', ''),
			'address'		=> array('VCHAR', ''),
			'telephone'		=> array('VHCAR:100', ''),
			'fax'			=> array('VHCAR:100', ''),
			'website'		=> array('VHCAR', ''),
			'email'			=> array('VHCAR:100', ''),
			'opening_hours'		=> array('VCHAR', ''),
			'insurance'		=> array('BOOL', 0),
			'garage'		=> array('BOOL', 0),
			'retail'		=> array('BOOL', 0),
			'product'		=> array('BOOL', 0),
			'dynocentre'		=> array('BOOL', 0),
			'pending'		=> array('BOOL', 0),
			'comments'		=> array('TEXT_UNI', ''),
		),
		'PRIMARY_KEY'	=> 'id',
		'KEYS'			=> array(
			'insurance'		=> array('INDEX', 'group_id'),
			'garage'		=> array('INDEX', 'group_id'),
			'retail'		=> array('INDEX', 'group_id'),
			'product'		=> array('INDEX', 'group_id'),
			'dynocentre'		=> array('INDEX', 'group_id'),
		),
	);

	$schema_data['phpbb_garage_categories'] = array(
		'COLUMNS'		=> array(
			'id'			=> array('UINT', NULL, 'auto_increment'),
			'title'			=> array('TEXT_UNI', ''),
			'field_order'		=> array('USINT', 0),
		),
		'PRIMARY_KEY'	=> 'id',
		'KEYS'			=> array(
			'title'		=> array('INDEX', 'auth_option'),
			'id'		=> array('INDEX', array('id', 'title')),
		),
	);

	$schema_data['phpbb_garage_config'] = array(
		'COLUMNS'		=> array(
			'config_name'		=> array('VCHAR', ''),
			'config_value'		=> array('VCHAR_UNI', ''),
		),
		'PRIMARY_KEY'	=> 'config_name',
	);

	$schema_data['phpbb_garage_vehicles_gallery'] = array(
		'COLUMNS'		=> array(
			'id'			=> array('UINT', NULL, 'auto_increment'),
			'vehicle_id'		=> array('UINT', 0),
			'image_id'		=> array('UINT', 0),
			'hilite'		=> array('BOOL', 0),
		),
		'PRIMARY_KEY'	=> 'id',
		'KEYS'			=> array(
			'vehicle_id'		=> array('INDEX', 'vehicle_id'),
			'image_id'		=> array('INDEX', 'image_id'),
		),
	);

	$schema_data['phpbb_garage_modifications_gallery'] = array(
		'COLUMNS'		=> array(
			'id'			=> array('UINT', NULL, 'auto_increment'),
			'vehicle_id'		=> array('UINT', 0),
			'modification_id'	=> array('UINT', 0),
			'image_id'		=> array('UINT', 0),
			'hilite'		=> array('BOOL', 0),
		),
		'PRIMARY_KEY'	=> 'id',
		'KEYS'			=> array(
			'vehicle_id'		=> array('INDEX', 'vehicle_id'),
			'image_id'		=> array('INDEX', 'image_id'),
		),
	);

	$schema_data['phpbb_garage_quartermiles_gallery'] = array(
		'COLUMNS'		=> array(
			'id'			=> array('UINT', NULL, 'auto_increment'),
			'vehicle_id'		=> array('UINT', 0),
			'quartermile_id'	=> array('UINT', 0),
			'image_id'		=> array('UINT', 0),
			'hilite'		=> array('BOOL', 0),
		),
		'PRIMARY_KEY'	=> 'id',
		'KEYS'			=> array(
			'vehicle_id'		=> array('INDEX', 'vehicle_id'),
			'image_id'		=> array('INDEX', 'image_id'),
		),
	);

	$schema_data['phpbb_dynoruns_gallery'] = array(
		'COLUMNS'		=> array(
			'id'			=> array('UINT', NULL, 'auto_increment'),
			'vehicle_id'		=> array('UINT', 0),
			'dynorun_id'		=> array('UINT', 0),
			'image_id'		=> array('UINT', 0),
			'hilite'		=> array('BOOL', 0),
		),
		'PRIMARY_KEY'	=> 'id',
		'KEYS'			=> array(
			'vehicle_id'		=> array('INDEX', 'vehicle_id'),
			'image_id'		=> array('INDEX', 'image_id'),
		),
	);

	$schema_data['phpbb_garage_laps_gallery'] = array(
		'COLUMNS'		=> array(
			'id'			=> array('UINT', NULL, 'auto_increment'),
			'vehicle_id'		=> array('UINT', 0),
			'lap_id'		=> array('UINT', 0),
			'image_id'		=> array('UINT', 0),
			'hilite'		=> array('BOOL', 0),
		),
		'PRIMARY_KEY'	=> 'id',
		'KEYS'			=> array(
			'vehicle_id'		=> array('INDEX', 'vehicle_id'),
			'image_id'		=> array('INDEX', 'image_id'),
		),
	);

	$schema_data['phpbb_garage_guestbooks'] = array(
		'COLUMNS'		=> array(
			'id'			=> array('UINT', NULL, 'auto_increment'),
			'vehicle_id'		=> array('UINT', 0),
			'author_id'		=> array('UINT', 0),
			'post_date'		=> array('TIMESTAMP', 0),
			'ip_address'		=> array('VCHAR:40', ''),
			'bbcode_bitfield'	=> array('VCHAR:255', ''),
			'bbcode_uid'		=> array('VCHAR:5', ''),
			'pending'		=> array('BOOL', 0),
			'post'			=> array('MTEXT_UNI', ''),
		),
		'PRIMARY_KEY'	=> 'id',
		'KEYS'			=> array(
			'vehicle_id'		=> array('INDEX', 'vehicle_id'),
			'author_id'		=> array('INDEX', 'vehicle_id'),
			'post_date'		=> array('INDEX', 'vehicle_id'),
		),
	);

	$schema_data['phpbb_garage_images'] = array(
		'COLUMNS'		=> array(
			'attach_id'		=> array('UINT', NULL, 'auto_increment'),
			'vehicle_id'		=> array('UINT', 0),
			'attach_location'	=> array('VCHAR', ''),
			'attach_hits'		=> array('UINT', 0),
			'attach_ext'		=> array('VCHAR:100', ''),
			'attach_file'		=> array('VCHAR', ''),
			'attach_thumb_location'	=> array('VCHAR', ''),
			'attach_thumb_width'	=> array('USINT', 0),
			'attach_thumb_height'	=> array('USINT', 0),
			'attach_is_image'	=> array('BOOL', 0),
			'attach_date'		=> array('TIMESTAMP', 0),
			'attach_filesize'	=> array('UINT:20', 0),
			'attach_thumb_filesize'	=> array('UINT:20', 0),
		),
		'PRIMARY_KEY'	=> 'attach_id',
	);

	$schema_data['phpbb_garage_premiums'] = array(
		'COLUMNS'		=> array(
			'id'			=> array('UINT', NULL, 'auto_increment'),
			'vehicle_id'		=> array('UINT', 0),
			'business_id'		=> array('UINT', 0),
			'cover_type_id'		=> array('UINT', 0),
			'premium'		=> array('UINT', 0),
			'comments'		=> array('MTEXT_UNI', ),
		),
		'PRIMARY_KEY'	=> 'id',
	);

	$schema_data['phpbb_garage_makes'] = array(
		'COLUMNS'		=> array(
			'id'			=> array('UINT', NULL, 'auto_increment'),
			'make'			=> array('VCHAR', ''),
			'pending'		=> array('BOOL', 0),
		),
		'PRIMARY_KEY'	=> 'id',
		'KEYS'			=> array(
			'make'			=> array('INDEX', 'make'),
		),
	);

	$schema_data['phpbb_garage_models'] = array(
		'COLUMNS'		=> array(
			'id'			=> array('UINT', NULL, 'auto_increment'),
			'make_id'		=> array('UINT', 0),
			'model'			=> array('VCHAR', ''),
			'pending'		=> array('BOOL', 0),
		),
		'PRIMARY_KEY'	=> 'id',
		'KEYS'			=> array(
			'make_id'		=> array('INDEX', 'make_id'),
		),
	);

	$schema_data['phpbb_garage_modifications'] = array(
		'COLUMNS'		=> array(
			'id'			=> array('UINT', NULL, 'auto_increment'),
			'vehicle_id'		=> array('UINT', 0),
			'user_id'		=> array('UINT', 0),
			'category_id'		=> array('UINT', 0),
			'manufacturer_id'	=> array('UINT', 0),
			'product_id'		=> array('UINT', 0),
			'price'			=> array('', ),
			'install_price'		=> array('', ),
			'product_rating'	=> array('', ),
			'purchase_rating'	=> array('', ),
			'install_rating'	=> array('', ),
			'shop_id'		=> array('UINT', 0),
			'installer_id'		=> array('UINT', 0),
			'comments'		=> array('MTEXT_UNI', ''),
			'install_comments'	=> array('MTEXT_UNI', ''),
			'date_created'		=> array('TIMESTAMP', 0),
			'date_updated'		=> array('TIMESTAMP', 0),
		),
		'PRIMARY_KEY'	=> 'id',
		'KEYS'			=> array(
			'user_id'		=> array('INDEX', 'make_id'),
			'vehicle_id_2'		=> array('INDEX', array('vehicle_id', 'category_id')),
			'category_id'		=> array('INDEX', 'make_id'),
			'vehicle_id'		=> array('INDEX', 'make_id'),
			'date_created'		=> array('INDEX', 'make_id'),
			'date_updated'		=> array('INDEX', 'make_id'),
		),
	);

	$schema_data['phpbb_garage_products'] = array(
		'COLUMNS'		=> array(
			'id'			=> array('UINT', NULL, 'auto_increment'),
			'business_id'		=> array('UINT', 0),
			'category_id'		=> array('UINT', 0),
			'title'			=> array('VCHAR', ''),
			'pending'		=> array('BOOL', 0),
		),
		'PRIMARY_KEY'	=> 'id',
		'KEYS'			=> array(
			'business_id'		=> array('INDEX', 'business_id'),
			'category_id'		=> array('INDEX', 'category_id'),
		),
	);

	$schema_data['phpbb_garage_quartermiles'] = array(
		'COLUMNS'		=> array(
			'id'			=> array('UINT', NULL, 'auto_increment'),
			'vehicle_id'		=> array('UINT', 0),
			'rt'			=> array('PDECIMAL', 0),
			'sixty'			=> array('PDECIMAL', 0),
			'three'			=> array('PDECIMAL', 0),
			'eighth'		=> array('PDECIMAL', 0),
			'eighthmph'		=> array('PDECIMAL', 0),
			'thou'			=> array('PDECIMAL', 0),
			'quart'			=> array('PDECIMAL', 0),
			'quartmph'		=> array('PDECIMAL', 0),
			'pending'		=> array('BOOL', 0),
			'dynorun_id'		=> array('UINT', 0),
			'date_created'		=> array('TIMESTAMP', 0),
			'date_updated'		=> array('TIMESTAMP', 0),
		),
		'PRIMARY_KEY'	=> 'id',
	);

	$schema_data['phpbb_garage_dynoruns'] = array(
		'COLUMNS'		=> array(
			'id'			=> array('UINT', NULL, 'auto_increment'),
			'vehicle_id'		=> array('UINT', 0),
			'dynocentre_id'		=> array('UINT', 0),
			'bhp'			=> array('DECIMAL:6', 0),
			'bhp_unit'		=> array('VCHAR:32', ''),
			'torque'		=> array('DECIMAL:6', 0),
			'torque_unit'		=> array('VCHAR:32', ''),
			'boost'			=> array('DECIMAL:6', 0),
			'boost_unit'		=> array('VCHAR:32', ''),
			'nitrous'		=> array('UINT', 0),
			'peakpoint'		=> array('PDECIMAL:7', 0),
			'date_created'		=> array('TIMESTAMP', 0),
			'date_updated'		=> array('TIMESTAMP', 0),
			'pending'		=> array('BOOL', 0),
		),
		'PRIMARY_KEY'	=> 'id',
	);

	$schema_data['phpbb_garage_ratings'] = array(
		'COLUMNS'		=> array(
			'id'			=> array('UINT', NULL, 'auto_increment'),
			'vehicle_id'		=> array('UINT', 0),
			'rating'		=> array('UINT', 0),
			'user_id'		=> array('UINT', 0),
			'rate_date'		=> array('TIMESTAMP', 0),
		),
		'PRIMARY_KEY'	=> 'id',
	);

	$schema_data['phpbb_garage_tracks'] = array(
		'COLUMNS'		=> array(
			'id'			=> array('UINT', NULL, 'auto_increment'),
			'title'			=> array('VCHAR', ''),
			'length'		=> array('VCHAR:32', ''),
			'mileage_unit'		=> array('VCHAR:32', ''),
			'pending'		=> array('BOOL', 0),
		),
		'PRIMARY_KEY'	=> 'id',
	);

	$schema_data['phpbb_garage_laps'] = array(
		'COLUMNS'		=> array(
			'id'			=> array('UINT', NULL, 'auto_increment'),
			'vehicle_id'		=> array('UINT', 0),
			'track_id'		=> array('UINT', 0),
			'condition_id'		=> array('UINT', 0),
			'type_id'		=> array('UINT', 0),
			'minute'		=> array('UINT:2', 0),
			'second'		=> array('UINT:2', 0),
			'millisecond'		=> array('UINT:2', 0),
			'pending'		=> array('BOOL', 0),
		),
		'PRIMARY_KEY'	=> 'id',
		'KEYS'			=> array(
			'vehicle_id'		=> array('INDEX', 'vehicle_id'),
			'track_id'		=> array('INDEX', 'track_id'),
		),
	);

	$schema_data['phpbb_garage_service_history'] = array(
		'COLUMNS'		=> array(
			'id'			=> array('UINT', NULL, 'auto_increment'),
			'vehicle_id'		=> array('UINT', 0),
			'garage_id'		=> array('UINT', 0),
			'type_id'		=> array('UINT', 0),
			'price'			=> array('UINT', 0),
			'rating'		=> array('UINT', 0),
			'mileage'		=> array('UINT', 0),
			'date_created'		=> array('TIMESTAMP', 0),
			'date_updated'		=> array('TIMESTAMP', 0),
		),
		'PRIMARY_KEY'	=> 'id',
		'KEYS'			=> array(
			'vehicle_id'		=> array('INDEX', 'vehicle_id'),
			'garage_id'		=> array('INDEX', 'garage_id'),
		),
	);

	$schema_data['phpbb_garage_blog'] = array(
		'COLUMNS'		=> array(
			'id'			=> array('UINT', NULL, 'auto_increment'),
			'vehicle_id'		=> array('UINT', 0),
			'user_id'		=> array('UINT', 0),
			'blog_title'		=> array('XSTEXT_UNI', ''),
			'blog_text'		=> array('MTEXT_UNI', ''),
			'blog_date'		=> array('TIMESTAMP', 0),
			'bbcode_bitfield'	=> array('VCHAR:255', ''),
			'bbcode_uid'		=> array('VCHAR:5', ''),
		),
		'PRIMARY_KEY'	=> 'id',
		'KEYS'			=> array(
			'vehicle_id'		=> array('INDEX', 'vehicle_id'),
			'user_id'		=> array('INDEX', 'user_id'),
		),
	);

	$schema_data['phpbb_garage_custom_fields'] = array(
		'COLUMNS'		=> array(
			'field_id'		=> array('UINT', NULL, 'auto_increment'),
			'field_name'		=> array('VCHAR_UNI', ''),
			'field_type'		=> array('TINT:4', 0),
			'field_ident'		=> array('VCHAR:20', ''),
			'field_length'		=> array('VCHAR:20', ''),
			'field_minlen'		=> array('VCHAR', ''),
			'field_maxlen'		=> array('VCHAR', ''),
			'field_novalue'		=> array('VCHAR_UNI', ''),
			'field_default_value'	=> array('VCHAR_UNI', ''),
			'field_validation'	=> array('VCHAR_UNI:20', ''),
			'field_required'	=> array('BOOL', 0),
			'field_show_on_reg'	=> array('BOOL', 0),
			'field_hide'		=> array('BOOL', 0),
			'field_no_view'		=> array('BOOL', 0),
			'field_active'		=> array('BOOL', 0),
			'field_order'		=> array('UINT', 0),
		),
		'PRIMARY_KEY'	=> 'field_id',
		'KEYS'			=> array(
			'fld_type'		=> array('INDEX', 'field_type'),
			'fld_ordr'		=> array('INDEX', 'field_order'),
		),
	);

	$schema_data['phpbb_garage_custom_fields_data'] = array(
		'COLUMNS'		=> array(
			'user_id'		=> array('UINT', 0),
		),
		'PRIMARY_KEY'	=> 'user_id',
	);

	$schema_data['phpbb_garage_custom_fields_lang'] = array(
		'COLUMNS'		=> array(
			'field_id'		=> array('UINT', 0),
			'lang_id'		=> array('UINT', 0),
			'option_id'		=> array('UINT', 0),
			'field_type'		=> array('TINT:4', 0),
			'lang_value'		=> array('VCHAR_UNI', ''),
		),
		'PRIMARY_KEY'	=> array('field_id', 'lang_id', 'option_id'),
	);

	$schema_data['phpbb_garage_lang'] = array(
		'COLUMNS'		=> array(
			'field_id'		=> array('UINT', 0),
			'lang_id'		=> array('UINT', 0),
			'lang_name'		=> array('VCHAR_UNI', ''),
			'lang_explain'		=> array('TEXT_UNI', ''),
			'lang_default_value'	=> array('VCHAR_UNI', ''),
		),
		'PRIMARY_KEY'	=> array('field_id', 'lang_id'),
	);

	return $schema_data;
}

?>
