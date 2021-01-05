<?php
namespace App\Lib;


// use App\Lib\CPHelper;

class coinBase
{

    private $API_KEY;
    private $API_VERSION;
    public $paymentErrors;


	public function setAPIKey($secretKey)
	{
		$this->API_KEY = $secretKey;
    }

    public function setAPIVersion($api_version)
	{
		$this->API_KEY = $api_version;
	}


	function createPayment($name, $priceUSD, $description, $successUrl = '', $cancelUrl = '')
	{
        $data =[];
        $data['name'] = $name;
        $data['description'] = $description;
        $data['pricing_type'] = 'fixed_price';
        $data["local_price"]['amount']= $priceUSD;
        $data["local_price"]['currency']="USD";

        return $this->Charge(($data));

    }

    private function Charge($data =array()){

        if( !(isset($this->API_VERSION) && $this->API_KEY) ){
            return array('error'=>"API key and API_version need to be set in config.php");
        }

        if( !(in_array('name',$data) && in_array('description',$data) && in_array('price_type',$data)) ){
            return array('error'=>'name, description and price_type are required parameters');
        }

        $base_url ='https://api.commerce.coinbase.com/charges';
        $contentType = 'application/json';

        $calling_API = curl_init($base_url);

        $payload = json_encode($data);

        $http_header = ['Content-Type: '.$contentType,'Content-length: '.strlen($payload),'X-CC-Api-Key: '.$this->API_KEY,'X-CC-Version: '.$thisAPI_VERSION];


        curl_setopt($calling_API, CURLOPT_FAILONERROR, TRUE);
        curl_setopt($calling_API, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($calling_API, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($calling_API, CURLOPT_POST, TRUE);
        curl_setopt($calling_API, CURLOPT_POSTFIELDS, $payload);

        curl_setopt($calling_API, CURLOPT_HTTPHEADER,$http_header);

        $result = curl_exec($calling_API);

        if ($result !== FALSE) {
            if (PHP_INT_SIZE < 8 && version_compare(PHP_VERSION, '5.4.0') >= 0) {
                // We are on 32-bit PHP, so use the bigint as string option. If you are using any API calls with Satoshis it is highly NOT recommended to use 32-bit PHP
                $dec = json_decode($result, TRUE, 512, JSON_BIGINT_AS_STRING);
            } else {
                $dec = json_decode($result, TRUE);
            }
            if ($dec !== NULL && count($dec)) {
                return $dec;
            } else {
                // If you are using PHP 5.5.0 or higher you can use json_last_error_msg() for a better error message
                return array('error' => 'Unable to parse JSON result ('.json_last_error().')');
            }
        } else {
            return array('error' => 'cURL error: '.curl_error($calling_API));
        }
    }


	function ValidatePayment($cost, $currency)
	{




	}


	public function getErrors()
	{

	}











}
