<?php

// thx to  Tobias Redmann

define('API_URL', 'https://cig.dhl.de/cig-wsdls/com/dpdhl/wsdl/geschaeftskundenversand-api/1.0/geschaeftskundenversand-api-1.0.wsdl');
define('SANDBOX_URL', 'https://cig.dhl.de/services/sandbox/soap');
define('PRODUCTION_URL', 'https://cig.dhl.de/services/production/soap');

/**
 * 
 */
class DHLBusinessShipment{
  
  private $credentials;
  
  private $info;
  
  private $client;
  
  public $errors;
  
  /**
   * Constructor for Shipment SDK
   * 
   * @param type $api_credentials
   * @param type $customer_info
   */
  function __construct($api_credentials, $customer_info) {
    
    $this->credentials = $api_credentials;
    $this->info = $customer_info;
    
    $this->errors = array();
    
  }
  
  private function log($message) {
    
    if (isset($this->credentials['log'])) {
      
      if (is_array($message) || is_object($message)) {
        
        error_log(print_r($message, true));
        
      } else {
      
        error_log($message);
        
      }
      
    }
    
  }
  
  function buildClient() {
    
    $header = $this->buildAuthHeader();
    
    $location = SANDBOX_URL;
    
    $auth_params = array(
        'login' => $this->credentials['api_user'],
        'password' => $this->credentials['api_password'],
        'location'  => $location,
        'trace' => 1
        
    );
    
    $this->log($auth_params);
    
    $this->client = new SoapClient(API_URL, $auth_params);
    
    $this->client->__setSoapHeaders($header);
    
    $this->log($this->client);
    
    
  }
  
