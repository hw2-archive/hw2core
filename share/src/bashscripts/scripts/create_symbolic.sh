#!/bin/bash
function init() {
    # change the directory to parent folder ( platform root )
    local D=$(dirname "$hw2core_path")
    echo "working on: $hw2core_path"
    cd "$D"
    #readlink -f $HW2PATH

    #merge custom array if exists
    FILTERS+=(${CUSTOM_FILTERS[@]})
    HW2_FILTERS+=(${HW2_CUSTOM_FILTERS[@]})
    #merge custom array if exists
    INCLUDES+=(${CUSTOM_INCLUDES[@]})
    HW2_INCLUDES+=(${Hw2_CUSTOM_INCLUDES[@]} )

    HW2_LINKS+=(${HW2_CUSTOM_LINKS[@]})
    LINKS+=(${CUSTOM_LINKS[@]})

    # change dir to hw2
    local OLD_D=$D
    D=$D$DS"hw2"
    cd "$D"


    #run
    processDir "" ${HW2_CONF['CS_HW2PATH']} HW2_FILTERS
    processLinks "" ${HW2_CONF['CS_HW2PATH']} HW2_INCLUDES
    extraLinks HW2_LINKS


    # return back to root directory after finished with $HW2PATH
    D=$OLD_D
    cd "$D"


    if (($ISTRUNK != 1 )); then # only if it's not on trunk path
        #run
        processDir "" ${HW2_CONF['CS_TRUNKPATH']} FILTERS
        processLinks "" ${HW2_CONF['CS_TRUNKPATH']} INCLUDES
    fi;

    extraLinks LINKS


    read -p "completed"
}

init
