<?php
	session_start();

	require __DIR__.'/vendor/autoload.php';
	use phpish\shopify;

	require __DIR__.'/conf.php';

	$shopify = shopify\client($_SESSION['shop'], SHOPIFY_APP_API_KEY, $_SESSION['oauth_token']);

	try
	{
		# Making an API request can throw an exception
		// $shop = $shopify('GET /admin/shop.json');
		// print_r($shop);
    $dbhost = 'localhost';
    $dbuser = 'root';
    $dbpass = '';
    $connect=mysql_connect("localhost","root","");

    // connect to databsase 

    mysql_select_db("shopify",);

       // enter code here

    // query the database 

    $query = mysql_query("SELECT * FROM order");

    // fetch the result / convert resulte in to array 
    $rows = mysql_fetch_array($query);
    print_r($rows);
    exit;

    WHILE ($rows = mysql_fetch_array($query)):

       $Order_ID = $rows['Order_ID'];
       $Date = $rows['Date'];
       $Customer_Name = $rows['Customer_Name'];
       $Payment_Status = $rows['Payment_Status'];
       $Fulfillment_Status = $rows['Fulfillment_Status'];
       $Phone_Number = $rows['Phone_Number'];
       $Email_ID = $rows['Email_ID'];
       $Total = $rows['Total'];


       echo "$Order_ID<br>$Date<br>$Customer_Name<br>$Payment_Status<br>$Fulfillment_Status<br>$Phone_Number<br>$Email_ID<br>$Total<br><br>";

       endwhile;
	}
	catch (shopify\ApiException $e)
	{
		# HTTP status code was >= 400 or response contained the key 'errors'
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
