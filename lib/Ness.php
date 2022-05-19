<?php
namespace lib;


/**
 * Ness lib module
 *
 * @author Aleksej Sokolov <aleksej000@gmail.com>,<chosenone111@protonmail.com>
 */
class Ness {
  private $host = '';
  private $port = 6460;
  private $wallet_id = '';
  private $password = '';

  public function __construct(string $host, int $port, string $wallet_id, string $password)
  {
    $this->host = $host;
    $this->port = $port;
    $this->wallet_id = $wallet_id;
    $this->password = $password;
  }

  public function health()
  {
    $responce = file_get_contents("http://" . $this->host . ":" . $this->port . "/api/v1/health");
    if (false !== $responce) {
      return json_decode($responce, true);
    } else {
      return false;
    }
  }

  public function createAddr(): string 
  {
    $responce = file_get_contents("http://" . $this->host . ":" . $this->port . "/api/v1/csrf");
    // var_dump($responce);

    if (empty($responce)) {
      throw new \Exception("Privateness daemon is not running");
    }

    $responce = json_decode($responce, true);
    $token = $responce["csrf_token"];

    $fields = [
      'id' => $this->wallet_id,
      'num' => 1,
      'password' => $this->password
    ];

    $ch = curl_init("http://" . $this->host . ":" . $this->port . "/api/v1/wallet/newAddress");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('X-CSRF-Token: '.$token));
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);

    $output = curl_exec($ch);
    // var_dump($responce, $output);
    if (empty($output)) {
      throw new \Exception("Privateness daemon is not running");
    }

    $output = json_decode($output, true);

    return $output['addresses'][0];
  }  
  
  public function getAddress(string $addr): array 
  {
    // var_dump($addr);
    $output = file_get_contents("http://" . $this->host . ":" . $this->port . "/api/v1/balance?addrs=" . $addr);
    // var_dump($output);

    if (empty($output)) {
      return [
        $addr => ['addresses' => [] ]
      ];
    }

    return json_decode($output, true);
  }

  public function checkAddress(string $addr): bool 
  {
    return isset($this->getAddress($addr)['addresses'][$addr]);
  }

  public function checkLastRecieved(string $from_addr, string $to_addr): bool
  {
    $responce = file_get_contents("http://" . $this->host . ":" . $this->port . "/api/v1/transactions?addrs=" . $to_addr . "&confirmed=1&verbose=1");
    $transactions = json_decode($responce, true);

    if (0 === count($transactions)) {
      return false;
    }

    $last_transaction = $transactions[count($transactions) - 1];

    $result = false;

    foreach ($last_transaction['txn']['inputs'] as $input) {
      if ($input['owner'] == $from_addr) {
        return true;
      }
    }

    return false;
  }

  public function pay(string $from_addr, string $to_addr, float $coins, int $hours) 
  {
    $responce = file_get_contents("http://" . $this->host . ":" . $this->port . "/api/v1/csrf");

    $responce = json_decode($responce, true);
    $token = $responce["csrf_token"];
    $wallet_id = $this->wallet_id;
    $password = $this->password;

    $body = <<<BODY
    {
        "hours_selection": {
            "type": "manual"
        },
        "wallet_id": "$wallet_id",
        "password": "$password",
        "addresses": ["$from_addr"],
        "to": [{
            "address": "$to_addr",
            "coins": "$coins",
            "hours": "$hours"
        }]
    }
BODY;

    $ch = curl_init("http://" . $this->host . ":" . $this->port . "/api/v1/wallet/transaction");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'X-CSRF-Token: '.$token));
    curl_setopt($ch, CURLOPT_POSTFIELDS, $body);

    $output = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if (200 !== $httpcode) {
      $msg = explode(' - ', $output, 2);

      if(2 === count($msg)) {
        $msg = $msg[1];
      } else {
        $msg = $msg[0];
      }

      throw new \Exception($msg);
    }

    $json_output = json_decode($output, true);
    $encoded_transaction = $json_output['encoded_transaction'];

    $body = '{"rawtx": "' . $encoded_transaction . '"}';

    // var_dump($output); 
    // die();

    $ch = curl_init("http://" . $this->host . ":" . $this->port . "/api/v1/injectTransaction");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'X-CSRF-Token: '.$token));
    curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
    $output = curl_exec($ch);


    return true;
  }
}
