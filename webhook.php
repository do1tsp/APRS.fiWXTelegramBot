<?php
include 'config.php';
$json = file_get_contents("php://input");
$aprs = json_decode($json, true);
$telegramtext = substr($aprs['message']['text'], 1); //Nur Ausgabe
if ($telegramtext != getWetter)
	{
		$aprsficall = $telegramtext;
	}

//Call und Api über conf
$weurl = 'https://api.aprs.fi/api/get?name=' . $aprsficall . '&what=wx&apikey=' . $aprsfiapikey . '&format=json';
$wecurl = curl_init();
$weheaders = array();
curl_setopt($wecurl, CURLOPT_HTTPHEADER, $weheaders);
curl_setopt($wecurl, CURLOPT_HEADER, 0);
curl_setopt($wecurl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($wecurl, CURLOPT_URL, $weurl);
curl_setopt($wecurl, CURLOPT_TIMEOUT, 30);
$wejson = curl_exec($wecurl);
curl_close($wecurl);
    
$wedata = json_decode($wejson);
$weakttemp      = number_format($wedata->entries[0]->temp ,1, ',', '');
$wewind         = number_format($wedata->entries[0]->wind_speed  ,1, ',', '');
$wewinddeg      = number_format($wedata->entries[0]->wind_direction  ,1, ',', '');
$wehumi         = number_format($wedata->entries[0]->humidity  ,1, ',', '');
$rain1			= number_format($wedata->entries[0]->rain_1h  ,1, ',', '');
$rain24		    = number_format($wedata->entries[0]->rain_24h  ,1, ',', '');
$rainmn			= number_format($wedata->entries[0]->rain_mn  ,1, ',', '');
$press          = number_format($wedata->entries[0]->pressure  ,1, ',', '');
$lum            = number_format($wedata->entries[0]->luminosity  ,1, ',', '');
$call           = $wedata->entries[0]->name;

$curl = curl_init();
curl_setopt($curl, CURLOPT_HTTPHEADER, array());
curl_setopt($curl, CURLOPT_HEADER, 0);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_TIMEOUT, 30);
$json = curl_exec($curl);
curl_close($curl);
    
$data = json_decode($json);
$weaktwasser =      number_format($data->timeseries[0]->currentMeasurement->value  ,0, '','' );
$weaktwassertrend = number_format($data->timeseries[0]->currentMeasurement->trend  ,0, '','' );
	
switch (TRUE){
	case ($weaktwassertrend<0):
		$weaktwassertrend = "\xE2\xAC\x87" ;
			break;
	case ($weaktwassertrend==0):
		$weaktwassertrend = "\xE2\xAC\x85";
			break;
	case ($weaktwassertrend>0):
		$weaktwassertrend = "\xE2\xAC\x86";
			break;

}

$text = "Wetterstation von " . $call .
 "\nTemperatur: " . $weakttemp . " °C" . 
 "\nWind: " . $wewind . " m/s"  .
 "\nWindrichtung: " . $wewinddeg . " °" .
 "\nLuftdruck: " . $press . " mbar" .	
 "\nFeuchtigkeit: " . $wehumi . " %" .
 "\nRegen 1h: "  . $rain1 .  " mm" .
 "\nRegen 24h: " . $rain24 . " mm" .
 "\nRegen MN: " .  $rainmn . " mm" .
 "\nHelligkeit: " .  $lum . " W/m²" ;


$chatIds = array($aprs["message"]["chat"]["id"]);
foreach($chatIds as $chatId)
	{
		//Text senden
    $ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL, "https://api.telegram.org/bot" . $token . "/sendMessage" );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt( $ch, CURLOPT_POST, 1 );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, array(
			'chat_id'    => $chatId,
			'text'       => $text ,
			'parse_mode' => 'HTML'
		) );
		$result = curl_exec( $ch );
	
        if ( curl_errno( $ch ) )
		{
			echo 'Error:' . curl_error( $ch );
		}
		curl_close( $ch );
        /*Bild senden
    $ch = curl_init();
        curl_setopt( $ch, CURLOPT_URL, "https://api.telegram.org/bot" . $token . "/sendPhoto" );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt( $ch, CURLOPT_POST, 1 );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, array(
			'chat_id'    => $chatId,
			'photo'    => $bild,
		) );
		$result = curl_exec( $ch );
		if ( curl_errno( $ch ) )
		{
			echo 'Error:' . curl_error( $ch );
		}
		curl_close( $ch );	*/
        //Bot Trigger Nachricht löschen
	/*$ch = curl_init();
        curl_setopt( $ch, CURLOPT_URL, "https://api.telegram.org/bot" . $token . "/deleteMessage" );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt( $ch, CURLOPT_POST, 1 );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, array(
			'chat_id'    => $chatId,
			'message_id'    => $getmessagedelete
		) );
		$result = curl_exec( $ch );
		if ( curl_errno( $ch ) )
		{
			echo 'Error:' . curl_error( $ch );
		}
		curl_close( $ch );*/
	}
?>