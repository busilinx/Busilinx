<?php 

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 3.4                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2011                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007 and the CiviCRM Licensing Exception.   |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the GNU Affero General Public License for more details.        |
 |                                                                    |
 | You should have received a copy of the GNU Affero General Public   |
 | License and the CiviCRM Licensing Exception along                  |
 | with this program; if not, contact CiviCRM LLC                     |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*/
 
/** 
 * 
 * @package CRM 
 * @copyright CiviCRM LLC (c) 2004-2011 
 * $Id$ 
 * 
 */ 

require_once 'CRM/Core/Payment.php';
require_once 'Google/library/googlecart.php';
require_once 'Google/library/googleitem.php';

class CRM_Core_Payment_Google extends CRM_Core_Payment { 
    /**
     * mode of operation: live or test
     *
     * @var object
     * @static
     */
    static protected $_mode = null;

    /**
     * We only need one instance of this object. So we use the singleton
     * pattern and cache the instance in this variable
     *
     * @var object
     * @static
     */
    static private $_singleton = null;

    /** 
     * Constructor 
     *
     * @param string $mode the mode of operation: live or test
     * 
     * @return void 
     */ 
    function __construct( $mode, &$paymentProcessor ) {
        $this->_mode             = $mode;
        $this->_paymentProcessor = $paymentProcessor;
        $this->_processorName    = ts('Google Checkout');
    }

    /** 
     * singleton function used to manage this object 
     * 
     * @param string $mode the mode of operation: live or test
     *
     * @return object 
     * @static 
     * 
     */ 
    static function &singleton( $mode, &$paymentProcessor ) {
        $processorName = $paymentProcessor['name'];
        if (self::$_singleton[$processorName] === null ) {
            self::$_singleton[$processorName] = new CRM_Core_Payment_Google( $mode, $paymentProcessor );
        }
        return self::$_singleton[$processorName];
    }

    /** 
     * This function checks to see if we have the right config values 
     * 
     * @return string the error message if any 
     * @public 
     */ 
    function checkConfig( ) {
        $config = CRM_Core_Config::singleton( );

        $error = array( );

        if ( empty( $this->_paymentProcessor['user_name'] ) ) {
            $error[] = ts( 'User Name is not set in the Administer CiviCRM &raquo; Payment Processor.' );
        }
        
        if ( empty( $this->_paymentProcessor['password'] ) ) {
            $error[] = ts( 'Password is not set in the Administer CiviCRM &raquo; Payment Processor.' );
        }
        
        if ( ! empty( $error ) ) {
            return implode( '<p>', $error );
        } else {
            return null;
        }
    }

    function doDirectPayment( &$params ) {
        CRM_Core_Error::fatal( ts( 'This function is not implemented' ) );
    }

    /**  
     * Sets appropriate parameters for checking out to google
     *  
     * @param array $params  name value pair of contribution datat
     *  
     * @return void  
     * @access public 
     *  
     */  
    function doTransferCheckout( &$params, $component ) {
        $component = strtolower( $component );
        
        $url = rtrim( $this->_paymentProcessor['url_site'], '/' ) . '/cws/v2/Merchant/' . 
            $this->_paymentProcessor['user_name'] . '/checkout';

        //Create a new shopping cart object
        $merchant_id  = $this->_paymentProcessor['user_name'];   // Merchant ID
        $merchant_key = $this->_paymentProcessor['password'];    // Merchant Key
        $server_type  = ( $this->_mode == 'test' ) ? 'sandbox' : '';
        
        $cart  = new GoogleCart($merchant_id, $merchant_key, $server_type); 
        $item1 = new GoogleItem($params['item_name'],'', 1, $params['amount'], $params['currencyID']);
        $cart->AddItem($item1);

        if ( $component == "event" ) {
            $privateData = "contactID={$params['contactID']},contributionID={$params['contributionID']},contributionTypeID={$params['contributionTypeID']},eventID={$params['eventID']},participantID={$params['participantID']},invoiceID={$params['invoiceID']}";
        } elseif ( $component == "contribute" ) {
            $privateData = "contactID={$params['contactID']},contributionID={$params['contributionID']},contributionTypeID={$params['contributionTypeID']},invoiceID={$params['invoiceID']}";

            $membershipID = CRM_Utils_Array::value( 'membershipID', $params );
            if ( $membershipID ) {
                $privateData .= ",membershipID=$membershipID";
            }

            $relatedContactID = CRM_Utils_Array::value( 'related_contact', $params );
            if ( $relatedContactID ) {
                $privateData .= ",relatedContactID=$relatedContactID";

                $onBehalfDupeAlert = CRM_Utils_Array::value( 'onbehalf_dupe_alert', $params );
                if ( $onBehalfDupeAlert ) {
                    $privateData .= ",onBehalfDupeAlert=$onBehalfDupeAlert";
                }
            }
        }
        
        // Allow further manipulation of the arguments via custom hooks ..
        CRM_Utils_Hook::alterPaymentProcessorParams( $this, $params, $privateData );

        $cart->SetMerchantPrivateData($privateData);
        
        if ( $component == "event" ) {
            $returnURL = CRM_Utils_System::url( 'civicrm/event/register',
                                                "_qf_ThankYou_display=1&qfKey={$params['qfKey']}", 
                                                true, null, false );
        } elseif ( $component == "contribute" ) {
            $returnURL = CRM_Utils_System::url( 'civicrm/contribute/transact',
                                                "_qf_ThankYou_display=1&qfKey={$params['qfKey']}",
                                                true, null, false );
        }

        $cart->SetContinueShoppingUrl( $returnURL );

        $cartVal      = base64_encode($cart->GetXML());
        $signatureVal = base64_encode($cart->CalcHmacSha1($cart->GetXML()));
        
        $googleParams = array('cart'      => $cartVal,
                              'signature' => $signatureVal );
        
        require_once 'HTTP/Request.php';
        $params = array( 'method' => HTTP_REQUEST_METHOD_POST,
                         'allowRedirects' => false );
        $request = new HTTP_Request( $url, $params );
        foreach ( $googleParams as $key => $value ) {
            $request->addPostData($key, $value);
        }

        $result = $request->sendRequest( );

        if ( PEAR::isError( $result ) ) {
            CRM_Core_Error::fatal( $result->getMessage( ) );
        }
        
        if ( $request->getResponseCode( ) != 302 ) {
            CRM_Core_Error::fatal( ts( 'Invalid response code received from Google Checkout: %1', 
                                       array(1 => $request->getResponseCode())) );
        }
        CRM_Utils_System::redirect( $request->getResponseHeader( 'location' ) );
        CRM_Utils_System::civiExit( );
    }

