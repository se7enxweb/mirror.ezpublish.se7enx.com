#!/usr/bin/env php
<?php
//
// Created on: <19-Mar-2004 09:51:56 amos>
//
// Copyright (C) 1999-2005 eZ systems as. All rights reserved.
//
// This source file is part of the eZ publish (tm) Open Source Content
// Management System.
//
// This file may be distributed and/or modified under the terms of the
// "GNU General Public License" version 2 as published by the Free
// Software Foundation and appearing in the file LICENSE included in
// the packaging of this file.
//
// Licencees holding a valid "eZ publish professional licence" version 2
// may use this file in accordance with the "eZ publish professional licence"
// version 2 Agreement provided with the Software.
//
// This file is provided AS IS with NO WARRANTY OF ANY KIND, INCLUDING
// THE WARRANTY OF DESIGN, MERCHANTABILITY AND FITNESS FOR A PARTICULAR
// PURPOSE.
//
// The "eZ publish professional licence" version 2 is available at
// http://ez.no/ez_publish/licences/professional/ and in the file
// PROFESSIONAL_LICENCE included in the packaging of this file.
// For pricing of this licence please contact us via e-mail to licence@ez.no.
// Further contact information is available at http://ez.no/company/contact/.
//
// The "GNU General Public License" (GPL) is available at
// http://www.gnu.org/copyleft/gpl.html.
//
// Contact licence@ez.no if any conditions of this licencing isn't clear to
// you.
//

include_once( 'lib/ezutils/classes/ezcli.php' );
include_once( 'kernel/classes/ezscript.php' );

$cli =& eZCLI::instance();
$script =& eZScript::instance( array( 'description' => ( "eZ publish SQL diff\n\n" .
                                                         "Displays differences between two database schemas,\n" .
                                                         "and sets exit code based whether there is a difference or not\n" .
                                                         "\n" .
                                                         "ezsqldiff.php --type mysql --user=root stable32 stable33" ),
                                      'use-session' => false,
                                      'use-modules' => true,
                                      'use-extensions' => true ) );

$script->startup();

$options = $script->getOptions( "[source-type:][source-host:][source-user:][source-password;][source-socket:]" .
                                "[match-type:][match-host:][match-user:][match-password;][match-socket:]" .
                                "[t:|type:][host:][u:|user:][p:|password;][socket:]" .
                                "[lint-check]" .
                                "[reverse][check-only]",
                                "[source][match]",
                                array( 'source-type' => ( "Which database type to use for source, can be one of:\n" .
                                                          "mysql, postgresql" ),
                                       'source-host' => "Connect to host source database",
                                       'source-user' => "User for login to source database",
                                       'source-password' => "Password to use when connecting to source database",
                                       'source-socket' => 'Socket to connect to source database (only for MySQL)',
                                       'match-type' => ( "Which database type to use for match, can be one of:\n" .
                                                         "mysql, postgresql" ),
                                       'match-host' => "Connect to host match database",
                                       'match-user' => "User for login to match database",
                                       'match-password' => "Password to use when connecting to match database",
                                       'match-socket' => 'Socket to connect to match database (only for MySQL)',
                                       'type' => ( "Which database type to use for match and source, can be one of:\n" .
                                                   "mysql, postgresql" ),
                                       'host' => "Connect to host match and source database",
                                       'user' => "User for login to match and source database",
                                       'password' => "Password to use when connecting to match and source database",
                                       'socket' => 'Socket to connect to match and source database (only for MySQL)',
                                       'reverse' => "Reverse the differences",
                                       'check-only' => "Don't show SQLs for the differences, just set exit code and return"
                                       ) );
$script->initialize();

if ( count( $options['arguments'] ) < 1 )
{
    $cli->error( "Missing source database" );
    $script->shutdown( 1 );
}

if ( count( $options['arguments'] ) < 2 and
     !$options['lint-check'] )
{
    $cli->error( "Missing match database" );
    $script->shutdown( 1 );
}

$sourceType = $options['source-type'] ? $options['source-type'] : $options['type'];
$sourceDBHost = $options['source-host'] ? $options['source-host'] : $options['host'];
$sourceDBUser = $options['source-user'] ? $options['source-user'] : $options['user'];
$sourceDBPassword = $options['source-password'] ? $options['source-password'] : $options['password'];
$sourceDBSocket = $options['source-socket'] ? $options['source-socket'] : $options['socket'];
$sourceDB = $options['arguments'][0];

if( !is_string( $sourceDBPassword ) )
    $sourceDBPassword = '';

$matchType = $options['match-type'] ? $options['match-type'] : $options['type'];
$matchDBHost = $options['match-host'] ? $options['match-host'] : $options['host'];
$matchDBUser = $options['match-user'] ? $options['match-user'] : $options['user'];
$matchDBPassword = $options['match-password'] ? $options['match-password'] : $options['password'];
$matchDBSocket = $options['match-socket'] ? $options['match-socket'] : $options['socket'];
$matchDB = count( $options['arguments'] ) >= 2 ? $options['arguments'][1] : '';

