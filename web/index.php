<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <title>Privateness Exchange Form</title>
  </head>
  <body>

  <div class="container">
  <div class="row">
    <div class="col">
        <p>
            Create NVS record in Emercoin <br>
            key: <b>worm:token:ness_exchange_v1_v2:your_address_address-v1-with-coinhours</b> <br>
            value: 
            <pre>
                <code>
                    <?=htmlentities('
<worm>
    <token type="ness-exchange-v1-v2" address="your_address_address-v1-with-coinhours" pay_address="your_address_to-recieve-coins-v2"/>
</worm>');?>
                </code>
            </pre>
        </p>

        <form action="/token.php" method="GET">
        <div class="mb-3">
            <label for="token" class="form-label">Your token address</label>
            <input type="text" class="form-control" id="token" name="address" placeholder="your_address_address-v1-with-coinhours">
            <div id="emailHelp" class="form-text">The address in privateness v1 network, where you have coin-hours.</div>
        </div>

        <button type="submit" class="btn btn-primary">Find</button>
        </form>
    
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