<?php

class Acegmbh_Flux_Model_Customer_Entity_Customer
    extends Mage_Customer_Model_Entity_Customer
{	
	
	/**
	 * Change password.
	 * 
	 * @param Mage_Customer_Model_Customer $Customer
	 * @param string $newPassword
	 * 
	 * @return true or a negative error code
	 * 
	 * -2 XML invalid 
	 * -3 Customer node is missing
	 * -4 Customer not found
	 * -5 Password missing
	 * -6 Node empty
	 */
	public function changePassword(Mage_Customer_Model_Customer $Customer, $newPassword)
    {
    	Mage::log('changePassword in Acegmbh_Flux_Model_Customer_Entity_Customer', null, 'Acegmbh_Flux_Customer.log', true);
    	
        $Customer->setPassword($newPassword);
        $this->saveAttribute($Customer, 'password_hash');
        $customerId = $Customer->getId();
        $strPasswdHash = $Customer->getPasswordHash();
        
        Mage::helper('flux')->changeCustomerPassword(	$Customer->getEmail(), 
														$strPasswdHash, 
														true, 
														$newPassword, 
														false );
        return parent::changePassword($Customer, $newPassword);
                
/*        
        if (Acegmbh_Flux_Model_Flux_Tools::isFluxUser($customerId))
        {
			$wsdl = Mage::getStoreConfig('acegmbh_flux/sandbox/flux_wsdl');
			$SoapClient = new SoapClient($wsdl, 
											array(
													'soap_version'	=> SOAP_1_2,
													'trace'			=> true,
													'classmap'		=> array(
																			'CreateCustomerPasswordResponse' => 'Acegmbh_Flux_Model_Flux_Soap_Response_ChangeCustomerPassword'
																			),
													'cache_wsdl'	=> WSDL_CACHE_NONE
												)
									);
        
	    	$fluxPassword = substr($password, 0, strpos($password, ':'));
	    	$soapRequest = array();
			$soapRequest['EMail'] = Mage::getStoreConfig('acegmbh_flux/flux/flux_email');
			$soapRequest['Password'] = Mage::getStoreConfig('acegmbh_flux/flux/flux_password');

			$Doc = new DOMDocument('1.0', 'UTF-8');
			$Doc->formatOutput = true;
	    	$_Customer = $Doc->createElement('Customer');
	    	$Doc->appendChild($_Customer);
	    	$_Email = $Doc->createElement('Email', $Customer->getEmail());
	    	$_Customer->appendChild($_Email);
	    	$Password = $Doc->createElement('PasswordHash', $fluxPassword);
	    	$_Customer->appendChild($Password);
	    	$soapRequest['XML'] = $Doc->saveXML();
	        try
			{
				$Result = $SoapClient->__call('CreateShopOrder',
																array(
																		'parameters' => $soapRequest
																	)
											);
			}
			catch (SoapFault $Exception)
			{
				Mage::logException($Exception);
				continue;
			}
        }
*/
        return $this;
    }
}