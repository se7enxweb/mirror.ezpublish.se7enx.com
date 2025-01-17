<?php
//
// Created on: <01-Nov-2002 13:39:10 amos>
//
// Copyright (C) 1999-2004 eZ systems as. All rights reserved.
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
// http://ez.no/products/licences/professional/. For pricing of this licence
// please contact us via e-mail to licence@ez.no. Further contact
// information is available at http://ez.no/home/contact/.
//
// The "GNU General Public License" (GPL) is available at
// http://www.gnu.org/copyleft/gpl.html.
//
// Contact licence@ez.no if any conditions of this licencing isn't clear to
// you.
//

/*! \file operation_definition.php
*/

$OperationList = array();
$OperationList['confirmorder'] = array( 'name' => 'confirmorder',
                                        'default_call_method' => array( 'include_file' => 'kernel/shop/ezshopoperationcollection.php',
                                                                        'class' => 'eZShopOperationCollection' ),
                                        'parameter_type' => 'standard',
                                        'parameters' => array( array( 'name' => 'order_id',
                                                                      'type' => 'integer',
                                                                      'required' => true ),
                                                               ),
                                        'keys' => array( 'order_id' ),
                                        'body' => array( array( 'type' => 'trigger',
                                                                'name' => 'pre_confirmorder',
                                                                'keys' => array( 'order_id' ) ),
                                                         array( 'type' => 'method',
                                                                'name' => 'fetch-order',
                                                                'frequency' => 'once',
                                                                'method' => 'fetchOrder',
                                                                ) ) );

$OperationList['checkout'] = array( 'name' => 'checkout',
                                    'default_call_method' => array( 'include_file' => 'kernel/shop/ezshopoperationcollection.php',
                                                                    'class' => 'eZShopOperationCollection' ),
                                    'parameter_type' => 'standard',
                                    'parameters' => array( array( 'name' => 'order_id',
                                                                  'type' => 'integer',
                                                                  'required' => true ),
                                                           ),
                                    'keys' => array( 'order_id' ),
                                    'body' => array( array( 'type' => 'trigger',
                                                            'name' => 'pre_checkout',
                                                            'keys' => array( 'order_id' ) ),
                                                     array( 'type' => 'method',
                                                            'name' => 'activate-order',
                                                            'frequency' => 'once',
                                                            'method' => 'activateOrder',
                                                            ),
                                                     array( 'type' => 'method',
                                                            'name' => 'send-order-email',
                                                            'frequency' => 'once',
                                                            'method' => 'sendOrderEmails',
                                                            ),
                                                     array( 'type' => 'trigger',
                                                            'name' => 'post_checkout',
                                                            'keys' => array( 'order_id' ) ),
                                                     ) );
?>
