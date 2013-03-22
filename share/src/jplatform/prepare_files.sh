#!/bin/bash

path="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

ignores=" -not -path ./defines.php" 
#ignores+=" -not -path ./loader.php"

regexp_p="/.*"
regexp_s=".*/!"

fileext='*.php'

function find_replace() {
    src=$1
    echo "replacing: "$src
    replace="my_"$src
    echo "with: "$replace
    find . $ignores -name "$fileext" -print0 | xargs -0 sed -i -e "${regexp_p}${replace}_d[ \t]*('${regexp_s} s/${src}[ \t]*('/${replace}_d('/g"
    find . $ignores -name "$fileext" -print0 | xargs -0 sed -i -e "${regexp_p}${replace}_d[ \t]*(\"${regexp_s} s/${src}[ \t]*(\"/${replace}_d(\"/g"
    find . $ignores -name "$fileext" -print0 | xargs -0 sed -i -e "${regexp_p}${replace}[ \t]*(${regexp_s} s/${src}[ \t]*(/${replace}(/g"
}

# test this function instead above, should fix the case when we found Array or other values
function find_replace_to_test() {
    src=$1
    echo "replacing: "$src
    replace="my_"$src
    echo "with: "$replace
    find . $ignores -name "$fileext" -print0 | xargs -0 sed -i -e "${regexp_p}${replace}[ \t]*([ \t]*\$${regexp_s} s/${src}[ \t]*([ \t]*\$/${replace}(/g"
    find . $ignores -name "$fileext" -print0 | xargs -0 sed -i -e "${regexp_p}${replace}_d[ \t]*(${regexp_s} s/${src}[ \t]*(/${replace}_d('/g"
}

function fix_globals() {
    src=$1
    echo "replacing: "$src
    replace="\\\\"$src

    echo "with: "$replace

    find . $ignores -name "$fileext" -print0 | xargs -0 sed -i -e "${regexp_p}${replace}${regexp_s} s/${src}/${replace}/g"
}


# set namespace for first line of all files
echo "setting namespace at beginning of file"
find . $ignores -name "$fileext" -print0 | xargs -0 sed -i -e '1{/^<?php namespace.*/! s/^<?php/<?php namespace Hwj;/g}'
echo "==============="
echo "replacing defined.."
find . $ignores -name "$fileext" -print0 | xargs -0 sed -i -e "${regexp_p}my_defined[ \t]*(${regexp_s} s/defined[ \t]*(/my_defined(/g"
echo "==============="
find_replace "class_exists"
echo "==============="
find_replace "is_subclass_of"
echo "==============="
find_replace "define"
echo "==============="
find_replace "get_class_methods"
echo "==============="
find_replace "is_callable"
echo "==============="

fix_globals "RecursiveIteratorIterator"
echo "==============="

fix_globals "DirectoryIterator"
echo "==============="
echo "fix RecursiveDirectoryIterator"
find . $ignores -name "$fileext" -print0 | xargs -0 sed -i -e "s/\\\\\?Recursive\\\\DirectoryIterator/\\\\RecursiveDirectoryIterator/g"
echo "==============="


fix_globals "SimpleXMLElement"
echo "==============="


fix_globals "Exception"
echo "==============="
echo "fix RuntimeException"
find . $ignores -name "$fileext" -print0 | xargs -0 sed -i -e "s/\\\\\?Runtime\\\\Exception/\\\\RuntimeException/g"
echo "==============="
echo "fix InvalidArgumentException"
find . $ignores -name "$fileext" -print0 | xargs -0 sed -i -e "s/\\\\\?InvalidArgument\\\\Exception/\\\\InvalidArgumentException/g"
echo "==============="
echo "fix UnexpectedValueException"
find . $ignores -name "$fileext" -print0 | xargs -0 sed -i -e "s/\\\\\?UnexpectedValue\\\\Exception/\\\\UnexpectedValueException/g"
echo "==============="
echo "fix JException"
find . $ignores -name "$fileext" -print0 | xargs -0 sed -i -e "s/\\\\\?J\\\\Exception/\\\\JException/g"
echo "==============="

fix_globals "Serializable"
echo "==============="
echo "fix JsonSerializable"
find . $ignores -name "$fileext" -print0 | xargs -0 sed -i -e "s/\\\\\?Json\\\\Serializable/\\\\JsonSerializable/g"
echo "==============="

fix_globals "stdClass"
echo "==============="

fix_globals "IteratorAggregate"
echo "==============="

#[TODO] find a way to fix Countable class since we find this word also in some function names
# workaround: replace it manually via IDE

fix_globals "DateTime" # it also fix DateTimeZone
echo "==============="


read -p "press any key to close.."
