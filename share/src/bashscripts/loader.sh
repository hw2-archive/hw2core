#!/bin/bash

#this file must be present also for legacy scope

DS="/"

#for current directory (because sym-link )
#abspath="$(cd "${0%/*}" 2>/dev/null; echo "$PWD"/"${0##*/}")"
#abspath=$(cd ${0%/*} && echo $PWD/${0##*/})

# to get the path only - not the script name - add
#path_only=`dirname "$abspath"`

#for parent directory (because include)
#path_only=${PWD}

#the path of the script, no matter where is called from

loader_path="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
hw2_config=$hw2core_path$DS"boot.sh" # name of config file

#all symlink resolved
#index_path=DIR="$( cd -P "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

#create the array of inclusion , with order of priority [to fix]
#TODO include directory ( also recursive )
declare -A a_inc
# auto includes
a_inc[shared_def]=$loader_path$DS"includes"$DS"shared_defines.sh"
a_inc[helpers]=$loader_path$DS"includes"$DS"helpers.sh"
a_inc[symlink]=$loader_path$DS"includes"$DS"symlink.sh"
a_inc[ini_parser]=$loader_path$DS"includes"$DS"ini_parser.sh"
declare -A m_inc
# manual includes
m_inc[create_git]=$loader_path$DS"scripts"$DS"create_git.sh"
m_inc[dbsync]=$loader_path$DS"scripts"$DS"dbsync.sh"
m_inc[create_symbolic]=$loader_path$DS"scripts"$DS"create_symbolic.sh"
m_inc[hw2j_backup]=$loader_path$DS"scripts"$DS"hw2j_backup.sh"
m_inc[mysql_tools]=$loader_path$DS"scripts"$DS"mysql_tools.sh"
#m_inc[json_parser]=$loader_path$DS"includes"$DS"json_parser.sh"
m_inc[create_branch]=$loader_path$DS"scripts"$DS"create_branch.sh"
m_inc[remote_conn]=$loader_path$DS"scripts"$DS"remote_connection.sh"

function includeAll() {
    for i in "${!a_inc[@]}"
    do
      echo "including: $i"
      source "${a_inc[$i]}"
    done
}

function runScript() {
    case $LOADER_SCRIPT in
    -1)
      # -1 use this file to load config
      ;;
    1)
      echo "create_links"
      source "${m_inc["create_symbolic"]}"
      ;;
    2)
      echo "backup folder"
      local sc=${HW2_CONF['PLATFORM']}"_backup"
      source "${m_inc[$sc]}"
      ;;
    3)
      echo "mysql_tools"
      source "${m_inc["mysql_tools"]}"
      ;;
    4)
      echo "dbsync"
      source "${m_inc["dbsync"]}"
      ;;
    #5)
    #  echo "create_git"
    #  source "${m_inc["create_git"]}"
    #  ;;
    6)
      echo "create_branch"
      source "${m_inc["create_branch"]}"
      ;;
    7)
      echo "remote_conn"
      source "${m_inc["remote_conn"]}"
      ;;
    *)
      echo "no option selected"
      ;;
    esac

}

includeAll

declare -A HW2_CONF

load_conf #load passed config from php

#init conf
source "$hw2core_path"$DS"share"$DS"conf"$DS"init_conf.sh"
#then local
source "${HW2_CONF['HW2PATH_LOCAL_CONF']}conf.sh"
#then by platform
source "$hw2core_path"$DS"share"$DS"conf"$DS${HW2_CONF['PLATFORM']}"_conf.sh"
#then common 
source "$hw2core_path"$DS"share"$DS"conf"$DS"common_conf.sh"


runScript