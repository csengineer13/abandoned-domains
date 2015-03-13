<?php 


class AbandonedDomainRequest 
{
	// Properties
    public $IsRead 	= False;
    public $From 	= "";
    public $Date 	= "";
    public $Subject = ""; 
    public $Message = "";

    // Flags
    public $F_IsBanned			= False;
    public $F_IsTodayBlocked 	= False;
    public $F_IsMonthBlocked	= False;
    public $F_IsValid			= False;
    public $F_IsInvalid			= False;
    public $F_IsDuplicate		= False;
    public $F_IsBannable		= False;

    public function __construct(){

	}
}


?>