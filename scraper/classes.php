<?php 


class AbandonedDomainRequest 
{
	// Properties
    public $IsRead 	= False;
    public $From 	= "";
    public $Date 	= "";
    public $Subject = ""; 
    public $Message = "";
    public $Domain  = "";

    // Flags
    # Past submission check
    public $F_IsBanned			= False;
    public $F_IsTodayBlocked 	= False;
    public $F_IsMonthBlocked	= False;
    public $F_IsBannable        = False;

    # Valid Submission E-mail
    public $F_HasOwner          = False;
    public $F_HasDomain         = False;

    # JSON Check
    public $F_HasJSON           = False;
    public $F_HasValidJSON      = False;

    # WHOIS Check
    public $F_HasViewableWHOIS  = False;
    public $F_IsOwner           = False;

    # Global
    public $F_IsValid			= False;
    public $F_IsInvalid			= False;
    public $F_IsDuplicate		= False;

    public function __construct(){

	}
}


?>