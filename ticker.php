<?php
session_start();

$data = array();

// refresh data after one minute
if(isset($_SESSION["timestamp"]) && ((time()-$_SESSION["timestamp"]) < 60))
	$data = $_SESSION["data"];
else
{
	$new_data = update();
	// we might get wrong data sometimes, so check it before update to new data
	$data["btc_usd"] = is_numeric($new_data["btc_usd"])?$new_data["btc_usd"]:$data["btc_usd"];
	$data["ltc_usd"] = is_numeric($new_data["ltc_usd"])?$new_data["ltc_usd"]:$data["ltc_usd"];
	$data["ltc_btc"] = is_numeric($new_data["ltc_btc"])?$new_data["ltc_btc"]:$data["ltc_btc"];
	$data["nvc_btc"] = is_numeric($new_data["nvc_btc"])?$new_data["nvc_btc"]:$data["nvc_btc"];
	$data["ftc_btc"] = is_numeric($new_data["ftc_btc"])?$new_data["ftc_btc"]:$data["ftc_btc"];
	$data["doge_btc"] = is_numeric($new_data["doge_btc"])?$new_data["doge_btc"]:$data["doge_btc"];
	$data["max_btc"] = is_numeric($new_data["max_btc"])?$new_data["max_btc"]:$data["max_btc"];
	$_SESSION["timestamp"] = time();
	$_SESSION["data"] = $data;
}

$arr = array("BTC_USD" => $data["btc_usd"],
	     "LTC_USD" => $data["ltc_usd"],
	     "LTC_BTC" => $data["ltc_btc"],
	     "NVC_BTC" => $data["nvc_btc"],
	     "FTC_BTC" => $data["ftc_btc"],
	     "DOGE_BTC" => $data["doge_btc"],
	     "MAX_BTC" => $data["max_btc"],
	     "NVC_USD" => $data["nvc_btc"]*$data["btc_usd"],
	     "FTC_USD" => $data["ftc_btc"]*$data["btc_usd"],
	     "DOGE_USD" => $data["doge_btc"]*$data["btc_usd"],
	     "MAX_USD" => $data["max_btc"]*$data["btc_usd"],
);

echo json_encode($arr);

function update()
{
	$ch = null;
	$url = "http://s1.bitcoinwisdom.com:8080/ticker";
	if (is_null($ch))
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; PHP client; '.php_uname('s').'; PHP/'.phpversion().')');
		$_SESSION["ch"] = ch;
	}
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	
	$res = curl_exec($ch);
	$dec = json_decode($res, true);
	
	$btc_usd = $dec["btcebtcusd"]["last"];
	$ltc_usd = $dec["btceltcusd"]["last"];
	$ltc_btc = $dec["btceltcbtc"]["last"];
	
/*
	$market_id = "5"; // FTC/BTC
	$url = "http://pubapi.cryptsy.com/api.php?method=singlemarketdata&marketid=";
	curl_setopt($ch, CURLOPT_URL, $url.$market_id);
	$res = curl_exec($ch);
	$dec = json_decode($res, true);
	if( $dec["success"] == 1)
		$ftc_btc = $dec["return"]["markets"]["FTC"]["lasttradeprice"];
	sleep(3);
	
	$market_id = "13"; // NVC/BTC
	$url = "http://pubapi.cryptsy.com/api.php?method=singlemarketdata&marketid=";
	curl_setopt($ch, CURLOPT_URL, $url.$market_id);
	$res = curl_exec($ch);
	$dec = json_decode($res, true);
	if( $dec["success"] == 1)
		$nvc_btc = $dec["return"]["markets"]["NVC"]["lasttradeprice"];
	sleep(3);
	
	$market_id = "132"; // DOGE/BTC
	$url = "http://pubapi.cryptsy.com/api.php?method=singlemarketdata&marketid=";
	curl_setopt($ch, CURLOPT_URL, $url.$market_id);
	$res = curl_exec($ch);
	$dec = json_decode($res, true);
	if( $dec["success"] == 1)
		$doge_btc = $dec["return"]["markets"]["DOGE"]["lasttradeprice"];
 */
	$url = "http://pubapi.cryptsy.com/api.php?method=marketdatav2";
	curl_setopt($ch, CURLOPT_URL, $url);
	$res = curl_exec($ch);
	$dec = json_decode($res, true);
	if( $dec["success"] == 1)
	{
		$ftc_btc = $dec["return"]["markets"]["FTC/BTC"]["lasttradeprice"];
		$nvc_btc = $dec["return"]["markets"]["NVC/BTC"]["lasttradeprice"];
		$doge_btc = $dec["return"]["markets"]["DOGE/BTC"]["lasttradeprice"];
		$max_btc = $dec["return"]["markets"]["MAX/BTC"]["lasttradeprice"];
	}
	

	$data = array("btc_usd" => $btc_usd,
		      "ltc_usd" => $ltc_usd,
		      "ltc_btc" => $ltc_btc,
		      "ftc_btc" => $ftc_btc,
		      "nvc_btc" => $nvc_btc,
		      "doge_btc" => $doge_btc,
		      "max_btc" => $max_btc,
	      );
	return $data;
}
?>
