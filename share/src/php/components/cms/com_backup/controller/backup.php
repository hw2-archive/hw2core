<?php Hw2\S_Core::checkAccess();

set_time_limit(0);
ini_set('default_socket_timeout',0);
ini_set('max_execution_time',0);
ignore_user_abort(true);

// Backup the table and save it to a sql file
function backup_db($host, $user, $pass, $name, $tables, $bckextname, $filess) {
    $link = mysql_connect($host, $user, $pass);
    mysql_select_db($name, $link);
    $return = "";

    // Get all of the tables
    if ($tables == '*') {
        $tables = array();
        $result = mysql_query('SHOW TABLES');
        while ($row = mysql_fetch_row($result)) {
            $tables[] = $row[0];
        }
    } else {
        if (is_array($tables)) {
            $tables = explode(',', $tables);
        }
    }

    // Cycle through each provided table
    foreach ($tables as $table) {
        $result = mysql_query('SELECT * FROM ' . $table);
        $num_fields = mysql_num_fields($result);

        // First part of the output - remove the table
        $return .= 'DROP TABLE IF EXISTS ' . $table . ';';

        // Second part of the output - create table
        $row2 = mysql_fetch_row(mysql_query('SHOW CREATE TABLE ' . $table));
        $return .= "\n\n" . $row2[1] . ";\n\n";

        // Third part of the output - insert values into new table
        for ($i = 0; $i < $num_fields; $i++) {
            while ($row = mysql_fetch_row($result)) {
                $return.= 'INSERT INTO ' . $table . ' VALUES(';
                for ($j = 0; $j < $num_fields; $j++) {
                    $row[$j] = addslashes($row[$j]);
                    $row[$j] = preg_replace("/\n/", "\\n", $row[$j]);
                    if (isset($row[$j])) {
                        $return .= '"' . $row[$j] . '"';
                    } else {
                        $return .= '""';
                    }
                    if ($j < ($num_fields - 1)) {
                        $return.= ',';
                    }
                }
                $return.= ");\n";
            }
        }
        $return.="\n\n\n";
    }

    // Save the sql file
    $handle = fopen($filess . '.sql', 'w+');
    fwrite($handle, $return);
    fclose($handle);

    // Close MySQL Connection
    mysql_close();
}

// Set the suffix of the backup filename
if ($table == '*') {
    $extname = 'all';
} else {
    $extname = str_replace(",", "_", $table);
    $extname = str_replace(" ", "_", $extname);
}

// Generate the filename for the backup file
$basename = $bakDir . DS . 'backup_' . date("d.m.Y_H_i_s");
$filess = $basename . '_' . $extname;

// Call the backup function for all tables in a DB
backup_db($DBhost, $DBuser, $DBpass, $DBName, $table, $extname, $filess);

Hw2\S_FileSys::pclZipDir(HW2PATH_PARENT, $basename, $basename, Array($bakDir));

// Print the message
print('The backup has been created successfully.');
?>

