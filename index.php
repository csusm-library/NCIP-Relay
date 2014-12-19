<?php 
$thismonth = date("Y-m");
$logfile = "ncip" . $thismonth . ".log";
$dataPOST = trim(file_get_contents('php://input'));
$dataPOST = str_replace("<INN-REACH SITE CODE>", "<ALMA INSTITUTION ID>", $dataPOST);
file_put_contents($logfile, "\r\n\r\nREQUEST:" . date("Y-m-d G:i:sa") . "\r\n" . $dataPOST, FILE_APPEND);

$ch = curl_init();
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml'));
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_URL, 'https://na01.alma.exlibrisgroup.com:443/view/NCIPServlet/v1');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $dataPOST);
$response = curl_exec($ch);
$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
$header = substr($response, 0, $header_size);
$body = substr($response, $header_size);
$response = str_replace("<ALMA INSTITUTION ID>", "<INN-REACH SITE CODE>", $response);
$response = str_replace("<Scheme>NCIP Unique Agency Id</Scheme>", "<Scheme>http://<INN-REACH SERVER IP>:<PORT>/IRCIRCD?target=get_scheme_values&amp;scheme=UniqueAgencyId</Scheme>", $response);
$response = str_replace("<ALMA INSTITUTION NAME>", "<INN-REACH SITE CODE>", $response);
if (strstr($dataPOST, "ItemRequested")){
	$response = str_replace("<Response>", "<ItemRequestedResponse>", $response);
	$response = str_replace("</Response>", "</ItemRequestedResponse>", $response);
}
file_put_contents($logfile, "\r\n\r\nRESPONSE:" . date("Y-m-d G:i:sa") . "\r\n" . $response, FILE_APPEND);
echo $response;
curl_close($ch);
?>