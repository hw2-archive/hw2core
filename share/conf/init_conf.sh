#!/bin/bash

#!!!UNUSED!!! , KEEP IT ONLY FOR INI PARSING ESAMPLE

hw2init_conf_path="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

hw2_setConf "HW2PATH_LOCAL_CONF" "$hw2core_path"$DS"local"$DS"conf"$DS
hw2_setConf "HW2PATH_PLATFORM" "$(resolvePath "$hw2core_path$DS".."$DS")"

#
# PATHS for symbolic links
#
hw2_setConf "CS_DEVPATH" ".."$DS".."$DS".."$DS".."$DS
hw2_setConf "CS_HW2PATH" ${HW2_CONF['CS_DEVPATH']}"hw2_core"$DS

