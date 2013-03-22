<?php Hw2\S_Core::checkAccess(); ?>
<div class="containerHolder">
    <div class="container">

        <!-- h2 stays for breadcrumbs -->
        <h2><a href="#" class="active">Dashboard</a></h2>

        <div class="main">
            <form action="" class="jNice">
                <h3>Available Backups</h3>
                <table cellpadding="0" cellspacing="0">
                    <?php
// List the files
                    if (!is_dir($bakDir)) {
                        mkdir($bakDir);
                    }
                    $dir = opendir($bakDir);
                    while (false !== ($file = readdir($dir))) {
                        if ( in_array($file,Array('.','..','._'))) 
                            continue;
                        // Remove the sql extension part in the filename
                        $filenameboth = basename($file);

                        // Print the cells
                        print("<tr>\n");
                        print("  <td>" . $filenameboth . "</td>\n");
                        print("  <td class='action'><a href='".$query."&action=restore&id=" . $filenameboth . "' class='edit'>Restore</a>\n");
                        //print("<a href='" .$bakDir.DS.$filenameboth . "' class='view'>Download SQL</a>\n");
                        //print("<a href='" .$bakDir.DS.$filenameboth . "' class='view'>Download ZIP</a>\n");
                        print("<a href='".$query."&action=delete&file=" . $filenameboth . "' class='delete'>Delete</a></td>\n");
                        print("</tr>\n");
                    }
                    ?>

                </table>
                <br />
            </form>
        </div>
        <!-- // #main -->

        <div class="clear"></div>
    </div>
    <!-- // #container -->
</div>	
<!-- // #containerHolder -->
