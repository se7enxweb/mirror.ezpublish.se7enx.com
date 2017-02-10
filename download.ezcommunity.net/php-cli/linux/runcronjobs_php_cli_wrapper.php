// #!c:\php\php.exe -q
/*
 Author: kracker
 Date: 05/09/2005
 License: GNU GPL (v2 or later)
 Description: Windows php script wrapper to call eZ publish : runcronjobs

 This (albeit little script) is expressly provided only
 under the terms of the GNU GPL. Just so we are clear :)
*/

system("cd /web/the-path-to-ez/; c:\php\cli\php.exe /web/the-path-to-ez/runcronjobs.php");

//exit();