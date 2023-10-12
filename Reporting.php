<?php 

require_once 'AmazonBusinessApi.php';

class Reporting extends AmazonBusinessAPI{

    public function getOrdersByOrderDate($startDate,$endDate,$nextPageToken=false,$includeLineItems=false,$includeShipments=false,$includeCharges=false){
        $accessToken = $this->getAccessToken();
        if ($accessToken) {
            $uriPath = "/reports/2021-01-08/orders";
            $url = $this->apiEndpoint . $uriPath;
            $method = 'GET';
            $headers = [
                "x-amz-access-token:$accessToken",
            ];
            $startDate = $this->setAmzDateTimeFormat($startDate);
            $endDate = $this->setAmzDateTimeFormat($endDate);
            $queryParams = [
                "startDate=$startDate",
                "endDate=$endDate"
            ];
            if($nextPageToken){
                $queryParams[] = "nextPageToken=$nextPageToken";
            }
            if($includeShipments){
                $queryParams[] = "includeShipments=$includeShipments";
            }
            if($includeLineItems){
                $queryParams[] = "includeLineItems=$includeLineItems";
            }
            if($includeCharges){
                $queryParams[] = "includeCharges=$includeCharges";
            }
            

            $url .= "?" . implode("&",$queryParams);
            $response = $this->sendCurlRequest($url, $method, $headers);
            if($response){
                $finalResponse = json_decode($response, true);
                return $this->response(true,$finalResponse );
            }
        }
        return $this->response(false, [] , $this->lastCurlError );
    }

    public function getOrdersByOrderId($orderId,$includeLineItems=false,$includeShipments=false,$includeCharges=false){
        $accessToken = $this->getAccessToken();
        if ($accessToken) {
            $uriPath = "/reports/2021-01-08/orders/{$orderId}";
            $url = $this->apiEndpoint . $uriPath;
            $method = 'GET';
            $headers = [
                "x-amz-access-token:$accessToken",
            ];
          
            $queryParams = [
            ];
            if($includeLineItems){
                $queryParams[] = "includeLineItems=$includeLineItems";
            }
            if($includeShipments){
                $queryParams[] = "includeShipments=$includeShipments";
            }
            if($includeCharges){
                $queryParams[] = "includeCharges=$includeCharges";
            }
            

            $url .= "?" . implode("&",$queryParams);
            $response = $this->sendCurlRequest($url, $method, $headers, false);
            if($response){
                $finalResponse = json_decode($response, true);
                return $this->response(true,$finalResponse );
            }
        }
        return $this->response(false, [] , $this->lastCurlError );
    }
}
