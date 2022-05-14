<?php
require '../lib/DB.php';

ini_set('display_errors', true);

use lib\DB;

$config = require '../config/config.php';
$db = new DB($config['db']['host'], $config['db']['database'], $config['db']['user'], $config['db']['password']);

var_dump($_GET);
?>

<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <title>Exchange token</title>
  </head>
  <body>


  <div class="container">
  <div class="row">
    <div class="col">

    <?php if (isset($_GET['address'])): ?>
<?php
$address = $_GET['address'];
$token = $db->find($address);
$status = $token['status'];
?>
        <h4><?=$token['status']?></h4>
        <pre><code><?=htmlentities($token['content'])?></code></pre>

        <table class="table table-bordered">
            <tr>
                <th>Address</th>
                <th>Pay address</th>
                <th>Generated address</th>
            </tr>
            <tr>
                <td><?=$token['address']?></td>
                <td><?=$token['pay_address']?></td>
                <td><?=$token['gen_address']?></td>
            </tr>
        </table>

        <?php if ('CHECKED' === $status): ?>
            <p>Your token is checked</p>
        <?php elseif ('ACTIVATED' === $status): ?>
            <p>Your token is activated</p>
            <p>Your have <b><?=$token['hours']?></b> HOURS on <b><?=$token['address']?></b> (v1)</p>
            <p>Transmit any ammount (0.000001) 
                from <b><?=$token['address']?></b> (v2) 
                to <b><?=$token['gen_address']?></b> (v2) 
                and you will recieve <?=$hours  / $config['exchange']['ratio']?> NESS on your address <b><?=$token['pay_address']?></b> (v2) </p>
        <?php elseif ('PAYED' === $status): ?>
            <p>Your token is payed</p>
            <p><?=$hours  / $config['exchange']['ratio']?> NESS</p>
            <p>Check your balance at <b><?=$token['pay_address']?></b> (v2) </p>
        <?php endif; ?>
    <?php else: ?>
        <h1>The address is not given !</h1>
        <h3>    
            Use <code>token.php?address=your_privateness_token_address</code>
        </h3>
    <?php endif; ?>

    
    </div>
  </div>
  </div>

    <!-- Optional JavaScript; choose one of the two! -->

    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

    <!-- Option 2: Separate Popper and Bootstrap JS -->
    <!--
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
    -->
  </body>
</html>


