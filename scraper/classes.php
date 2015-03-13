<?php 


class AbandonedDomainRequest 
{
    public $aMemberVar = 'aMemberVar Member Variable'; 
    public $aFuncName = 'aMemberFunc'; 

    public function __construct( array $cfg){
	    foreach($cfg as $k=>$v){
	        $this->{$k}=$v;
	    }
	}
}


?>