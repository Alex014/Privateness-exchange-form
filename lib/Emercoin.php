<?php 
namespace lib;

require 'JsonRpcClient.php';

use \lib\JsonRpcClient;

/**
 * Emercoin RPC module
 *
 * @author Aleksej Sokolov <aleksej000@gmail.com>,<chosenone111@protonmail.com>
 */
class Emercoin {
  public static $username = '';
  public static $password = '';
  public static $address = 'localhost';
  public static $port = '8332';
  public static $account_prefix = 'emc_terminal_';
  public static $comission = 0.1;
  /**
   * The function, which returns current order ID which is usualy stored in session
   * @var integer 
   */
  public static $get_order_id;
  /**
   * The function, which returns current order ammount (EMC) which is usualy stored in session
   * @var decimal 
   */
  public static $get_order_ammount;
  public static $rpcClient;
  public static $emercoin_info;
  public static $debug = false;
  
  /**
   * Returns an object containing various state info.
   * @return array 
   */
  public static function getinfo() {
    $url = self::$username.':'.self::$password.'@'.self::$address.':'.self::$port.'/';
    self::$rpcClient = new JsonRpcClient($url, self::$debug);
    return self::$emercoin_info = self::$rpcClient->getinfo();
  }
  
  /**
   * Create account or return an existing account address
   * @param string $account
   * @return address
   * @throws Exception
   */
  public static function getAccountAddress($account) {
    $url = self::$username.':'.self::$password.'@'.self::$address.':'.self::$port.'/';
    self::$rpcClient = new JsonRpcClient($url, self::$debug);
    
    return self::$rpcClient->getaccountaddress($account);
  }
  
  public static function listaccounts() {
    $url = self::$username.':'.self::$password.'@'.self::$address.':'.self::$port.'/';
    self::$rpcClient = new JsonRpcClient($url, self::$debug);
    
    return self::$rpcClient->listaccounts();
  }
  
  public static function listAccountsAddresses() {  
    $accounts = self::listaccounts();
    $result = array();
      
    foreach ($accounts as $account => $ammount) {
        $result[$account] = self::getAddressesByAccount($account);
    }
    
    return $result;
  }
  
  public static function getFirstAddress() {  
    $accounts = self::listaccounts();
      
    foreach ($accounts as $account => $ammount) {
        $addresses = self::getAddressesByAccount($account);
        foreach ($addresses as $addr) {
            $address = $addr;
        }
    }
    return $address;
  }
  
  /**
   * All Addresses by account
   * @param type $account
   * @return type
   */
  public static function getAddressesByAccount($account) {
    $url = self::$username.':'.self::$password.'@'.self::$address.':'.self::$port.'/';
    self::$rpcClient = new JsonRpcClient($url, self::$debug);
    
    return self::$rpcClient->getaddressesbyaccount($account);
  }
  
  /**
   * Function creates new account and returns it's address to recieve payments
   * The account name is saved as "self::$account_prefix+_+self::$get_order_id()"
   * @return account Payment address
   */
  public static function createPaymentAddress() {
    $url = self::$username.':'.self::$password.'@'.self::$address.':'.self::$port.'/';
    self::$rpcClient = new JsonRpcClient($url, self::$debug);
    
    if(!is_callable(self::$get_order_id)) {
      throw new \Exception('Property self::$get_order_id must be callable');
    }
    $account = self::$account_prefix.'_'.call_user_func(self::$get_order_id);
    return self::$rpcClient->getaccountaddress($account);
  }
  
