#!/bin/bash

SL_RM_MSG="Press any key to remove "

#check if FILE/DIR is present inside a FILTERS array
# par 1: FILE/DIR , par 2: FILTERS
# return: 0 if not filtered, 1 if it's exactly path of filter entry, 2 if it's part of the path of a filter
function checkExclude() { 
    # FOLDERS
    local CFILE="$1"

    #array arguments begin
    local OLDIFS=$IFS
    IFS=''
    local array_string="$2[*]"
    local FILTERS=(${!array_string})
    IFS=$OLDIFS
    #array arguments end

    #remove link mode
    echo "filters: ${FILTERS[@]}"
    if [ "${FILTERS[0]}" == "*.*" ]; then
        #exact filter file/dir 
        echo "rem_link_mode"
        return 1
    fi;

    # get length of an array
    tLen=${#FILTERS[@]}
    for (( k=0; k<${tLen}; k++ ));
    do
      local FILTER="${FILTERS[$k]}"

      if [ ! -n "$FILTER" ]; then 
        echo "empty filter n."$k
        continue
      fi;

      if [ ! -e "$FILTER" ]; then
        echo "file/dir $FILTER doesn't exists, can't filter"
      fi;

      # we should check the path without the filename
      if [ -f "$FILTER" ]; then
        FILTER=$(dirname $FILTER)
      fi;
      
      local len=${#CFILE}
      echo "check filter: $FILTER file: $CFILE" 
      if [ "$FILTER" == "$CFILE" ]; then
        #exact filter file/dir 
        echo "full filter $FILTER found"
        return 1
      elif [ "${FILTER:0:$len}" == "$CFILE" ]; then
        #included in filter path
        echo "part of $FILTER filter  found"
        return 2
      fi;
    done

    return 0;
}

#remove all files/folder of a given PATH that are not present in SRCPATH
# par 1: PATH, par 2: SRCPATH, par 3: FILTERS
function folderRefactor() {
    local MPATH=$1
    local SRCPATH=$2    

    # save and change IFS
    local OLDIFS=$IFS
    IFS=''
    local array_string="$3[*]"
    local FILTERS=(${!array_string})
    
    local IGNORE=(${FILTERS[@]} ${EXIGNORE[@]})
    local NOTPATH=""
    for f in "${IGNORE[@]}"
    do
        NOTPATH+=" -not -path \".$DS$MPATH$f\""
    done

    # Resetting IFS to default
    IFS=$OLDIFS

    # save and change IFS
    OLDIFS=$IFS
    IFS=$'\n'
    # read all file name into an array
    FCMD='find -L ".'$DS$MPATH'" -maxdepth 1 '$NOTPATH' -printf "%f\n"'
    echo "$FCMD"
    local MLIST=($(eval $FCMD))
    # Resetting IFS to default
    IFS=$OLDIFS


    local MLEN=${#MLIST[@]}
    local y=1
    for (( y=1; y<${MLEN}; y++ ));
    do
        local MORIG="$SRCPATH$MPATH${MLIST[$y]}"
        local MDEST="$MPATH${MLIST[$y]}"
			
        echo "refactoring orig: $MORIG dest: $MDEST"
        if [ ! -e "$MORIG" ]; then
           if [ -d "$MDEST" ]; then
                if [ ! -h "$MDEST" ]; then               
                        read -p "$SL_RM_MSG $MDEST "    # ask before delete something local                    
                fi;
                echo "removing dir $MDEST"
                rm -R "$MDEST"
           else
                if [ ! -h "$MDEST" ]; then               
                        read -p "$SL_RM_MSG $MDEST "    # ask before delete something local                    
                fi;
                echo "removing file $MDEST"
                rm "$MDEST"
           fi;
        fi;
        
    done
}


# $1 ISDIR , $2 FILE, $3 TPATH , $4 1: force hard , 2: force symbolic, not set: normal way
function createLink() {
  
    if [ ! -z $4 ] && (($4 == 1)); then
        local COMMAND=$COMMAND_HARD
    elif [ ! -z $4 ] && (($4 == 2)); then
        local COMMAND=$COMMAND_SYM
    elif (($1 == 1)); then
        local COMMAND=$COMMAND_DIR;
    else
        local COMMAND=$COMMAND_FILE;
    fi;
    
  local LINK=""
  [[ $2 != $DS* ]] && LINK+=".$DS" # check prefix "/" , if not absolute, add relative prefix
  LINK+="$2"
  local TARGET="$3"

  echo "link: $LINK target: $TARGET"

  eval "$COMMAND \"$TARGET\" \"$LINK\""

}

# this function count directories presents in path
#par 1: path 
function cntParDir() {
    local N=`echo $1 | grep -o $DS | wc -l | sed s/\ //g`
    local S=""
    for (( j=0; j<$N; j++ ));
    do
            S=${S}".."$DS
    done
    echo $S
}

#par 1: links path , par 2: Source path, par 3 FILTERS, par 4 force link
function processDir() { 
    echo "processDir.."  
    #VARIABLE NAME CAN'T BE "PATH" BECAUSE IT IS A SYSTEM VAR 
    local FPATH=$1
    local SRCPATH=$2

    #array arguments begin
    local OLDIFS=$IFS
    IFS=''
    # Create a string containing "FILTERS[*]"
    local array_string="$3[*]"
    # assign value to ${FILTERS[*]} using indirect variable reference
    local FILTERS=(${!array_string})
    # Resetting IFS to default
    IFS=$OLDIFS

    #array arguments end
    echo "filters: ${FILTERS[@]}"
    echo "force: "$4
    if [ -z "$4" ]; then
        local force=0
    else
        local force=$4
    fi;
     

    echo "search on: "$SRCPATH$FPATH
    local IGNORE=(${FILTERS[@]} ${EXIGNORE[@]})
    local NOTPATH=""
    for f in "${IGNORE[@]}"
    do
        NOTPATH+=" -not -path \"$SRCPATH$FPATH$f\""
    done

    # save and change IFS
    OLDIFS=$IFS
    local IFS=$'\n'
    # read all file name into an array
    FCMD='find  -L "'$SRCPATH$FPATH'" -maxdepth 1 '$NOTPATH' -printf "%f\n"'
    echo "$FCMD"
    local LIST=($(eval $FCMD))
    echo ${LIST[@]}
    # restore it
    IFS=$OLDIFS

    local lLEN=${#LIST[@]}
    echo "refactoring files for: $FPATH"
    folderRefactor "$FPATH" "$SRCPATH" FILTERS
    
    local i=1
    for (( i=1; i<${lLEN}; i++ ));
    do
        local FILE="${LIST[$i]}"
        echo "check filter for $FPATH$FILE"
        checkExclude "$FPATH$FILE" FILTERS
        local res=$?
        echo "result: $res"
        echo "force: $force"
        local MORIG="$SRCPATH$FPATH$FILE"
        local MDEST="$FPATH$FILE"

        if (($res>=1)); then
            #remove symbolic link if now  it's a filter
            echo "DEST: "$MDEST
            #[ FILE1 -ef FILE2 ]	True if FILE1 and FILE2 refer to the same device and inode numbers.
            if [ -h "$MDEST" ] || [[ -f "$MDEST" && $MDEST -ef $MORIG ]] ; then
            echo "remove symbolic: $MDEST"
                if [ -d "$MDEST" ]; then
                    rm -r "$MDEST"
                else
                    rm "$MDEST"
                fi;
            fi;
        fi;

        if (($res>=1 && $force==0)) ; then

            if (($res == 1)); then
                
                if [ -e "$MORIG" ]; then
                    if [ ! -e "$MDEST" ]; then 
                    echo "copying $MORIG in $MDEST"
                        #fix: after copy a folder do not link files inside 
                        cp -r "$MORIG" "$MDEST"
                    fi;
                fi;  
                if [ -f "$MORIG" ]; then
                    echo "force $FPATH$FILE";
                    processDir "$FPATH" "$SRCPATH" FILTERS  1 #forced
                fi;
            fi;

            if (($res==2)); then
                if [ -d "$MORIG" ]; then
                    if [ ! -e "$FPATH$FILE" ]; then
                        mkdir "$FPATH$FILE"
                    fi;
                fi;

                if [ -d "$FPATH$FILE" ]; then
                    #recursively process directories
                    processDir "$FPATH$FILE$DS" "$SRCPATH" FILTERS
                fi;
            fi;
        fi;
        
        if (($res==0 || (($force==1  && $res==0)) )); then
        #create the link  
               echo "check $MDEST type"
               if [ -d "$MORIG" ]; then
                    local RPATH=`cntParDir "${MDEST%/}"` # remove latest slash from path
                    echo "creating dir link for $MDEST"
                    if [[ -d "$MDEST" && ! -h "$MDEST" ]]; then               
                        read -p "$SL_RM_MSG  $MDEST"   # ask before delete something local                     
                    fi;
                    rm -R "$MDEST"
                    createLink 1 "$MDEST" "$RPATH$SRCPATH$MDEST"
               elif [ -e "$MORIG" ]; then
                    # calculate it without the filename
                    # ( unix doesn't need relative path when symlink files, maybe windows can )
                    #local RPATH=`cntParDir $(dirname "$MDEST")` 
                    echo "creating file link for $MDEST"
                    if [[ -e "$MDEST" && ! -h "$MDEST" ]]; then               
                        read -p "$SL_RM_MSG $MDEST "    # ask before delete something local                    
                    fi;
                    rm "$MDEST"
                    createLink 0 "$MDEST" "$RPATH$SRCPATH$MDEST"
               fi;
        fi;
    done
}

#includes processing
function processLinks() {
    echo "processLinks.."  
    local FPATH=$1
    local SRCPATH=$2
    #array arguments begin
    local OLDIFS=$IFS
    IFS=''
    local array_string="$3[*]"
    local INCLUDES=(${!array_string})
    IFS=$OLDIFS
    #array arguments end
    # get length of an array
    local sLen=${#INCLUDES[@]}
    # FOLDERS
    for (( i=0; i<${sLen}; i++ ));
    do
        local FNAME=`hw2_stget "${INCLUDES[$i]}" 1`
        local FORCE_HARD=`hw2_stget "${INCLUDES[$i]}" 2`
        echo "FORCE HARD: $FORCE_HARD";
        if [ ! -n "$FNAME" ]; then 
            echo "empty "$i
            continue
        fi;

        echo "check $FNAME type"
        
        if [ -d "$SRCPATH$FNAME" ]; then
            local RPATH=`cntParDir "${FNAME%/}"` # remove latest slash from path
            echo "creating dir link for $FNAME"
            if [[ -d "$MDEST" && ! -h "$MDEST" ]]; then               
                read -p "$SL_RM_MSG  $FNAME" # ask before delete something local                       
            fi;
            rm -R ".$DS$FNAME"
            createLink 1 "$FNAME" "$RPATH$SRCPATH$FNAME" $FORCE_HARD
        elif [ -e "$SRCPATH$FNAME" ]; then
            local RPATH=`cntParDir $(dirname "$FNAME")`
            echo "creating file link for $FNAME"
            if [[ -e "$MDEST" && ! -h "$MDEST" ]]; then               
                read -p "$SL_RM_MSG  $FNAME"  # ask before delete something local                     
            fi;
            rm ".$DS$FNAME"
            createLink 0 "$FNAME" "$RPATH$SRCPATH$FNAME" $FORCE_HARD
        fi;
    done
}

# $1 links
function extraLinks() {
    #array arguments begin
    local OLDIFS=$IFS
    IFS=''
    local array_string="$1[*]"
    local LINKS=(${!array_string})
    IFS=$OLDIFS

    for e in "${LINKS[@]}"
    do
        local isDir=`hw2_stget $e 1`
        local MDEST=`hw2_stget $e 2`
        local MTARGET=`hw2_stget $e 3`
        if [[ -e "$MTARGET" ]]; then  
            if [[ -e "$MDEST" ]]; then
                if [[ ! -L "$MDEST" && ! "$MDEST" -ef "$MTARGET" ]]; then              
                    read -p "$SL_RM_MSG  $MDEST"   # ask before delete something local                     
                fi;

                if [ $isDir -eq "1" ]; then
                    rm -R $MDEST
                else
                    rm $MDEST
                fi;
            fi;
            createLink $isDir $MDEST $MTARGET `hw2_stget $e 4`
        fi;
    done
}


