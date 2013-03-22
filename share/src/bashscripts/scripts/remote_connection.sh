#!/bin/bash

function init() {
    local MPATH=${HW2_CONF['HW2PATH_WORKSPACE_CURRENT']}"hw2_remote"$DS${HW2_CONF['ALIAS']}$DS;
    if [ ! -d $MPATH ]; then
        mkdir $MPATH
    fi;

    if [ -z $R_HOST ]; then
        R_HOST="95.110.161.81"
    fi

    local method=0
    if (($USE_FTP == 0)); then
        echo "select login method:"
        echo "1. sshfs"
        echo "2. ssh shell" 
        read method
    fi

    if (($method == 2)); then
        echo "ssh login..."
        ssh $R_USER@$R_HOST "cd $R_PATH; bash";
    elif grep -qs "$MPATH " /etc/mtab; then #the space is needed to avoid "substring" case
        echo "unmounting.."
        if (($USE_FTP == 0)); then
            fusermount -u $MPATH
        else
            sudo umount $MPATH
        fi; 
    else
        echo "mount..user: $R_USER , pass: $R_PASS, host: $R_HOST, path: $R_PATH"
        if (($USE_FTP == 0)); then
            #umask=0002,transform_symlinks
            echo "$R_PASS" | sshfs $R_USER@$R_HOST:$R_PATH $MPATH -o password_stdin,allow_other$R_OPTIONS;
        else
            curlftpfs -v -o user=$R_USER:$R_PASS,allow_other ftp://$R_HOST/$R_PATH $MPATH
        fi; 
    fi

    read -p "completed"
}

init