  /**
   * The function compares the current account ammount of EMC and
   * current order ammount of EMC and returns TRUE if 
   * (account ammount) >= (order ammount)
   * The account name is "self::$account_prefix+_+self::$get_order_id()"
   * The order ammount is stored in self::$get_order_ammount()
   * @return boolean
   * @throws Exception
   */
  public static function confirmPayment() {
    $url = self::$username.':'.self::$password.'@'.self::$address.':'.self::$port.'/';
    self::$rpcClient = new JsonRpcClient($url, self::$debug);
    
    if(!is_callable(self::$get_order_id)) {
      throw new \Exception('Property self::$get_order_id must be callable');
    }
    $account = self::$account_prefix.'_'.call_user_func(self::$get_order_id);
    
    if(!is_callable(self::$get_order_ammount)) {
      throw new \Exception('Property self::$get_order_ammount must be callable');
    }
    $order_ammount = call_user_func(self::$get_order_ammount);
    
    
    try {
      $received_amount = self::$rpcClient->getreceivedbyaccount($account, 0);
    } catch (\Exception $e) {
            echo false;
    }
    if((float)$received_amount >= (float)$order_ammount) {
            return true;
    }
    else {
            return false;
    }
  }
  
  /**
   * Return the balance from account
   * @param type $account
   * @return float
   */
  public static function getAccauntBalance($account) {
    $url = self::$username.':'.self::$password.'@'.self::$address.':'.self::$port.'/';
    self::$rpcClient = new JsonRpcClient($url, self::$debug);
    
    return self::$rpcClient->getbalance($account);
  }
  
  /**
   * Return total from the wallet
   * @return float
   */
  public static function getAllBalance() {
    $url = self::$username.':'.self::$password.'@'.self::$address.':'.self::$port.'/';
    self::$rpcClient = new JsonRpcClient($url, self::$debug);
    
    return self::$rpcClient->getbalance();
  }
  
  /**
   * Send some EMC to address
   * 
   * @param type $emercoinaddress
   * @param type $amount
   * @param type $account - send account from
   * @return type 
   */
  public static function sendToAddress($emercoinaddress, $amount, $account = '') {
    $url = self::$username.':'.self::$password.'@'.self::$address.':'.self::$port.'/';
    self::$rpcClient = new JsonRpcClient($url, self::$debug);
    
    if($account == '') {
      return self::$rpcClient->sendtoaddress($emercoinaddress, (double)$amount);
    }
    else {
      return self::$rpcClient->sendfrom($account, $emercoinaddress, (double)$amount);
    }
  }
  
  /**
   * Send ALL your EMC to address
   * @param type $emercoinaddress
   * @return type
   */
  public static function sendAllToAddress($emercoinaddress) {
    $url = self::$username.':'.self::$password.'@'.self::$address.':'.self::$port.'/';
    self::$rpcClient = new JsonRpcClient($url, self::$debug);
    
    $amount = self::getAllBalance();
    
    return self::$rpcClient->sendtoaddress($emercoinaddress, (double)$amount);
  }
  
  /**
   * 
   * @param type $account
   * @return type 
   */
  public static function createNewAddress($account) {
      $url = self::$username.':'.self::$password.'@'.self::$address.':'.self::$port.'/';
      self::$rpcClient = new JsonRpcClient($url, self::$debug);

      return self::$rpcClient->getnewaddress($account);
  }
  
  /**
   * Total recieved by address
   * @param type $emercoinaddress
   * @return type
   */
  public static function getRecievedByAddress($emercoinaddress) {
    $url = self::$username.':'.self::$password.'@'.self::$address.':'.self::$port.'/';
    self::$rpcClient = new JsonRpcClient($url, self::$debug);
    
    return self::$rpcClient->getreceivedbyaddress($emercoinaddress);
  }
  
  /**
   * Trasaction list
   * @param type $account
   * @param type $count
   * @return array
   */
  public static function listtransactions($account, $count = 1000) {
    $url = self::$username.':'.self::$password.'@'.self::$address.':'.self::$port.'/';
    self::$rpcClient = new JsonRpcClient($url, self::$debug);
    
    return self::$rpcClient->listtransactions($account, $count);
  }  
  
  /**
   * Last transaction
   * @param type $account
   * @param type $address
   * @param type $count
   * @return type
   */
  public static function getLastReceivedTransaction($account, $address, $count = 1000) {
    //echo " getLastReceivedTransaction($account, $address, $count = 1000) ";
    $transactions = self::listtransactions($account, $count);
    //var_dump($transactions);
    $tx_hight = count($transactions) - 1;
    for($i = $tx_hight; $i >= 0; $i--) {
      if(($transactions[$i]['category'] == 'receive') && ($transactions[$i]['address'] == $address)) {
        return $transactions[$i];
      }
    }
  }  

