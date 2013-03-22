#!/bin/bash4

#
# CONFIG CREATOR 
#

isVarSet(){
 local v="$1"
 [[ ! ${!v} && ${!v-unset} ]] && echo 0 || echo 1
}

#allow to set a conf only if is not defined yet
#par1: key, par2: var, par3: avoid_var_creation (1/0), par4: force 
function hw2_setConf() {
    if [[ -z ${HW2_CONF[$1]} || $4 == "1" ]]; then
        HW2_CONF[$1]="$2"
        #HW2_CONF=([$1]=$2)
    fi;

    # [deprecated]
    # it will define a conf variable using var definition
    # instead array fill 
    if [[ $3 != "1" ]]; then
        if [[ $(isVarSet `eval "echo $1"`) == 0 || $4 == "1" ]]; then
            eval "$1=\"$2\""      
        fi;
    fi;
}


#
# STRUCTURE EMULATOR
#

# par 1 .. x : member of structure
function hw2_struct() {
    local struct;
    for (( i=1; i<=$#; i++ )); do
       eval local arg=\$$i
       struct=$struct$arg;
        if [ "$i" -ne "$#" ]; then
            struct=$struct"|"
        fi;
    done;
    echo $struct # return 
}

# par1: structure , par2: member name ( to implement ) or id
function hw2_stget() {
    echo $1 | awk -F "[|]" ' { print $'$2' } ' #return
}




#
# PATH RESOLVER
#


# Enable the following for debugging purpose
# set -vx
 
#
# Resolves any path (even through link-chains etc. to an absolute path.
#
# Usage:
#   MY_COMMAND="$(resolvePath "${0}")"
#   MY_DIR="$(dirname "${MY_COMMAND}")"
#
function resolvePath() {
  local path="${1}"
 
  firstTry="$(readlink -f "${path}" 2> /dev/null)"
  if [ -n "${firstTry}" ]; then
    echo "${firstTry}"
  else
    echo "$(_pwdResolvePath "${path}")"
  fi
}
 
#
# If readlink is not available on the system the fallback is to use
# pwd -P and the "cd"-approach to resolve a symbolic link.
#
function _pwdResolvePath() {
  local path="${1}"
  local cmd dir link
 
  if [ -d "${path}" ]; then
    cmd=
    dir="${path}"
  else
    cmd="$(basename "${path}")"
    dir="$(dirname "${path}")"
  fi
 
  cd "$dir"
 
  if [ ! -d "${path}" ]; then
    while [ -h "$cmd" ]; do
      link="$(ls -l "$cmd" | cut -d\> -f2 | cut -c2-)"
      cmd="$(basename "$link")"
      dir="$(dirname "$link")"
      cd "$dir"
    done
    cmd="/${cmd}"
  fi
 
  echo "$(pwd -P)${cmd}"
}




#
# FUNCTION TO COPY OR BACKUP FOLDERS
#

# par1: source dir , par2: target dir , par3: time_suffix (1 / 0 default), par4: remove_links (1 / 0 default)
function copy_dir() {
    
    SOURCE_DIR=$1$DS"*" # first parameter
    TARGET_DIR=$2       # second parameter

    # time_suffix
    if [ $3 -eq "1" ]; then
        TIME=`date +"%T_%m-%d-%y"`
        TARGET_DIR+="_"$TIME
    fi;

    OPTIONS=" --recursive --preserve=mode,timestamps "
    # remove_links
    if [ $4 -eq "1" ]; then
        OPTIONS+=" --dereference"
    fi;

    echo "SOURCE_DIR: $SOURCE_DIR"
    echo "copying in: $TARGET_DIR"

    read -p "starting to copying..press any key to continue"

    mkdir $TARGET_DIR
    eval "cp $OPTIONS $SOURCE_DIR $TARGET_DIR"
    echo "end"
}



