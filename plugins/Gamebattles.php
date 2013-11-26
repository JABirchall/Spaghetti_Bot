<?php

//$this->info['text']);

class Gamebattles extends Plugin {

	function isTriggered() {
		
		if(!isset($this->info['text'])) {
			$this->sendOutput(sprintf($this->CONFIG['no_term'],$this->info['triggerUsed']));
			return;
		}
		$this->sendOutput("I'm sorry, i must of derped, or machanics broke.");
	}
}