  /**
   * Sign a message with the private key of an address
        Requires wallet passphrase to be set with walletpassphrase call.
   * @param type $emercoinaddress The emercoin address to use for the private key.
   * @param type $message The message to create a signature of.
   * @return type
   */
  public static function signmessage($emercoinaddress, $message) {
    $url = self::$username.':'.self::$password.'@'.self::$address.':'.self::$port.'/';
    self::$rpcClient = new JsonRpcClient($url, self::$debug);
    
    return self::$rpcClient->signmessage($emercoinaddress, $message);
  }


  /**
   * Verify a signed message
   * @param type $emercoinaddress The emercoin address to use for the signature.
   * @param type $signature The signature provided by the signer in base 64 encoding (see signmessage).
   * @param type $message The message that was signed.
   * @return type
   */
  public static function verifymessage($emercoinaddress, $signature, $message) {
    $url = self::$username.':'.self::$password.'@'.self::$address.':'.self::$port.'/';
    self::$rpcClient = new JsonRpcClient($url, self::$debug);
    
    return self::$rpcClient->verifymessage($emercoinaddress, $signature, (string)$message);
  }

  /**
   * scan and filter names
   * name_filter "" 5 # list names updated in last 5 blocks
   * name_filter "^id/" # list all names from the "id" namespace
   * name_filter "^id/" 0 0 0 stat # display stats (number of names) on active names from the "id" namespace
   * @param type $regexp apply [regexp] on names, empty means all names
   * @param int $maxage look in last [maxage] blocks
   * @param int $from show results from number [from]
   * @param int $nb show [nb] results, 0 means all
   * @param type $stat show some stats instead of results
   * @param type $valuetype if "hex" or "base64" is specified then it will print value in corresponding format instead of string.

   * @return array
   */
  public static function name_filter( $regexp, $maxage=0, $from=0, $nb=0, $stat='', $valuetype=''): array {
    $url = self::$username.':'.self::$password.'@'.self::$address.':'.self::$port.'/';
    self::$rpcClient = new JsonRpcClient($url, self::$debug);

    if(!empty($valuetype)) {
        return self::$rpcClient->name_filter( $regexp, $maxage, $from, $nb, $stat, $valuetype);
    }
    elseif(!empty($stat)) {
        return self::$rpcClient->name_filter( $regexp, $maxage, $from, $nb, $stat);
    }
    else {
        return self::$rpcClient->name_filter( $regexp, $maxage, $from, $nb);
    }
  }
  
  /**
   * Look up the current and all past data for the given name.
   * @param type $name
   * @param type $fullhistory
   * @param type $valuetype
   * @return type
   */
  public static function name_history( $name , $fullhistory, $valuetype) {
    $url = self::$username.':'.self::$password.'@'.self::$address.':'.self::$port.'/';
    self::$rpcClient = new JsonRpcClient($url, self::$debug);
    
    return self::$rpcClient->name_history( $name , $fullhistory, $valuetype);
  }
  
  /**
   * list pending name transactions in mempool.
   * @param type $valuetype
   * @return type
   */
  public static function name_mempool( $valuetype ) {
    $url = self::$username.':'.self::$password.'@'.self::$address.':'.self::$port.'/';
    self::$rpcClient = new JsonRpcClient($url, self::$debug);
    
    return self::$rpcClient->name_mempool($valuetype);
  }
  
  /**
   * Scan all names, starting at start-name and returning a maximum number of entries (default 500)
   * @param type $start_name
   * @param type $max_returned
   * @param type $max_value_length
   * @param type $valuetype
   * @return type
   */
  public static function name_scan( $start_name, $max_returned, $max_value_length=-1, $valuetype='') {
    $url = self::$username.':'.self::$password.'@'.self::$address.':'.self::$port.'/';
    self::$rpcClient = new JsonRpcClient($url, self::$debug);
    
    return self::$rpcClient->name_scan($start_name, $max_returned, $max_value_length, $valuetype);
  }
  
