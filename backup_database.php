<?php
// Database backup
function backup_database($host, $user, $pass, $name, $tables = '*') {
 
    $link = mysql_connect($host, $user, $pass);
    mysql_select_db($name, $link);
 
    //get all of the tables
    if ($tables == '*') {
        $tables = array();
        $result = mysql_query('SHOW TABLES');
        while ($row = mysql_fetch_row($result)) {
            $tables[] = $row[0];
        }
    } else {
        $tables = is_array($tables) ? $tables : explode(',', $tables);
    }
    $return = "";
 
    //cycle through
    foreach ($tables as $table) {
        $result = mysql_query('SELECT * FROM ' . $table);
        $num_fields = mysql_num_fields($result);
 
        $return .= "";
        $row2 = mysql_fetch_row(mysql_query('SHOW CREATE TABLE ' . $table));
        $return .= "\n\n" . $row2[1] . ";\n\n";
 
        for ($i = 0; $i < $num_fields; $i++) {
            while ($row = mysql_fetch_row($result)) {
                $return .= 'INSERT INTO ' . $table . ' VALUES(';
                for ($j = 0; $j < $num_fields; $j++) {
                    $row[$j] = addslashes($row[$j]);
                    //  $row[$j] = ereg_replace("\n", "\\n", $row[$j]);
                    if (isset($row[$j])) {
                        $return .= '"' . $row[$j] . '"';
                    } else {
                        $return .= '""';
                    }
                    if ($j < ($num_fields - 1)) {
                        $return .= ',';
                    }
                }
                $return .= ");\n";
            }
        }
        $return .= "\n\n\n";
    }
 
    $file_name = 'db-backup-' . date("Y-m-d--h-i-s") . ".sql";
    //Save file
    $handle = fopen($file_name, 'w+');
    fwrite($handle, $return);
    fclose($handle);
    return $file_name;
}
 
$backup_file_name=backup_database("localhost", "root", "password", "db_name");
  // For Backup mail this sql
 
?>
 
 