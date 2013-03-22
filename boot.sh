#!/bin/bash
hw2core_path="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
if [[ "${BASH_SOURCE[0]}" != "${0}" ]]; then #workaround 
    result=$( php -f "$hw2core_path/boot.php" -1 ) # if sourced, get only configurations
    eval $result
else
    php -f "$hw2core_path/boot.php" $1 #$1 is the option id to direct execute menu action
    read -p "press any key to exit"
fi;

