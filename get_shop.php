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
    $connect=mysqli_connect("localhost","root","","shopify");
    $q="SELECT * FROM `order`"; 
    $result = mysqli_query($connect,$q);
    print_r($connect);
    if(!$result)
    {
    	printf("Error:%s\n",mysqli_error($connect));
    	exit();
    }
    While ($rows = mysqli_fetch_array($result))
    {
	 $Order_ID = $rows['Order_ID'];
       $Date = $rows['Date'];
       $Customer_Name = $rows['Customer Name'];
       $Payment_Status = $rows['Payment_Status'];
       $Fulfillment_Status = $rows['Fulfillment_Status'];
       $Phone_Number = $rows['Phone_Number'];
       $Email_ID = $rows['Email_ID'];
       $Total = $rows['Total'];
      


       echo "$Order_ID<br>$Date<br>$Customer_Name<br>$Payment_Status<br>$Fulfillment_Status<br>$Phone_Number<br>$Email_ID<br>$Total<br><br>";

       }
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
