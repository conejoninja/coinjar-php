<?php
class CoinJar {

    private $_apiEndpoint;
    private $_checkoutEndpoint;
    private $_apiKey;
    private $_checkoutUser;
    private $_checkoutPassword;

    public function __construct($user = '', $password = '', $apikey = '', $sandbox = false) {
        if($sandbox) {
            $this->_checkoutEndpoint = 'https://checkout.sandbox.coinjar.io/api/v1/';
        } else {
            $this->_checkoutEndpoint = 'https://checkout.coinjar.io/api/v1/';
        }
        $this->_apiEndpoint = 'https://api.coinjar.io/v1/';
        $this->_apiKey = $apikey;
        $this->_checkoutUser = $user;
        $this->_checkoutPassword = $password;
    }

    // Retrieve account information GET
    public function accountInformation() {
        return $this->_doApiRequest('account');
    }

    public function listBitcoinAddresses($limit = 100, $offset = 0) {
        return $this->_doApiRequest('bitcoin_addresses', array('limit' => $limit, 'offset' => $offset));
    }

    public function bitcoinAddress($address) {
        return $this->_doApiRequest('bitcoin_addresses/'.$address);
    }

    public function generateBitcoinAddress($label) {
        return $this->_doApiRequest('bitcoin_addresses', array('label' => $label), 'post');
    }

    public function listContacts($limit = 100, $offset = 0) {
        return $this->_doApiRequest('contacts', array('limit' => $limit, 'offset' => $offset));
    }

    public function contact($uuid) {
        return $this->_doApiRequest('contacts/'.$uuid);
    }

    //payee = email o BTC address
    public function createContact($payee, $name) {
        return $this->_doApiRequest('contacts', array('contact[payee]' => $payee, 'contact[name]' => $name), 'post');
    }

    public function deleteContact($uuid) {
        return $this->_doApiRequest('contacts/'.$uuid, null, 'delete');
    }

    public function listPayments($limit = 100, $offset = 0) {
        return $this->_doApiRequest('payments', array('limit' => $limit, 'offset' => $offset));
    }

    public function payment($uuid) {
        return $this->_doApiRequest('payments/'.$uuid);
    }

    //payee = email o BTC address
    public function createPayment($payee, $amount, $reference) {
        return $this->_doApiRequest('payments', array('payment[payee]' => $payee, 'payment[amount' => $amount, 'payment[reference]' => $reference), 'post');
    }

    public function confirmPayment($uuid) {
        return $this->_doApiRequest('payments/'.$uuid.'/confirm', null, 'post');
    }

    public function listTransactions($limit = 100, $offset = 0) {
        return $this->_doApiRequest('transactions', array('limit' => $limit, 'offset' => $offset));
    }

    public function transaction($uuid) {
        return $this->_doApiRequest('transactions/'.$uuid);
    }

    // $currency could be : BTC, USD, AUD, NZD, CAD, EUR, GBP, SGD, HKD, CHF, JPY
    public function fairRate($currency) {
        return $this->_doApiRequest('fair_rate/'.strtoupper($currency));
    }

    // limit / offset is not on the documentation, CHECK IT!
    public function listOrders($limit = 100, $offset = 0) {
        return $this->_doCheckoutRequest('orders', array('limit' => $limit, 'offset' => $offset));
    }

    public function order($uuid) {
        return $this->_doCheckoutRequest('orders/'.$uuid);
    }

    /*order[order_items_attributes[n][name]]	String	Mandatory
    order[order_items_attributes[n][quantity]]	Decimal	Mandatory
    order[order_items_attributes[n][amount]]	Decimal	Mandatory	Amount in order currency*/
    public function createOrder($items, $currency, $merchant_invoice, $merchant_reference, $notify_url, $return_url, $cancel_url) {
        $params = array(
            'order[currency]' => $currency,
            'order[merchant_invoice]' => $merchant_invoice,
            'order[merchant_reference]' => $merchant_reference,
            'order[notify_url]' => $notify_url,
            'order[return_url]' => $return_url,
            'order[cancel_url]' => $cancel_url
        );
        $k = 0;
        foreach($items as $item) {
            $params['order[order_items_attributes['.$k.'][name]]'] = $item['name'];
            $params['order[order_items_attributes['.$k.'][quantity]]'] = $item['quantity'];
            $params['order[order_items_attributes['.$k.'][amount]]'] = $item['amount'];
            $k++;
        }
        return $this->_doCheckoutRequest('orders', $params, 'post');
    }

    private function _doApiRequest($action, $params = null, $method = "get") {
        return $this->_doRequest($this->_apiEndpoint, $action, $this->_apiKey, $params, $method);
    }

    private function _doCheckoutRequest($action, $params = null, $method = "get") {
        return $this->_doRequest($this->_checkoutEndpoint, $action, $this->_checkoutUser.":".$this->_checkoutPassword, $params, $method);
    }

    private function _doRequest($endpoint, $action, $user, $params = null, $method = "get") {
        $request = '';
        if($params!=null && is_array($params)) {
            foreach($params as $key => $value) {
                $request .= '&'.$key.'='.$value;
            }
        }
        if(strtolower($method)=='post') {
            $curl = curl_init($endpoint.$action.'.json');
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($curl, CURLOPT_POSTFIELDS, $request);
        } else if(strtolower($method)=='delete') {
            $curl = curl_init($endpoint.$action.'.json?'.$request);
            curl_setopt($curl, CURLOPT_POST, false);
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'DELETE');
        }else {
            $curl = curl_init($endpoint.$action.'.json?'.$request);
            curl_setopt($curl, CURLOPT_POST, false);
            curl_setopt($curl, CURLOPT_HTTPGET, true);
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');
            curl_setopt($curl, CURLOPT_POSTFIELDS, $request);
        }

        curl_setopt($curl, CURLOPT_USERPWD, $user);
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }
}
?>