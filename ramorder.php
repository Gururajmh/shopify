 <?php 
 $connect=mysqli_connect("herennowidentifier.clns7dnu70de.us-west-2.rds.amazonaws.com;port=3306","herennowdb","herennowpass","guru");
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