  function createNationalShipment($customer_details, $shipment_details = null, $bank_details = null, $cod_details = null) {
    
    $this->buildClient();
    
    $shipment = array();
    
    // Version
    $shipment['Version']  = array('majorRelease' => '1', 'minorRelease' => '0');
    

    if($customer_details['country_code']=="" || $customer_details['country_code']=="DE") 
    { 
       $customer_details['country_code']="DE";
       $customer_details['country_zip']="germany";
    } else if ($customer_details['country_code']=="UK"){
       $customer_details['country_zip']="england";
    } else {
       $customer_details['country_zip']="other";
    }
    
    // Order
    $shipment['ShipmentOrder'] = array();
    
    // Fixme
    $shipment['ShipmentOrder']['SequenceNumber']  = '1';
    
    // Shipment
    $s = array();
    $s['ProductCode']               = 'EPN';
    $s['ShipmentDate']              = date('Y-m-d');
    $s['EKP']                       = $this->credentials['ekp'];
    
    $s['Attendance']                = array();
    $s['Attendance']['partnerID']   = '01';
    
    if ($shipment_details == null) {
      $s['ShipmentItem']  = array();
      $s['ShipmentItem']['WeightInKG'] = '5';
      $s['ShipmentItem']['LengthInCM'] = '50';
      $s['ShipmentItem']['WidthInCM']  = '50';
      $s['ShipmentItem']['HeightInCM'] = '50';
      // FIXME: What is this
      $s['ShipmentItem']['PackageType'] = 'PL';
    } else {
      $s['ShipmentItem']  = array();
      $s['ShipmentItem']['WeightInKG'] = $shipment_details['WeightInKG'];
      $s['ShipmentItem']['LengthInCM'] = $shipment_details['LengthInCM'];
      $s['ShipmentItem']['WidthInCM']  = $shipment_details['WidthInCM'];
      $s['ShipmentItem']['HeightInCM'] = $shipment_details['HeightInCM'];
      // FIXME: What is this
      $s['ShipmentItem']['PackageType'] = $shipment_details['PackageType'];
    }

    // Falls ein Gewicht angegeben worden ist
    if($customer_details['weight']!="")
      $s['ShipmentItem']['WeightInKG'] = $customer_details['weight'];
   
    if($bank_details != null)
    {
      $s['BankData']  = array();
      $s['BankData']['accountOwner'] = $bank_details['account_owner'];
      $s['BankData']['accountNumber'] = $bank_details['account_number'];
      $s['BankData']['bankCode'] = $bank_details['bank_code'];
      $s['BankData']['bankName'] = $bank_details['bank_name'];
      $s['BankData']['iban'] = $bank_details['iban'];
      $s['BankData']['bic'] = $bank_details['bic'];
      $s['BankData']['note'] = $bank_details['note'];
    } 

    if($cod_details != null)
    {
      //$s['Service']  = array();
      //$s['Service']['ServiceGroupOther']  = array();
      $s['Service']['ServiceGroupOther']['COD']  = array();
      $s['Service']['ServiceGroupOther']['COD']['CODAmount'] = $cod_details['amount'];
      $s['Service']['ServiceGroupOther']['COD']['CODCurrency'] = $cod_details['currency'];
    }

    // Auftragnummer auf Label
    $s['CustomerReference']=$customer_details['ordernumber'];


    
    $shipment['ShipmentOrder']['Shipment']['ShipmentDetails'] = $s;

    //$shipment['ShipmentOrder']['Shipment']['ShipmentDetails'] = $s;
    
    
    $shipper = array();
    $shipper['Company'] = array();
    $shipper['Company']['Company'] = array();
    $shipper['Company']['Company']['name1'] = $this->info['company_name'];
    
    $shipper['Address'] = array();
    $shipper['Address']['streetName']     = $this->info['street_name'];
    $shipper['Address']['streetNumber']   = $this->info['street_number'];
    $shipper['Address']['Zip']            = array();
    $shipper['Address']['Zip'][strtolower($this->info['country'])]  = $this->info['zip'];
    $shipper['Address']['city']           = $this->info['city'];
    
    $shipper['Address']['Origin'] = array('countryISOCode' => 'DE');
    
    $shipper['Communication']                   = array();
    $shipper['Communication']['email']          = $this->info['email'];
    $shipper['Communication']['phone']          = $this->info['phone'];
    $shipper['Communication']['internet']       = $this->info['internet'];
    $shipper['Communication']['contactPerson']  = $this->info['contact_person'];
    
    
    $shipment['ShipmentOrder']['Shipment']['Shipper'] = $shipper;
    
    $receiver = array();
    
    $receiver['Company'] = array();

/*
    $receiver['Company']['Person']  = array();
    $receiver['Company']['Person']['firstname'] = $customer_details['first_name'];
    $receiver['Company']['Person']['lastname'] = $customer_details['last_name'];
*/

    $receiver['Company']['Company']  = array();
    $receiver['Company']['Company']['name1'] = $customer_details['name1'];
    $receiver['Company']['Company']['name2'] = $customer_details['name2'];
 
    
    $receiver['Address'] = array();
    $receiver['Address']['streetName']     = $customer_details['street_name'];
    $receiver['Address']['streetNumber']   = $customer_details['street_number'];
    $receiver['Address']['Zip']            = array();
    $receiver['Address']['Zip'][strtolower($customer_details['country_zip'])]  = $customer_details['zip'];
    $receiver['Address']['city']           = $customer_details['city'];
    $receiver['Communication']                   = array();

    
    $receiver['Address']['Origin'] = array('countryISOCode' => $customer_details['country_code']);
    
    $shipment['ShipmentOrder']['Shipment']['Receiver'] = $receiver;

    
    
    $response = $this->client->CreateShipmentDD($shipment);
    
    if (is_soap_fault($response) || $response->status->StatusCode != 0) {
      
      if (is_soap_fault($response)) {
        
        $this->errors[] = $response->faultstring;
        
      } else {
        
        $this->errors[] = $response->status->StatusMessage;
        
      }
      
      return false;
      
    } else {
      
      $r = array();
      $r['shipment_number']   = (String) $response->CreationState->ShipmentNumber->shipmentNumber;
      $r['piece_number']      = (String) $response->CreationState->PieceInformation->PieceNumber->licensePlate;
      $r['label_url']         = (String) $response->CreationState->Labelurl;
      
      return $r;
    }
    
  }
  
  
  /*
  function getVersion() {
    
    $this->buildClient();
    
    $this->log("Response: \n");
    
    $response = $this->client->getVersion(array('majorRelease' => '1', 'minorRelease' => '0'));
    
    $this->log($response);
    
  }
  */
  
  
  
  private function buildAuthHeader() {
    
    $head = $this->credentials;
    
    $auth_params = array(
        'user' => $this->credentials['user'],
        'signature' => $this->credentials['signature'],
        'type'  => 0
        
    );
    
    return new SoapHeader('http://dhl.de/webservice/cisbase','Authentification', $auth_params);
    
    
  }
  
  
}

?>
