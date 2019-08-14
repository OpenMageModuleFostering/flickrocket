<?php

class Acegmbh_Flux_Model_Flux_Soap_Response_GetLicenses
{
	public $GetLicensesResult;
	public $Licenses;
		/* 
		 * <stLicense>
		 * 	<ID>15</ID>
		 * 	<Name>Rental (24 hours)</Name>
		 * 	<LicType>ltFlickRocket</LicType>
		 * </stLicense> 
		 * */
	
	/**
	 * @var int $ErrorCode 
	 */
	public $ErrorCode;
}