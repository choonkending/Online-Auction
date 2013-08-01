<?php

// Description	: process.php - Server side processing for processing auction items
// Author		: Choon Ken Ding
// Date			: 21 May 2013
// Validated	: OK 21 May 2013

header('Content-Type: text/xml');	
	
	
	// Check if auction file exists
	$title = "../../data/auction.xml";
	
	$doc = new DomDocument("1.0");
	
	// check if file exists
	if( file_exists($title) ){
		$doc->load($title);
		$root = $doc->documentElement;
		$auctions = $root->getElementsByTagName("auction");
		
		foreach( $auctions as $auction){
			$status = $auction->getElementsByTagName("status")->item(0)->nodeValue;
			if( $status == "in_progress" ){
				$reservePrice = $auction->getElementsByTagName("reservePrice")->item(0)->nodeValue;
				$startDate = $auction->getElementsByTagName("startDate")->item(0)->nodeValue;
				$startTime = $auction->getElementsByTagName("startTime")->item(0)->nodeValue;
				$durDay = $auction->getElementsByTagName("duration")->item(0)->getElementsByTagName("day")->item(0)->nodeValue;
				$durHour = $auction->getElementsByTagName("duration")->item(0)->getElementsByTagName("hour")->item(0)->nodeValue;
				$durMin = $auction->getElementsByTagName("duration")->item(0)->getElementsByTagName("min")->item(0)->nodeValue;
				$curBid = $auction->getElementsByTagName("bid")->item(0)->getElementsByTagName("bidPrice")->item(0)->nodeValue;
				
				$start = $startDate . " " . $startTime;
				$endBid = $start . " +" . $durDay . " days " . $durHour . " hours " . $durMin . " minutes";
				$endBidTime = strtotime($endBid);
				$endDate = date("Y-m-d H:i:s",$endBidTime);
				$endDiff = $endBidTime - strtotime("now");
				$remainTime = date("d H:i:s",$endDiff);

				// if difference between the time the bid ends and the current time is a positive value, 
				// this means there is still time remaining
				if( $endDiff <= 0 ){
					if( $curBid >= $reservePrice){
						$auction->getElementsByTagName("status")->item(0)->nodeValue = "sold";
					}else{
						$auction->getElementsByTagName("status")->item(0)->nodeValue = "failed";
					}
				}
			}
		}
		// save changes to the XML file
		$doc->save($title);
		// processing is complete
		echo "200";
	}else{
		echo "100";
	}

?>