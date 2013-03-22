<?php Hw2\S_Core::checkAccess();

set_time_limit(0);
ini_set('default_socket_timeout',0);
ini_set('max_execution_time',0);
ignore_user_abort(true);

// Get the provided arg
$id = $_GET['id'];

// Check if the file has needed args
if ($id == NULL) {
    print("<script type='text/javascript'>window.alert('You have not provided a backup to restore.')</script>");
    print("<script type='text/javascript'>window.location='manage.php'</script>");
    print("You have not provided a backup to restore.<br>Click <a href='manage.php'>here</a> if your browser doesn't automatically redirect you.");
    die();
}

$ext = pathinfo($id, PATHINFO_EXTENSION);
if ($ext == "sql") {
// Generate filename and set error variables
    $filename = $bakDir . DS . $id;
    $sqlErrorText = '';
    $sqlErrorCode = 0;
    $sqlStmt = '';

// Restore the backup
    $con = mysql_connect($DBhost, $DBuser, $DBpass);
    if ($con !== false) {
        // Load and explode the sql file
        mysql_select_db("$DBName");
        $f = fopen($filename, "r+");
        $sqlFile = fread($f, filesize($filename));
        $sqlArray = explode(';<|||||||>', $sqlFile);

        // Process the sql file by statements
        foreach ($sqlArray as $stmt) {
            if (strlen($stmt) > 3) {
                $result = mysql_query($stmt);
            }
        }
    }

// Print message (error or success)
    if ($sqlErrorCode == 0) {
        print("Database restored successfully!<br>\n");
        print("Backup used: " . $filename . "<br>\n");
    } else {
        print("An error occurred while restoring backup!<br><br>\n");
        print("Error code: $sqlErrorCode<br>\n");
        print("Error text: $sqlErrorText<br>\n");
        print("Statement:<br/> $sqlStmt<br>");
    }

    // Close the connection
    mysql_close();
} else if ($ext == "zip") {
    $archive = new PclZip($bakDir . DS . $id); // load library
    // we can remove everything since we've changed pclzip temp path in config file
    Hw2\S_FileSys::rrmDir(HW2PATH_PARENT, Array($bakDir));  // remove all except backdir  
    // extract
    if ($archive->extract(PCLZIP_OPT_PATH, HW2PATH_PARENT . DS, PCLZIP_OPT_TEMP_FILE_ON) == 0) {
        die("Error : " . $archive->errorInfo(true));
    }

    // Files restored successfully
    print("Files restored successfully!<br>\n");
    print("Backup used: " . $filename . "<br>\n");
} else
    print("File cannot be restored\n");
?> 


