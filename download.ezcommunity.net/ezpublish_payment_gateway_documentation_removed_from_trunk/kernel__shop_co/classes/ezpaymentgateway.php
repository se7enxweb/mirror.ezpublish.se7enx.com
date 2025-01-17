<?php
//
// Definition of eZPaymentGateway class
//
// Created on: <18-May-2004 14:18:58 dl>
//
// Copyright (C) 1999-2002 eZ systems as. All rights reserved.
//
// This source file is part of the eZ publish (tm) Open Source Content
// Management System.
//
// This file may be distributed and/or modified under the terms of the
// "GNU General Public License" version 2 as published by the Free
// Software Foundation and appearing in the file LICENSE.GPL included in
// the packaging of this file.
//
// Licencees holding valid "eZ publish professional licences" may use this
// file in accordance with the "eZ publish professional licence" Agreement
// provided with the Software.
//
// This file is provided AS IS with NO WARRANTY OF ANY KIND, INCLUDING
// THE WARRANTY OF DESIGN, MERCHANTABILITY AND FITNESS FOR A PARTICULAR
// PURPOSE.
//
// The "eZ publish professional licence" is available at
// http://ez.no/home/licences/professional/. For pricing of this licence
// please contact us via e-mail to licence@ez.no. Further contact
// information is available at http://ez.no/home/contact/.
//
// The "GNU General Public License" (GPL) is available at
// http://www.gnu.org/copyleft/gpl.html.
//
// Contact licence@ez.no if any conditions of this licencing isn't clear to
// you.
//

/*! \file ezpaymentgateway.php
*/

/*!
  \class eZPaymentGateway ezpaymentgateway.php
  \brief Abstract class for all payment gateways.
*/

include_once( 'kernel/classes/workflowtypes/event/ezpaymentgateway/ezpaymentlogger.php' );

class eZPaymentGateway
{
    /*!
        Constructor.
    */
    function eZPaymentGateway()
    {                     
        $this->logger = eZPaymentLogger::CreateForAdd( "var/log/eZPaymentGateway.log" );
    }

    function execute( &$process, &$event )
    {
        $this->logger->writeTimedString( 'You must override this function.', 'execute' );
        return EZ_WORKFLOW_TYPE_STATUS_REJECTED;
    }

    function needCleanup()
    {
        return false;
    }
    
    function cleanup( &$process, &$event )
    {
    }

    /*!
        Creates short description of order. Usually this string is
        passed to payment site as describtion of payment.
    */
    function &createShortDescription( &$order, $maxDescLen )
    {
        //__DEBUG__
            $this->logger->writeTimedString("createShortDescription");
        //___end____
            
        $descText       = '';
        $productItems   = $order->productItems();

        foreach( $productItems as $item )
        {
            $descText .= $item['object_name'] . ',';
        }
        $descText   = rtrim( $descText, "," );
        
        $descLen    = strlen( $descText );
        if( ($maxDescLen > 0) && ($descLen > $maxDescLen) )
        {
            $descText = substr($descText, 0, $maxDescLen - 3) ;
            $descText .= '...';
        }

        //__DEBUG__
            $this->logger->writeTimedString("descText=$descText");
        //___end____

        return $descText;
    }

    var $logger;
}
?>