#!/bin/bash
cd "$(dirname "$0")"
FILES=../storage/*
for f in $FILES
do

#   if (( (basename $f) > 0));
#   if [ "${$(basename $f)//[0-9]}" = "" ]; 
    if [[ $(basename $f) =~ ^[-+]?[0-9]+$ ]]
    then
        php ./friends.php friend $(basename $f)
    fi

    # take action on each file. $filename store current file name
    echo $(basename $f)
done
