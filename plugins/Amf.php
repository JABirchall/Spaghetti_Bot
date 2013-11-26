<?php

require_once dirname(__FILE__) . '\core\amf\app\Gateway.php';
require_once AMFPHP_BASE . 'amf/io/AMFDeserializer.php';
require_once AMFPHP_BASE . 'amf/io/AMFSerializer.php';

function amfTree($amf) {
	foreach ($amf as $branch => $twig) {
		//$this->sendOutput("Successfuly Deserialized: " . $branch);
		$d =  ((is_array($twig)) ? $branch . " => " . amfTree($twig)  : $branch . " => " . $twig);
	}
	return $d;
}

class Amf extends Plugin {
	public function isTriggered() {
		if(!isset($this->info['text'])) {
			$this->sendOutput($this->CONFIG['usage']);
			return;
		}
		$this->info['text'] = str_replace(" ", "", $this->info['text']);
		$rawData = pack('H*',$this->info['text']);
		
		$amfRead = new AMFDeserializer($rawData);
		$a = $amfRead->readAmf3Data();
		$this->sendOutput("Packet ". $this->info['text'] . ".");

		foreach ($a as $branch => $twig) {
		$d =  $branch . " => " . $twig;
			if (is_array($twig)) {
				$b = $branch . "(Array) => ";
				foreach ($twig as $branch => $twig) {
					$d =  $b . $branch . " => " . $twig;
					//$this->sendOutput($d);
					if (is_array($twig)) {
						$b2 = $b . $branch . "(Array) => ";
						foreach ($twig as $branch => $twig) {
							$d =  $b2 . $branch . " => " . $twig;
							//$this->sendOutput($d);
							if (is_array($twig)) {
								$b3 = $b2 . $branch . "(Array) => ";
								foreach ($twig as $branch => $twig) {
									$d =  $b3 . $branch . " => " . $twig;
									//$this->sendOutput($d);
								}
							} else {
							$this->sendOutput($d);
							}
						}
					} else {
					$this->sendOutput($d);
					}
				}
			} else {
			$this->sendOutput($d);
			}
		}
		//$this->amfTree($a);
		$this->sendOutput("If you see fun stuff it worked. Contact DrWhat if you didn't.");
		return;
	}
	//public function amfTree($amf) {
	//	foreach ($amf as $branch => $twig) {
	//		$c =  $this->$b . $branch . " => " . $twig;
	//		if (is_array($twig)) {
	//			$this->amfTree($twig);
	//			//$b = $branch . "(Array) => ";
	//			//foreach ($twig as $branch => $twig) {
	//			//	$d =  $b . $branch . " => " . $twig;
	//			//$this->sendOutput($d);
	//			//}
	//		} else {
	//		$this->sendOutput($c);
	//		}
	//	}
	//	return;
	//}
}
