#!/bin/bash
#
# it's a link to mysql_tools
#

function init() {
    echo "choose option:"
    echo "0: dump ( from mysql to files )"
    echo "1: import (from files to mysql )"
    read method
    local mt=${HW2_CONF['HW2PATH_APPS']}"hw2mysqltool"$DS"mysql_tools";
    if (($method == 0)); then
        source $mt "dump" "" "" "" ""
    else
        source $mt "import" "" "" "" ""
    fi
}

init
