<?php

class Acegmbh_Flux_Model_Observer
{	
	const LOGFILE = 'Acegmbh_Flux_Model_Observer.log';
	
	public function _log( $strLogMessage )
	{
		Mage::log($strLogMessage, null, self::LOGFILE );
	}
	
	public function placeOrder(Varien_Event_Observer $Observer)
	{
		self::_log('placeOrder');
		
		$Event = $Observer->getEvent();
// echo '<pre>';		
// print_r($Event);
// $object = new ReflectionObject($Event);
// var_dump($object->getMethods());
// echo '</pre>';
// die();		
		$Order = $Event->getOrder();
		if( $Order==null )
		{
			$arrOrderIds = $Observer->getOrderIds();
			$iIdOrder = $arrOrderIds[0];
			$Order = Mage::getModel('sales/order')->load($iIdOrder);
		}
		self::_log('idorder: ' . $iIdOrder);		
		if( $Order==null )
		{
			throw new Exception("Zahlung konnte nicht abgeschlossen werden! Bitte wenden Sie sich an den Support.");
		}
		$Customer = Mage::getModel('customer/customer')->load($Order->getCustomerId());
		
		$strEmail = $Customer->getEmail();
		$strPassword = $_SESSION['flux_customer_password'];
		$strHashedPw = Mage::getModel('customer/customer')->hashPassword($strPassword);
		$iTransactionId = '99999' . $Customer->getId();
		
		self::_log('email: ' . $strEmail);
		self::_log('transactionid: ' . $iTransactionId);
										
		if( !empty($strPassword) )
		{
			$strResultCreate = Mage::helper('flux')->createShopUser($iTransactionId,
																	$strEmail,
																	$strHashedPw,
																	true );
			if( $strResultCreate=='OK' )
			{
				// User erfolgreich neu angelegt, oder User mit gleichem Passwort bereits in Flux verfügbar
			}
			elseif( $strResultCreate=='PASSWORD_WRONG' )
			{
				$strResultCheck = Mage::helper('flux')->checkUserExists($strEmail,
																		$strPassword,
																		false);
				if( $strResultCheck=='OK' )
				{
					// echo 'User in Flux verfügbar, Password korrekt, Passworthash falsch. PasswordHash anpassen.';
					$strResultChange = Mage::helper('flux')->changeCustomerPassword($strEmail,
																					$strPassword,
																					false,
																					$strHashedPw,
																					true );
				}
				elseif( $strResultCheck=='PASSWORD_WRONG' )
				{
					/**
					 * this should never happen(!) because the same check is in the billing-step of onepage checkout
					 **/
					self::_log('ERROR: User (' . $strEmail .') exists in Flux, but with an other password', null, 'Acegmbh_Flux_Data.log', true);
			
					// ('Der Benutzer ist in Flux bereits verfügbar, allerdings unter mit einem anderen Passwort.');
				}
			}
			else
			{
				self::_log('SOAP_ERROR in Observer->createFluxUser', null, 'Acegmbh_Flux_Data.log', true);
			}
		}
				
		$FluxHelper = Mage::helper('flux');
		$FluxHelper->createShopOrder($Order);		
	} 
	
	public function createFluxUser(Varien_Event_Observer $Observer)
	{
		self::_log('createFluxUser');
		
		$Event = $Observer->getEvent();
		$Customer = $Event->getCustomer();
		 				
		$strEmail = $Customer->getEmail();
		$strPassword = $Customer->getPassword();
		$strHashedPw = Mage::getModel('customer/customer')->hashPassword($strPassword);
		$iTransactionId = '99999' . $Customer->getId();
		
		self::_log('email: ' . $strEmail);
		self::_log('transactionid: ' . $iTransactionId);
				
		$strResultCreate = Mage::helper('flux')->createShopUser($iTransactionId,
																$strEmail,
																$strHashedPw,
																true );
		if( $strResultCreate=='OK' )
		{
			// User erfolgreich neu angelegt, oder User mit gleichem Passwort bereits in Flux verfügbar			
		}
		elseif( $strResultCreate=='PASSWORD_WRONG' )
		{
			$strResultCheck = Mage::helper('flux')->checkUserExists($strEmail,
																	$strPassword,
																	false);
			if( $strResultCheck=='OK' )
			{
				// echo 'User in Flux verfügbar, Password korrekt, Passworthash falsch. PasswordHash anpassen.';
				$strResultChange = Mage::helper('flux')->changeCustomerPassword($strEmail,
																				$strPassword,
																				false,
																				$strHashedPw,
																				true );
			}
			elseif( $strResultCheck=='PASSWORD_WRONG' )
			{
				/**
				 * this should never happen(!) because the same check is in $this->checkFluxUser()
				 **/
				self::_log('ERROR: User (' . $strEmail .') exists in Flux, but with an other password', null, 'Acegmbh_Flux_Data.log', true);
				// ('Der Benutzer ist in Flux bereits verfügbar, allerdings unter mit einem anderen Passwort.');
			}
		}
		else
		{
			self::_log('SOAP_ERROR in Observer->createFluxUser', null, 'Acegmbh_Flux_Data.log', true);
		}
	}
	
	public function checkFluxUser(Varien_Event_Observer $Observer)
	{
		self::_log('checkFluxUser');
		
        //get input params
        $request = Mage::app()->getFrontController()->getRequest();
        $email = $request->getParam('email');
        $password = $request->getParam('password');		

        //let the normal validation handle this
        if (empty($email) || empty($password)) return;
		
		/** for regular "create customer" form **/	
		$strEmail = $email;
		$strPassword = $password; 
		$strResultCheck = Mage::helper('flux')->checkUserExists($strEmail,
																$strPassword,
																false);
		if( $strResultCheck=='PASSWORD_WRONG' )
		{
			Mage::getSingleton('customer/session')->addError('Ihre E-Mail-Adresse ist bereits in Flux angelegt. Bitte verwenden Sie das Passwort aus Ihrem Flux Account.');
			$url = Mage::getModel('core/url') ->getUrl('*/*/create');
			Mage::app()->getResponse()->setRedirect($url);
			Mage::app()->getResponse()->sendResponse();
			exit;
		}
		
	}	
	
	public function changeFluxPw(Varien_Event_Observer $Observer)
	{
		self::_log('changeFluxPw');
		
		
		// Check if it happens somewhere in the customer module
		$request = Mage::app()->getFrontController()->getRequest();
		$module = $request->getModuleName();
		//$controller = $request->getControllerName();
		//$action = $request->getActionName();
		if ( 'customer' != $module ) return;
				
		$event              = $Observer->getEvent();
		$customer           = $event->getCustomer();
		$postData           = Mage::app()->getRequest()->getPost();
		if($customer instanceof Mage_Customer_Model_Customer && !$customer->isObjectNew()) 
		{
			if( $postData['change_password'] == 1 && $postData['current_password'] != $postData['password'] ) 
			{
				Mage::helper('flux')->changeCustomerPassword(	$customer->getEmail(),
																$postData['current_password'],
																false,
																$postData['password'],
																false );
			}
		}
		return $this;
	}
		
}