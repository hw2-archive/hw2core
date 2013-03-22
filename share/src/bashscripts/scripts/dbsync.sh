#!/bin/bash

function init() {
    local method=0
    echo "select sync method:"
    echo "1. from remote to local"
    echo "2. from local to remote" 
    read method

    DS_PATH=${HW2_CONF['HW2PATH_APPS']}"hw2dbsync"$DS;
    if (($method == 1)); then
        source $DS_PATH"tolocal.sh"
    elif (($method == 2)); then
        source $DS_PATH"toremote.sh"
    fi;

    read -p "completed"
}

init