<?php Hw2\S_Core::checkAccess();

// Get the filename to be deleted
$file=$_GET['file'];

// Check if the file has needed args
if ($file==NULL){
  print("<script type='text/javascript'>window.alert('You have not provided a file to delete.')</script>");
  print("<script type='text/javascript'>window.location='manage.php'</script>");
  print("You have not provided a file to delete.<br>Click <a href='manage.php'>here</a> if your browser doesn't automatically redirect you.");
  die();
}

// Delete the SQL file
if (!is_dir($bakDir.DS. $file)) {
    if (unlink($bakDir.DS. $file ))
            print("DELETED!");
}


?>
