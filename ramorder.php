<?php
        session_start();

        require __DIR__.'/vendor/autoload.php';
        use phpish\shopify;

        require __DIR__.'/conf.php';

        $shopify = shopify\client($_SESSION['shop'], SHOPIFY_APP_API_KEY, $_SESSION['oauth_token']);

        ?>
<form action="" method="post">
        Enter mobile number
        <input type="text" name="mobile"> OR
        Enter E-mail address
        <input type="text" name="email">
        <br>
        <input type="submit" name="submit" value="Check order">
        </form>


        <?php
        try
        {

                # Making an API request can throw an exception
                $shop = $shopify('GET /admin/shop.json');
                echo "welcome ".$shop['name'];echo '<br><br>';
                $products = $shopify('GET /admin/products.json', array('published_status'=>'published'));
                $orders = $shopify('GET /admin/orders.json?status=any');
                $order_count = $shopify('GET /admin/orders/count.json');
                $ordeer_filed = $shopify('GET /admin/orders.json?fields=id,name,customer,email,total-price,created_at,phone,fulfillments&status=any');

                if(isset($_POST['submit']))
                {

                        $mobile = $_POST['mobile'];
                        $email = $_POST['email'];
                        echo $mobile;echo "<br>";
                        echo $email;

                }
                echo "<table style='align:center'>";
                echo "<th>Order ID</th>";echo "<th>Name</th>";echo "<th>Email</th>";echo "<th>Total Price</th>";echo "<th>Date</th>";echo "<th>Phone</th>";
                foreach ($orders as $order) {
                        $st = strcmp($email,$order['email']);
                        if($st==0){
                        echo "<tr>";
                                echo "<td align='center'>";print_r($order['name']);echo "</td>";
                                echo "<td align='center'>";print_r($order['customer']['first_name']);echo " ";print_r($order['customer']['last_name']);echo "</td>";
                                echo "<td align='center'>";print_r($order['email']);echo "</td>";
                                echo "<td align='center'>";print_r($order['total_price']);echo "</td>";
                                echo "<td align='center'>";print_r($order['created_at']);echo "</td>";
                                echo "<td align='center'>";print_r($order['shipping_address']['phone']);echo "</td>";
                        echo "</tr>";
                        }

                }
                echo "</table>";
//               $connec=mysqli_connect("herennowidentifier.clns7dnu70de.us-west                                                                                                                -2.rds.amazonaws                                                                                                                                                                                                                                .com:3306","herennowdb","herenno                                                                                                                wpass","test");
//               print_r($connec);
//               echo "error";exit;
//     $q2="INSERT INTO `orders` (`order_id`, `name`, `email`, `mobile_number`)                                                                                                                 VALUES ('1', 'ka', 'ka', '1');";
// if($connect->query($q2))
//     print_r("inserted");exit;
                try {
                        $hostname='herennowidentifier.clns7dnu70de.us-west-2.rds.amazonaws.com;port=3306';
                        $username='herennowdb';
                        $password='herennowpass';
                        $dbh = new PDO("mysql:host=$hostname;dbname=guru",$username,$password);

                        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // <== add th is line
                        echo 'Connected to Database<br/>';

//     $sql = "SELECT * FROM orders";
//    // print_r($dbh);exit;
//     echo "order id";echo "name";
// foreach ($dbh->query($sql) as $row)
//     {
//     echo $row["order_id"] ." - ". $row["name"] ."<br/>";
//     }


    $dbh = null;
    }
catch(PDOException $e)
    {
    echo $e->getMessage();
    }
//                      $dbhost = 'herennowidentifier.clns7dnu70de.us-west-2.rds                                                                                                                .amazonaws.com';
//  $dbport = '3306';
//  $dbname = 'test';
//  $charset = 'utf8' ;

//  $dsn = "mysql:host={$dbhost};port={$dbport};dbname={$dbname};charset={$chars                                                                                                                et}";
//  $username = 'herennowdb';
//  $password = 'herennowpass';
// print_r($dsn);exit;
//  $pdo = new PDO($dsn, $username, $password);
//  print_r($pdo);exit;
//                      $link = mysqli_connect($dbhost, $username, $password, $d                                                                                                                bname, $dbport);
//                      if (!$link) {
//     printf("Error: %s\n", mysqli_error($link));
//     exit();
//                      print_r($link);exit;

}
        catch (shopify\ApiException $e)
        {
                # HTTP status code was >= 400 or response contained the key 'err                                                                                                                ors'
                echo $e;
                print_r($e->getRequest());
                print_r($e->getResponse());
        }
        catch (shopify\CurlException $e)
        {
                # cURL error
                echo $e;
                print_r($e->getRequest());
                print_r($e->getResponse());
        }
  ?>
<script>var osURL= 'https://shipway.in/orderscan?key=ZHJpNlJBR0Fpbm5SL0oxaDdmNXUxbTVKT0xMMk9NeVhZaG53ZjVsZ0R0QlZZTE84L3JwQzNTZXZaWVpQdlM5TjBuQkhGbjVrbWVPL0RwbmpvR1RzUHc9PQ==&layout=order_id';document.write('<scr'+'ipt type="text/JavaScript" src="https://shipway.in/orderscan/widget/widget.js"></scr'+'ipt>')</script>
<!-- Place this div where you want the tracking widget -->
<div id="ship_oscan_main_content"></div>

<?php
$webhookContent = "";

$webhook = fopen('php://input' , 'rb');
while (!feof($webhook)) {
    $webhookContent .= fread($webhook, 4096);
}
fclose($webhook);

error_log($webhookContent);
?>