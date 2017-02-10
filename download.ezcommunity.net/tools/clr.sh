#!/bin/bash

################################################################################
# Argument #1 : 0 = silent or 1 = verbose, default 0:  (usage: ./buildsvndemo.sh 0 )

##########################
# Clear Terminal Screan

clear;


if [ $1 ]; then
   # Assign method based on argument
   if [ $1 == 'ini' ]; then
      OPTIONS_TYPE='ini';
   else
      OPTIONS_TYPE='tpl';
   fi

   # Assign method based on argument
   if [ $1 == 'tpl' ]; then
      OPTIONS_TYPE='tpl';
   else
      OPTIONS_TYPE='ini';
   fi

   # Assign method based on argument
   if [ $1 == 'all' ]; then
      OPTIONS_TYPE='all';
   else
      OPTIONS_TYPE='tpl';
   fi

   if [ $OPTIONS_TYPE -eq 1 ]; then
      echo;
      echo "Optional Variable Passed (Clear :): $OPTIONS_TYPE";
   fi
else
   # Default VERBOSITY IS 0, Silent
   OPTIONS_TYPE="all";
fi


##########################
# type="(tpl|ini|all)"

type="$OPTIONS_TYPE"
options="--clear-$type"


##########################
# Clear Cache

./bin/shell/clearcache.sh $options


##########################
# Clear Terminal Screan

clear;


exit
