<?php

class AmazonBusinessAPI
{   
    private $clientId;
    private $clientSecret;
    private $refreshToken;
    protected $apiEndpoint  = 'https://na.business-api.amazon.com';
    protected $authEndpoint = 'https://api.amazon.com/auth/O2/token';
    public $sslVerification = true;

    public function __construct( $clientId, $clientSecret, $refreshToken )
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->refreshToken = $refreshToken;
    }

    public function setApiEndpoint($endpoint)
    {
        $this->apiEndpoint = $endpoint;
    }

    public function clearCurlResponse()
    {
        $this->lastCurlError = '';
        $this->lastCurlResult = '';
    }

    public function setCurlResponse($result)
    {
        $this->lastCurlResponse = $result;
    }

    public function setCurlError($error)
    {
        $this->lastCurlError = $error;
    }

    public function disableSslVerification()
    {
        $this->sslVerification = false;
    }

    public function sendCurlRequest($url, $method = 'GET', $headers = [], $data = null)
    {
        $this->clearCurlResponse();

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));

        if (!empty($headers)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        if ($data !== null) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }
        if (!$this->sslVerification) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        }
        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            $this->setCurlError(curl_error($ch));
            curl_close($ch);
            return false;
        }

        curl_close($ch);
        $this->setCurlResponse($response);

        return $response;
    }

    protected function getAccessToken()
    {
        $clientId = $this->clientId;
        $clientSecret = $this->clientSecret;
        $refreshToken = $this->refreshToken;
        $curl = curl_init();

        $url = $this->authEndpoint;
        $data = "grant_type=refresh_token&refresh_token=$refreshToken&client_id=$clientId&client_secret=$clientSecret";
        $response = $this->sendCurlRequest($url, "POST", [], $data);
        if (!$response) {
            return false;
        }
        $result = json_decode($response, true);
        if($result){
            if(isset($result['access_token'])){
                return $result['access_token'];
            }
            $error_desc = $result['error_description'];
            $error = $result['error'];
            $this->setCurlError("$error : $error_desc");
        }
        return false;
    }

    function setAmzDateTimeFormat($datetime, $inputFormat = "Y-m-d H:i:s", $outputFormat="Y-m-d\TH:i:s.u\Z") {
        // Create a DateTime object from the input datetime string and format
        $dateTime = DateTime::createFromFormat($inputFormat, $datetime);
    
        // Check if there was an error in parsing the input datetime
        if (!$dateTime) {
            return false;
        }
    
        // Convert the datetime to the desired timezone (optional)
        // $dateTime->setTimezone(new DateTimeZone('YourTimeZone'));
    
        // Format the datetime in the desired output format
        $outputDateTime = $dateTime->format($outputFormat);
    
        return $outputDateTime;
    }

    function response($success,$output=null,$error_message=""){
        return [
            'success' => $success,
            'output' => $output,
            'error_msg' => $error_message,
        ];
    }
}