    /**
   * List my own names.
   * @param type $name (string, required) Restrict output to specific name
   * @param type $valuetype (string, optional) If "hex" or "base64" is specified then it will print value in corresponding format instead of string.
   * @return type
   */
  public static function name_list( $name, $valuetype='') {
    $url = self::$username.':'.self::$password.'@'.self::$address.':'.self::$port.'/';
    self::$rpcClient = new JsonRpcClient($url, self::$debug);
    
    return self::$rpcClient->name_list($name, $valuetype);
  }
  
  /**
   * List my own names (filtered).
   * @param string $name_start first part of a name
   * @param type $valuetype
   */
  public static function name_list_filtered( $name_start, $valuetype='') {
      $records = self::name_list("", $valuetype);
      $result = array();
      
      foreach ($records as $record) {
          if(strpos($record['name'], $name_start) === 0) {
              $result[] = $record;
          }
      }
      
      return $result;
  }
  
  /**
   * Show values of a name.
   * @param type $name
   * @param type $valuetype If "hex" or "base64" is specified then it will print value in corresponding format instead of string.
   * @param type $filepath save name value in binary format in specified file (file will be overwritten!).
   * @return type
   */
  public static function name_show( $name, $valuetype='', $filepath='') {
    $url = self::$username.':'.self::$password.'@'.self::$address.':'.self::$port.'/';
    self::$rpcClient = new JsonRpcClient($url, self::$debug);
    
    if(!empty($filepath)) {
        return self::$rpcClient->name_show($name, $valuetype, $filepath);
    }
    elseif(!empty($valuetype)) {
        return self::$rpcClient->name_show($name, $valuetype);
    }
    else {
        return self::$rpcClient->name_show($name);
    }
    
    
  }
  
  public static function name_new($name, $value, $days, $toaddress = '', $valuetype = "") {
    $url = self::$username.':'.self::$password.'@'.self::$address.':'.self::$port.'/';
    self::$rpcClient = new JsonRpcClient($url, self::$debug);
    
    //return self::$rpcClient->name_new($name, $value, $days);
    
    if(!empty($toaddress)) {
        return self::$rpcClient->name_new($name, $value, $days, $toaddress);
    }
    elseif(!empty($valuetype)) {
        return self::$rpcClient->name_new($name, $value, $days, $toaddress, $valuetype);
    }
    else {
        return self::$rpcClient->name_new($name, $value, $days);
    }
  }
  
  public static function names_new($values, $days, $toaddress = '', $valuetype = "") {
    $url = self::$username.':'.self::$password.'@'.self::$address.':'.self::$port.'/';
    self::$rpcClient = new JsonRpcClient($url, self::$debug);
    
    //return self::$rpcClient->name_new($name, $value, $days);
    
    if(!empty($toaddress)) {
        foreach ($values as $name => $value)
            self::$rpcClient->name_new($name, $value, $days, $toaddress);
    }
    elseif(!empty($valuetype)) {
        foreach ($values as $name => $value)
            self::$rpcClient->name_new($name, $value, $days, $toaddress, $valuetype);
    }
    else {
        foreach ($values as $name => $value)
            self::$rpcClient->name_new($name, $value, $days);
    }
  }
  
  public static function name_update( $name, $value, $days, $toaddress='', $valuetype='') {
    $url = self::$username.':'.self::$password.'@'.self::$address.':'.self::$port.'/';
    self::$rpcClient = new JsonRpcClient($url, self::$debug);
    
    //return self::$rpcClient->name_update($name, $value, $days);
    
    if(!empty($toaddress)) {
        return self::$rpcClient->name_update($name, $value, $days, $toaddress);
    }
    elseif(!empty($valuetype)) {
        return self::$rpcClient->name_update($name, $value, $days, $toaddress, $valuetype);
    }
    else {
        return self::$rpcClient->name_update($name, $value, $days);
    }
  }
  
  public static function name_delete( $name ) {
    $url = self::$username.':'.self::$password.'@'.self::$address.':'.self::$port.'/';
    self::$rpcClient = new JsonRpcClient($url, self::$debug);
    
    return self::$rpcClient->name_delete( $name );
  }
}