    /**
     * hash_call: Function to perform the API call to PayPal using API signature
     * @paymentProcessor is the array of payment processor settings value.
     * @searchParamsnvpStr is the array of search params.
     * returns an associtive array containing the response from the server.
     */
    function invokeAPI( $paymentProcessor, $searchParams ) {
        $merchantID  = $paymentProcessor['user_name'];
        $merchantKey = $paymentProcessor['password'];
        $siteURL     = rtrim(str_replace('https://', '', $paymentProcessor['url_site']), '/');

        $url = "https://{$merchantID}:{$merchantKey}@{$siteURL}/api/checkout/v2/reports/Merchant/{$merchantID}";
        $xml = self::buildXMLQuery( $searchParams );

        if ( !function_exists('curl_init') ) {
            CRM_Core_Error::fatal("curl functions NOT available.");
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url );
        curl_setopt($ch, CURLOPT_VERBOSE, 1);

        //turning off the server and peer verification(TrustManager Concept).
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);

        //setting the nvpreq as POST FIELD to curl
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        
        //getting response from server
        $xmlResponse = curl_exec( $ch );

        // strip slashes if needed
        if (get_magic_quotes_gpc()) {
            $xmlResponse = stripslashes($xmlResponse);
        }

        if ( curl_errno( $ch ) ) {
            $e =& CRM_Core_Error::singleton( );
            $e->push( curl_errno( $ch ),
                      0, null,
                      curl_error( $ch ) );
            return $e;
        } else {
			curl_close($ch);
        }

        return self::getArrayFromXML( $xmlResponse ); 
   }

    static function buildXMLQuery( $searchParams ) {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>
<notification-history-request xmlns="http://checkout.google.com/schema/2">';

        if ( array_key_exists('next-page-token', $searchParams) ) {
            $xml .= '
<next-page-token>' . $searchParams['next-page-token'] . '</next-page-token>';
        }
        if ( array_key_exists('start', $searchParams) ) {
            $xml .= '
<start-time>' . $searchParams['start'] . '</start-time>
<end-time>'   . $searchParams['end']   . '</end-time>';
        }
        if ( array_key_exists('notification-types', $searchParams) ) {
            $xml .= '
<notification-types>
<notification-type>' . implode($searchParams['notification-types'], '</notification-type>
<notification-type>') . '</notification-type>
</notification-types>';
        }
        if ( array_key_exists('order-numbers', $searchParams) ) {
            $xml .= '
<order-numbers>
<google-order-number>' . implode($searchParams['order-numbers'], '</google-order-number>
<google-order-number>') . '</google-order-number>
</order-numbers>';
        }
        $xml .= '
</notification-history-request>';

        return $xml;
    }
    
    static function getArrayFromXML( $xmlData ) {
        require_once 'Google/library/xml-processing/xmlparser.php';
        $xmlParser = new XmlParser($xmlData);
        $root      = $xmlParser->GetRoot();
        $data      = $xmlParser->GetData();
        
        return array( $root, $data );
    }
}