if ( !is_string( $matchDBPassword ) )
    $matchDBPassword = '';

if ( strlen( trim( $sourceType ) ) == 0 )
{
    $cli->error( "No source type chosen" );
    $script->shutdown( 1 );
}
if ( strlen( trim( $matchType ) ) == 0 )
{
    if ( !$options['lint-check'] )
    {
        $cli->error( "No match type chosen" );
        $script->shutdown( 1 );
    }
}

$ini =& eZINI::instance();

function &loadDatabaseSchema( $type, $host, $user, $password, $socket, $db, &$cli )
{
    if ( file_exists( $db ) and is_file( $db ) )
    {
        include_once( 'lib/ezdbschema/classes/ezdbschema.php' );
        return eZDBSchema::instance( array( 'type' => $type,
                                            'schema' => eZDBSchema::read( $db ) ) );
    }
    else
    {
        include_once( 'lib/ezdbschema/classes/ezdbschema.php' );
        include_once( 'lib/ezdb/classes/ezdb.php' );
        $parameters = array( 'use_defaults' => false,
                             'server' => $host,
                             'user' => $user,
                             'password' => $password,
                             'database' => $db );
        if ( $socket )
            $parameters['socket'] = $socket;
        $dbInstance =& eZDB::instance( 'ez' . $type,
                                       $parameters,
                                       true );

        if ( !is_object( $dbInstance ) )
        {
            $cli->error( 'Could not initialize database:' );
            $cli->error( '* No database handler was found for $type' );
            return false;
        }
        if ( !$dbInstance->isConnected() )
        {
            $cli->error( "Could not initialize database:" );
            $msg = "* Tried database '$db'";
            if ( strlen( $host ) > 0 )
            {
                $msg .= " at host '$host'";
            }
            else
            {
                $msg .= " locally";
            }
            if ( strlen( $user ) > 0 )
            {
                $msg .= " with user '$user'";
            }
            if ( strlen( $password ) > 0 )
                $msg .= " and with a password";
            $cli->error( $msg );

            // Fetch the database error message if there is one
            // It will give more feedback to the user what is wrong
            $msg = $dbInstance->errorMessage();
            if ( $msg )
            {
                $number = $dbInstance->errorNumber();
                if ( $number > 0 )
                    $msg .= '(' . $number . ')';
                $cli->error( '* ' . $msg );
            }
            return false;
        }

        return eZDBSchema::instance( $dbInstance );
    }
}

function &loadLintSchema( &$dbSchema, &$cli )
{
    include_once( 'lib/ezdbschema/classes/ezlintschema.php' );
    return new eZLintSchema( false, $dbSchema );
}

$sourceSchema = loadDatabaseSchema( $sourceType, $sourceDBHost, $sourceDBUser, $sourceDBPassword, $sourceDBSocket, $sourceDB, $cli );
if ( !$sourceSchema )
{
    $cli->error( "Failed to load schema from source database" );
    $script->shutdown( 1 );
}

if ( $options['lint-check'] )
{
    $matchType = $sourceType;
    $matchSchema = $sourceSchema;
    unset( $sourceSchema );
    $sourceSchema = loadLintSchema( $matchSchema, $cli );
}
else
{
    $matchSchema = loadDatabaseSchema( $matchType, $matchDBHost, $matchDBUser, $matchDBPassword, $matchDBSocket, $matchDB, $cli );
    if ( !$matchSchema )
    {
        $cli->error( "Failed to load schema from match database" );
        $script->shutdown( 1 );
    }
}

include_once( 'lib/ezdbschema/classes/ezdbschemachecker.php' );

if ( $options['reverse'] )
{
    $differences = eZDbSchemaChecker::diff( $sourceSchema->schema(), $matchSchema->schema(), $sourceType, $matchType );
    if ( !$options['check-only'] )
    {
        $cli->output( "-- Difference in SQL commands for " . $sourceSchema->schemaName() );
        $sql = $sourceSchema->generateUpgradeFile( $differences );
        $cli->output( $sql );
    }
}
else
{
    $differences = eZDbSchemaChecker::diff( $matchSchema->schema(), $sourceSchema->schema(), $matchType, $sourceType );
    if ( !$options['check-only'] )
    {
        $cli->output( "-- Difference in SQL commands from " . $sourceSchema->schemaName() . " to " . $matchSchema->schemaName() );
        $sql = $matchSchema->generateUpgradeFile( $differences );
        $cli->output( $sql );
    }
}

if ( count( $differences ) > 0 )
    $script->setExitCode( 1 );

$script->shutdown();

?>
