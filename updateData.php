<?php
error_reporting(E_ALL); 
ini_set("display_errors", 1); 
include "db.php";
if(isset($_GET['barCode'])){
	$con = connect();
  if($_GET['action']=="Entrega")
  {
    $serviceNum=$_GET['serviceNum'];
    $barCode=$_GET['barCode'];
    $dateDeliver= date("Y-m-d");
    $texStatus=$_GET['texStatus'];

    $consulta="UPDATE Servicios SET barCode='$barCode',dateDeliver='$dateDeliver',texStatus='$texStatus' WHERE serviceNum='$serviceNum'";
    //$consulta="UPDATE Servicios SET barCode='$barCode', limitPay=$limitPay, dateIntro=$dateIntro, texStatus=$texStatus, orderGrup=$orderGrup WHERE serviceNum=$serviceNum";
    mysqli_query($con,$consulta);
    mysqli_close($con);
  }
  else
  {
     $serviceNum=$_GET['serviceNum'];
      $barCode=$_GET['barCode'];
      $pay=$_GET['pay'];
      $dateIntro= date("Y-m-d");
      $texStatus=$_GET['texStatus'];
      $orderGrup=$_GET['orderGrup'];
      $limitPay=$_GET['limitPay'];

      $consulta="UPDATE Servicios SET barCode='$barCode', pay='$pay', limitPay='$limitPay',dateIntro='$dateIntro',texStatus='$texStatus',orderGrup='$orderGrup' WHERE serviceNum='$serviceNum'";
      //$consulta="UPDATE Servicios SET barCode='$barCode', limitPay=$limitPay, dateIntro=$dateIntro, texStatus=$texStatus, orderGrup=$orderGrup WHERE serviceNum=$serviceNum";
      mysqli_query($con,$consulta);
      mysqli_close($con);
  }

 
  mysqli_close($con);
  $con = connect();
   $result = mysqli_query($con,"SELECT * FROM Servicios WHERE serviceNum=$serviceNum");

 while($row = mysqli_fetch_array($result))
 {
    $msg =$msg."    ".$row['barCode'];
  }
}
mysqli_close($con);
echo $msg;
  


?>