<?php
require '../lib/DB.php';

ini_set('display_errors', true);

use lib\DB;

$config = require '../config/config.php';
$db = new DB($config['db']['host'], $config['db']['database'], $config['db']['user'], $config['db']['password']);

?>

<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <title>Tokens</title>
  </head>
  <body>


  <div class="container">
  <div class="row">
    <div class="col">

        <table class="table table-bordered table-striped-columns">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Address</th>
                    <th>Pay address</th>
                    <th>Generated address</th>
                    <th>Hours</th>
                    <th>Error</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($db->findAll() as $token): ?>
                <tr>
                    <td><?=$token['ID']?></td>
                    <td><?=$token['address']?></td>
                    <td><?=$token['pay_address']?></td>
                    <td><?=$token['gen_address']?></td>
                    <td><?=$token['hours']?></td>
                    <td><?=$token['error']?></td>
                    <?php if ('CHECKED' === $token['status']): ?>
                    <td style="font-weight: bold;"><?=$token['status']?></td>
                    <?php elseif ('ACTIVATED' === $token['status']): ?>
                    <td style="font-weight: bold; color:yellow;"><?=$token['status']?></td>
                    <?php elseif ('PAYED' === $token['status']): ?>
                    <td style="font-weight: bold; color:green;"><?=$token['status']?></td>
                    <?php elseif ('ERROR' === $token['status']): ?>
                    <td style="font-weight: bold; color:red;"><?=$token['status']?></td>
                    <?php endif; ?>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    
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
