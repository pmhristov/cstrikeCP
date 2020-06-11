<?php
abstract class Payment {
    protected $website, $database;
    
	abstract public function process();
}
?>
