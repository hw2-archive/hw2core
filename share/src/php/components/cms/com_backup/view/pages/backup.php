<?php Hw2\S_Core::checkAccess(); ?>
<div class="containerHolder">
    <div class="container">

        <!-- h2 stays for breadcrumbs -->
        <h2><a href="#" class="active">Create a Backup</a></h2>

        <div class="main">
            <form action="" class="jNice">
                <h3>Backup Log</h3>
                <table cellpadding="0" cellspacing="0"><td>
                         <?php Hw2\S_Com_Backup::run("backup"); ?>
                    </td></table>
                <br />
            </form>
        </div>
        <!-- // #main -->

        <div class="clear"></div>
    </div>
    <!-- // #container -->
</div>	
<!-- // #containerHolder -->