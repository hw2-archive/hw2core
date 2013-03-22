#!/bin/bash
# change the directory to parent folder ( platform root )

function init () {
    local D=${HW2_CONF['HW2PATH_PLATFORM']}
    local NAME=$(basename $D)

    copy_dir $D "${HW2_CONF['HW2PATH_BACKUP']}${HW2_CONF['PLATFORM']}$DS$NAME" 1 1
}

init

