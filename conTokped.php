<?php 

defined('BASEPATH') OR exit('No direct script access allowed');
Class connTokped extends CI_Controller {
  function __construct()
    {
        parent::__construct();
        
    }
    
    public function index()
    {
      
    }
  
  public function Get_authorization() {
        /*
         You need to encode client_id & client_secret use to base64 (Visit base64encode.org)
         with format === client_id:client_secret
         sample result === Y2xpZW50X2lkOmNsaWVudF9zZWNyZXQK 
        */
        $url_authorization = "https://accounts.tokopedia.com/token";
        
        $curl = curl_init();
        
       
        curl_setopt_array($curl, array(
            
            CURLOPT_URL => $url_authorization,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => array(
                "Authorization: Basic Y2xpZW50X2lkOmNsaWVudF9zZWNyZXQK"
            ),
            CURLOPT_POSTFIELDS => 'grant_type=client_credentials'
        ));
        
        $response = curl_exec($curl);
        $err = curl_error($curl);
        
        curl_close($curl);
        
        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            $data = json_decode($response);
            $this->token = $data->access_token;
        }
    }
  
  public function Price_update(){
    /* 
      to update price by sku 
      sample fs_id = 13004
      sample shop_id = 479573
    */
        //get token
        $this->Get_authorization();
        
        $this->load->model('Toped_m');
	    
      // get data from your database
	    $sql = "select sku,price from my_product  ";
	    $arr_product = $this->Toped_m->get_query($sql,1);
        
        $i=0;
        foreach ($arr_product['records'] as $key=>$val){
              $product[0]["sku"] = $val['SKU'];
              $product[0]["new_price"] = (int)$val['PRICE'];

          $url_stock = "https://fs.tokopedia.net/inventory/v1/fs/13004/price/update?shop_id=479573";

          $curl = curl_init(); 
          $data_artikel = json_encode($product);
          //print_r($data_artikel); 
          curl_setopt_array($curl, array(

            CURLOPT_URL => $url_stock,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_POST => true,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_HTTPHEADER => array(
              "Authorization: Bearer ".$this->token,
              "Content-Type:application/json",
              "Content-Length: " . strlen($data_artikel)
            ),
            CURLOPT_POSTFIELDS => $data_artikel
          ));

          $response = curl_exec($curl);
          $resp = json_decode($response,true);
          curl_close($curl);
          
          //you can add logic here. like save to the Log table
          
          $i++;
        }	
    }
  
  public function Stock_update(){
    /* 
      to update stock by sku 
      sample fs_id = 13004
      sample shop_id = 479573
    */
       
    $this->load->model('Toped_m');
	    
    $sql = "SELECT sku,stock FROM my_product  ";
	  $arr_product = $this->Toped_m->get_query($sql,1);
        
		$i=0;
		foreach ($arr_product['records'] as $key=>$val){
				
					$product[0]["sku"] = $val['sku'];
					$product[0]["new_stock"] = $val['stock'];
				
			$this->Get_authorization(); 
        
			$url_stock = "https://fs.tokopedia.net/inventory/v1/fs/13004/stock/update?shop_id=479573";
			
			$curl = curl_init(); 
			$data_artikel = json_encode($product);
			
			curl_setopt_array($curl, array(
				
				CURLOPT_URL => $url_stock,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => "",
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 0,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => "POST",
				CURLOPT_HTTPHEADER => array(
					"Authorization: Bearer ".$this->token,
					"Content-Type:application/json"
				),
				CURLOPT_POSTFIELDS => $data_artikel
			));
			
			$response = curl_exec($curl);
			$resp = json_decode($response,true);
			$err = curl_error($curl);
			curl_close($curl);
			
			$i++;
		}	
  }
  
}

?>
