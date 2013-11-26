<?php
class hort extends Plugin {

	function isTriggered() {

		if(!isset($this->info['text'])) {
			$this->sendOutput($this->CONFIG['usage']);
			return;
		}

		$shuffle = array("heads","tails");
		$result = array_rand($shuffle);
		$bet = explode(" ", $this->info['text']);
		if(!$this->userInDb()) $this->insertUser();
		if (!(int)$bet[1]){
			$this->sendOutput("Please choose amount to bet");
			return;
		} else {
			if ($bet[0] != "heads" && $bet[0] != "tails"){
				$this->sendOutput("You idiot, The game is heads or tails! You loss by default!");
			}
			$this->sendOutput($this->info['nick'] . " You bet $" . (int)$bet[1] . " on " . $bet[0]);
			if (!$this->check($bet[1])) {
				$this->sendOutput("You dont have enough funds to play");
			} else {
				if($result == 0) {
					$result = "heads";
					if ($result == $bet[0]) {
						$this->sendOutput("You won");
						$this->win((int)$bet[1]);
					} else {
						$this->sendOutput("You lost");
						$this->lost((int)$bet[1]);
					}
				} else if ($result == 1) {
					$result = "tails";
					if ($result == $bet[0]) {
						$this->sendOutput("You won");
						$this->win((int)$bet[1]);
					} else {
						$this->sendOutput("You lost");
						$this->lost((int)$bet[1]);
					}
				}
			}
		}
	}

	function userInDb() {
		$res = $this->MySQL->sendQuery("SELECT 1 FROM hort WHERE nick='" . $this->info['nick'] . "'");
		if($res['count'] == 0) return false;
	return true;
	}
	function win($a) {
		$res = $this->MySQL->sendQuery("UPDATE hort SET money = money + " . $a . " WHERE nick= '" . $this->info['nick'] . "'");
	return true;
	}
	function lost($a) {
		$res = $this->MySQL->sendQuery("UPDATE hort SET money = money - " . $a . " WHERE nick= '" . $this->info['nick'] . "'");
	return true;
	}
	function check($a) {
		$res = $this->MySQL->sendQuery("SELECT * FROM `hort` where nick = '" . $this->info['nick'] . "' and money >= " . $a . "");
		if($res['count'] == 0) return false;
	return true;
	}
	function insertUser() {
		$this->MySQL->sendQuery("INSERT INTO hort (nick,money) VALUES ('" . $this->info['nick'] . "',100)");
	return true;
	}
}
