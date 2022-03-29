<?php
header('Access-Control-Allow-Origin: *');

header('Access-Control-Allow-Methods: GET, POST');

header("Access-Control-Allow-Headers: X-Requested-With");

$dataset = json_decode(file_get_contents("https://covid19.ddc.moph.go.th/api/Cases/today-cases-all"),true);
$dataset = $dataset[0];
if(isset($_GET["act"])){
if($_GET["act"]=="save"){
$data = array();
if(file_exists("db.json")){
$myfile = fopen("db.json", "r") or die("Unable to open file!");
$data = json_decode(fread($myfile,filesize("db.json")),true);
fclose($myfile);
}

for($i=0; $i<=count($data)-1; $i++){
if($_POST["token"]==$data[$i]["token"]){
echo json_encode(array("statusCode"=>400,"Message"=>"Cannot Save Token"));
exit();
}
}
$createToken =md5(json_encode(array("date"=>date("Y-m-d h:i:s"),"token"=>$_POST["token"])));
array_push($data,array("token"=>$_POST["token"],"id"=>$createToken,"value"=>$dataset["txn_date"]));

$myfile = fopen("db.json", "w") or die("Unable to open file!");
fwrite($myfile, json_encode($data));
fclose($myfile);

ini_set('display_errors', 1);

ini_set('display_startup_errors', 1);

error_reporting(E_ALL);

date_default_timezone_set("Asia/Bangkok");


// เอา token จากที่เรา gen ขึ้นมา

$sToken = $_POST["token"];


$sMessage ="จำนวนผู้ติดโควิดประจำวันที่ : ".$dataset["txn_date"]."\n";
$sMessage .="ผู้ติดเชื้อใหม่ : ".$dataset["new_case"]."\n";
$sMessage .="เสียชีวิตวันนี้ : ".$dataset["new_death"]."\n";
$sMessage .="เสียชีวิตรวม : ".$dataset["total_death"]."\n";
$sMessage .="ผู้ป่วยสะสมทั้งหมด : ".$dataset["total_case"]."\n";

$chOne = curl_init(); 

curl_setopt( $chOne, CURLOPT_URL, "https://notify-api.line.me/api/notify");

curl_setopt( $chOne, CURLOPT_SSL_VERIFYHOST, 0); 

curl_setopt( $chOne, CURLOPT_SSL_VERIFYPEER, 0); 

curl_setopt( $chOne, CURLOPT_POST, 1); 

curl_setopt( $chOne, CURLOPT_POSTFIELDS, "message=".$sMessage); 

$headers = array( 'Content-type: application/x-www-form-urlencoded', 'Authorization: Bearer '.$sToken.'', );

curl_setopt($chOne, CURLOPT_HTTPHEADER, $headers); 

curl_setopt( $chOne, CURLOPT_RETURNTRANSFER, 1);

$result = curl_exec( $chOne ); 


//Result error 

if(curl_error($chOne)) 

{ 

// echo 'error:' . curl_error($chOne);

} 

else { 

// $result_ = json_decode($result, true); 

// echo "status : ".$result_['status']; echo "message : ". $result_['message'];

} 

curl_close( $chOne );

echo json_encode(array("statusCode"=>200));
}else if($_GET["act"]=="detail"){

$data= array();
if(file_exists("db.json")){
$myfile = fopen("db.json", "r") or die("Unable to open file!");
$data = json_decode(fread($myfile,filesize("db.json")),true);
fclose($myfile);
}

for($i=0; $i<=count($data)-1; $i++){
if($data[$i]["value"]!=$dataset["txn_date"]){
$data[$i]["value"] = $dataset["txn_date"];
ini_set('display_errors', 1);

ini_set('display_startup_errors', 1);

error_reporting(E_ALL);

date_default_timezone_set("Asia/Bangkok");


// เอา token จากที่เรา gen ขึ้นมา

$sToken = $data[$i]["token"];


$sMessage ="จำนวนผู้ติดโควิดประจำวันที่ : ".$dataset["txn_date"]."\n";
$sMessage .="ผู้ติดเชื้อใหม่ : ".$dataset["new_case"]."\n";
$sMessage .="ตายวันนี้ : ".$dataset["new_death"]."\n";
$sMessage .="ตายรวม : ".$dataset["total_death"]."\n";
$sMessage .="ผู้ป่วยสะสมทั้งหมด : ".$dataset["total_case"]."\n";

$chOne = curl_init(); 

curl_setopt( $chOne, CURLOPT_URL, "https://notify-api.line.me/api/notify");

curl_setopt( $chOne, CURLOPT_SSL_VERIFYHOST, 0); 

curl_setopt( $chOne, CURLOPT_SSL_VERIFYPEER, 0); 

curl_setopt( $chOne, CURLOPT_POST, 1); 

curl_setopt( $chOne, CURLOPT_POSTFIELDS, "message=".$sMessage); 

$headers = array( 'Content-type: application/x-www-form-urlencoded', 'Authorization: Bearer '.$sToken.'', );

curl_setopt($chOne, CURLOPT_HTTPHEADER, $headers); 

curl_setopt( $chOne, CURLOPT_RETURNTRANSFER, 1);

$result = curl_exec( $chOne ); 


//Result error 

if(curl_error($chOne)) 

{ 

// echo 'error:' . curl_error($chOne);

} 

else { 

// $result_ = json_decode($result, true); 

// echo "status : ".$result_['status']; echo "message : ". $result_['message'];

} 

curl_close( $chOne );
}
}


$myfile = fopen("db.json", "w") or die("Unable to open file!");
fwrite($myfile, json_encode($data));
fclose($myfile);
echo json_encode(array("statusCode"=>200));

}



}else{
echo "not Param";
}

?>
