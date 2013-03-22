#!/bin/bash

hw2common_conf_path="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

#
#   MYSQL_DUMP
#

hw2_setConf "TPATH" ${HW2_CONF['DB_DIR']}"tables"
# path of file to extract database full dump
hw2_setConf "FPATH" ${HW2_CONF['DB_DIR']}"full.sql"

# clean directory before dump, in this way non-existant db tables will be deleted
hw2_setConf "CLEANFOLDER" 1

# switch to enable(1)/disable(0) the dump/import of full db file
# ( you can do it manually using command parameters )
hw2_setConf "FULL" 0

# set 1 to enable --tab option for mysqldump and import data from it
# it's very fast import/export process but doesn't utilize the insert query
# ( NOTE: full db continue to be dumped with normal sql format ) 

hw2_setConf "TEXTDUMPS" 0

# (boolean) allow to change "TPATH" folder permissions to enable mysql server writing
hw2_setConf "CHMODE" 1

#
# TOOLS OPTIONS
#
#number of threads you want to use in TEXT import mode ( you can safely set it to your number of processor increasing process speed )
hw2_setConf "THREADS" 1
hw2_setConf "IMPORTOPTS_TEXT" "--use-threads=${HW2_CONF['THREADS']} --local --compress --delete --lock-tables"
hw2_setConf "DUMPOPTS" "--skip-comments --skip-set-charset --extended-insert --order-by-primary --single-transaction --quick"

#
# DB_SYNC
#
hw2_setConf "DS_CODE" "standard"
hw2_setConf "FILENAME" "my_db.sql"
hw2_setConf "REMOTEFILE" ${HW2_CONF['DB_DIR']}${HW2_CONF['FILENAME']}".gz"
hw2_setConf "LOCALFILE" ${HW2_CONF['DB_DIR']}${HW2_CONF['FILENAME']}".gz"
#hw2_setConf "RDUMP_OPT" ""


#
# SYMBOLIC LINKS
#

#ignoring files/folders to not be processed with find command


SHARED_FILTERS=(
    'hw2'
    'nbproject'
    '.netbeans_hw2'
    '.git'
    '.htaccess'
    '.project'
    '.settings'
    '.externalToolBuilders'
    '.buildpath'
)

SHARED_INCLUDES=( 
)

FILTERS+=(${SHARED_FILTERS[@]})
INCLUDES+=(${SHARED_INCLUDES[@]})

# HW2 DEFS ( all path must be relative to hw2 folder)
#global var used in process* functions

HW2_SHARED_FILTERS=(
    'nbproject'
    '.netbeans_hw2'
    'local'
    'boot.php' # actually we've to filter a file before inlcude it ( design issue )
    'index.php'
    'index.html'
    '.git'
    '.gitignore'
)

HW2_SHARED_INCLUDES=(
    `hw2_struct 'boot.php' 1`
)

HW2_FILTERS+=(${HW2_SHARED_FILTERS[@]})
HW2_INCLUDES+=(${HW2_SHARED_INCLUDES[@]})


HW2_LINKS+=(
    `hw2_struct 1 "${HW2_CONF['HW2PATH_APPS']}hw2dbsync" "${HW2_CONF['CS_DEVPATH']}hw2_apps"$DS"hw2dbsync"$DS`
    `hw2_struct 1 "${HW2_CONF['HW2PATH_APPS']}hw2mysqltool" "${HW2_CONF['CS_DEVPATH']}hw2_apps"$DS"hw2mysqltool"$DS`
)


