<?php
include('session.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
  <title>AQUA</title>

  <!-- Bootstrap -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.0/css/materialize.min.css">
  <link href="css/style.css" rel ="stylesheet">
  <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->
</head>
<body class="cyan">

  <nav>
    <div class="nav-wrapper white">
      <a href="#" class="brand-logo center" style="color:#00BCD4;">Waterworks</a>
      <ul id="nav-mobile" class="right hide-on-med-and-down">
        <li><a style="color:#00BCD4;">Hello </a></li>

        <li><a href="../vendor/logout.php"  style="color:#00BCD4;">Logout</a></li>
      </ul>
    </div>

  </nav>

  <div class="container">
    <div class="row">
      <div class="col s6">
        <form action='submitorder.php' method="post">

          <input type='text' name="pid" value="<?php echo $_GET["type"]?>" hidden >
          <input type='text' name="quantity" value="<?php echo $_GET["quantity"]?>" hidden >
          <?php
          $zip=$_GET["zip"];
          $type=$_GET["type"];
          $quantity=$_GET["quantity"];

          $servername = "localhost";
          $username = "root";
          $password = "";
          $dbname = "aqua";

          // Create connection
          $conn = new mysqli($servername, $username, $password, $dbname);
          // Check connection
          if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
          }
          $sql = "SELECT * FROM `vendor` where vzip='$zip'";
          $result = $conn->query($sql);
          if ($result->num_rows > 0) {
            // output data of each row
            while($row = $result->fetch_assoc()) {

              $sr=$row["vid"];
              $emid=$row["email"];
              $name=$row["name"];
              $sql = "SELECT `$type` FROM `product` where vid='$sr'";
              $result = $conn->query($sql);
              if ($result->num_rows > 0) {
                echo"
                <table class='bordered white' style ='margin-top:10px;border-radius:3px;''>
                <thead>
                <tr>
                <th data-field='units'>Choose</th>
                <th data-field='pid'>Vendor Name</th>
                <th data-field='quantity'>Email</th>

                </tr>
                </thead>
                ";
                while($row = $result->fetch_assoc()) {
                  if($row[$type]>=$quantity)
                  {
                    echo"<tr><td>
                    <p>
                    <input type='checkbox' id='test5' name='vendor' value='$sr' />
                    <label for='test5'>Choose</label>
                    </p>
                    </td>";
                    echo"<td>".$name."</td><td>".$emid."</td></tr>";

                  }
                  else  {

                    echo"No Vendors Available1";
                  }
                }
              }
              else {

                echo"No Vendors Available";
              }
            }





          }
          else {

            echo"No Merchant Found";
          }
          echo"</table>";
          $conn->close();
          ?>



        </div>
        <div class="card-panel" >

          <div class="row">

            <div class="row">
              <div class="input-field col s6">
                <textarea id="textarea1" name="address" class="materialize-textarea"></textarea>
                <label for="textarea1">Enter Delivery Address</label>
              </div>
            </div>

      <div class="row">
        <div class="input-field col s6">
          <input type="date" class="datepicker">
          <label for="textarea1">Enter To Date</label>
        </div>
      </div>
  </div>


  <!-- PayPal Logo --><table border="0" cellpadding="10" cellspacing="0" align="center"><tr><td align="center"></td></tr><tr><td align="center"><a href="https://www.paypal.com/webapps/mpp/paypal-popup" title="How PayPal Works" onclick="javascript:window.open('https://www.paypal.com/webapps/mpp/paypal-popup','WIPaypal','toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=yes, width=1060, height=700'); return false;"><img src="https://www.paypalobjects.com/webstatic/mktg/logo/bdg_payments_by_pp_2line.png" border="0" alt="Payments by PayPal"></a><div style="text-align:center"><a href="https://www.paypal.com/webapps/mpp/how-paypal-works"><font size="2" face="Arial" color="#0079CD"></font></div></td></tr></table><!-- PayPal Logo -->
    <button type="submit" class="waves-effect waves-light btn " hidden>Proceed For Payment</button></form>
          <!--<button type="submit" class="waves-effect waves-light btn">Proceed For Payment</button></form>  -->
          <div class="row">
            <?php
            $data=array(
              'merchant_email'=>'admin@aqua.com',
              'product_name'=>'Demo Product',
              'amount'=>20.50,
              'currency_code'=>'USD',
              'thanks_page'=>"http://".$_SERVER['HTTP_HOST'].'paypal/thank.php',
              'notify_url'=>"http://".$_SERVER['HTTP_HOST'].'paypal/ipn.php',
                'cancel_url'=>"http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'],
                'paypal_mode'=>true,
              );
              ?>
              <form id='paypal-info' method='post' action='#'>
                <label>Product Name : <?php echo $data['product_name']; ?></label></br>
                <label>Product Price : <?php echo $data['currency_code']; ?></label>

                <input type='submit' name='pay_now' id='pay_now' value='Pay' />
              </form>
              <?php

              if(isset($_POST['pay_now'])){
                echo infotutsPaypal($data);

              }

              function infotutsPaypal( $data) {

                define( 'SSL_URL', 'https://www.paypal.com/cgi-bin/webscr' );
                define( 'SSL_SAND_URL', 'https://www.sandbox.paypal.com/cgi-bin/webscr' );

                $action = '';
                //Is this a test transaction?
                $action = ($data['paypal_mode']) ? SSL_SAND_URL : SSL_URL;

                $form = '';

                $form .= '<form name="frm_payment_method" action="' . $action . '" method="post">';
                $form .= '<input type="hidden" name="business" value="' . $data['merchant_email'] . '" />';
                // Instant Payment Notification & Return Page Details /
                $form .= '<input type="hidden" name="notify_url" value="' . $data['notify_url'] . '" />';
                $form .= '<input type="hidden" name="cancel_return" value="' . $data['cancel_url'] . '" />';
                $form .= '<input type="hidden" name="return" value="' . $data['thanks_page'] . '" />';
                $form .= '<input type="hidden" name="rm" value="2" />';
                // Configures Basic Checkout Fields -->
                $form .= '<input type="hidden" name="lc" value="" />';
                $form .= '<input type="hidden" name="no_shipping" value="1" />';
                $form .= '<input type="hidden" name="no_note" value="1" />';
                // <input type="hidden" name="custom" value="localhost" />-->
                $form .= '<input type="hidden" name="currency_code" value="' . $data['currency_code'] . '" />';
                $form .= '<input type="hidden" name="page_style" value="paypal" />';
                $form .= '<input type="hidden" name="charset" value="utf-8" />';
                $form .= '<input type="hidden" name="item_name" value="' . $data['product_name'] . '" />';
                $form .= '<input type="hidden" value="_xclick" name="cmd"/>';
                $form .= '<input type="hidden" name="amount" value="' . $data['amount'] . '" />';
                $form .= '<script>';
                $form .= 'setTimeout("document.frm_payment_method.submit()", 2);';
                $form .= '</script>';
                $form .= '</form>';
                return $form;
              }
              ?>
            </div>
            </div>
        </div>

    </div>
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->

    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.0/js/materialize.min.js"></script>
    <script src="js/script.js"></script>
    <script>$('.datepicker').pickadate({
        selectMonths: true, // Creates a dropdown to control month
        selectYears: 15 // Creates a dropdown of 15 years to control year
      });
           </script>
  </body>
  </html>
