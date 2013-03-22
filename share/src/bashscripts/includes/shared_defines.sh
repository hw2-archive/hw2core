#!/bin/bash

function pause(){
   read -p "$*"
}

WIN_BIN=1
if [ `uname -s` == "Linux" ]; then
   WIN_BIN=0
fi;


if (($WIN_BIN != 0)); then
	COMMAND_DIR="c:/WRK/tools/mklink /J" #junction
    COMMAND_FILE="c:/WRK/tools/mklink /H" #hard link
    COMMAND_HARD="c:/WRK/tools/mklink /H" #hard link
    COMMAND_SYM="c:/WRK/tools/mklink /J" #junction
else
    COMMAND_DIR="ln -sfvT" #symbolic link
    COMMAND_FILE="ln -sfvT" #symbolic link
    COMMAND_HARD="ln -fvT" #hard link
    COMMAND_SYM="ln -sfvT" #symbolic link
fi;

EXIGNORE=(
    "*~"
    'Thumbs.db:encryptable'
    'Thumbs.db'
    '.DS_Store'
    '*.bak'
    '*.orig'
    '*.patch'
    'callgrind.out.*'
    'patches-*'
    '.sync.ffs_db'
)

