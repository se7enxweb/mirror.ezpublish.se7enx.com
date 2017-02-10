#!/bin/bash

EXT='.log'; 
TIME_STAMP=`date +%F_%H%M%S`; 
TIME_STAMP="-$TIME_STAMP"; 
TIME_STAMP="$TIME_STAMP$EXT"; 

YTIME_STAMP=`date +%F_%H%M%S`; 
YTIME_STAMP="-$YTIME_STAMP"; 
YTIME_STAMP="$YTIME_STAMP$EXT"; 

# Archive Current Log
 cp -a /home/web/download.ezpublishhosting.com/html/ezirc/logs/ezpublish.log /home/web/download.ezpublishhosting.com/html/ezirc/logs/archive/ezpublish$TIME_STAMP

# Archive Yesterday's Log
 cp -a /home/web/download.ezpublishhosting.com/html/ezirc/logs/ezpublish-yesterday.log /home/web/download.ezpublishhosting.com/html/ezirc/logs/archive/y-ezpublish$YTIME_STAMP

