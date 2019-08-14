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
			self::_log('idorder: ' . $iIdOrder);
		}
				
		if( $Order==null )
		{
			throw new Exception("Payment could not be completed. Please contact support.");
		}
		$Customer = Mage::getModel('customer/customer')->load($Order->getCustomerId());
		
		$strEmail = $Customer->getEmail();
		$strPassword = Mage::helper('flux')->getFluxCustomerPassword();//$_SESSION['flux_customer_password'];
		$strHashedPw =Mage::getModel('customer/customer')->hashPassword($strPassword);
		$iTransactionId = '99999' . $Customer->getId();
		
		self::_log('email: ' . $strEmail);
		self::_log('transactionid: ' . $iTransactionId);
										
		if( !empty($strPassword) )
		{
			$strResultCreate = Mage::helper('flux')->createShopUser($iTransactionId,
										$strEmail,
										$strPassword,
										false );
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
					// echo 'User in Flux verfügbar, Password korrekt, Passworthash falsch. PasswordHash anpassen.';commented may be of no use
					/*$strResultChange = Mage::helper('flux')->changeCustomerPassword($strEmail,
													$strPassword,
													false,
													$strHashedPw,
													false );*/
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
		$strHashedPw =$strPassword;// Mage::getModel('customer/customer')->hashPassword($strPassword);
		$iTransactionId = '99999' . $Customer->getId();
		
		self::_log('email: ' . $strEmail);
		self::_log('transactionid: ' . $iTransactionId);
				
		$strResultCreate = Mage::helper('flux')->createShopUser($iTransactionId,$strEmail,
									$strHashedPw,
									false );
		if( $strResultCreate=='OK' )
		{	
			// User erfolgreich neu angelegt, oder User mit gleichem Passwort bereits in Flux verfügbar		
			Mage::helper('flux')->setFluxCustomerPassword($strPassword);	
		}
		elseif( $strResultCreate=='PASSWORD_WRONG' )
		{
			$strResultCheck = Mage::helper('flux')->checkUserExists($strEmail,$strPassword,false);
			if( $strResultCheck=='OK' )
			{
				// echo 'User in Flux verfügbar, Password korrekt, Passworthash falsch. PasswordHash anpassen.';
			   $strResultChange = Mage::helper('flux')->changeCustomerPassword($strEmail,
											   $strPassword,
				false,																		$strHashedPw,																			false );	Mage::helper('flux')->setFluxCustomerPassword($strPassword);
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
			Mage::getSingleton('customer/session')->addError('Your E-Mail Address is already registered at Flickrocket/FluxPlayer. Please use the password from your existing account.');
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
			if(isset($postData['change_password']) && $postData['change_password'] == 1 && $postData['current_password'] != $postData['password'] ) 
			{       
				Mage::helper('flux')->changeCustomerPassword($customer->getEmail(),
										$postData['current_password'],
										false,
										$postData['password'],
										false );
				Mage::helper('flux')->setFluxCustomerPassword($postData['password']);
			}
			$token=$request->getParam('token');
			$id=$request->getParam('id');
			if(!empty($token) && $id==$customer->getId())
			{
				
				Mage::helper('flux')->changeCustomerPassword($customer->getEmail(),
										'_use_only_after_external_validation_',
										false,
										$postData['password'],
										false );
				Mage::helper('flux')->setFluxCustomerPassword($postData['password']);
			}
		}
		return $this;
	}
		
	public function changeOldPassword(Varien_Event_Observer $Observer)
	{
		self::_log('changeOldPassword');
		
		// Check if it happens somewhere in the customer module
		$request = Mage::app()->getFrontController()->getRequest();
		$module = $request->getModuleName();
		if ( 'customer' != $module ) return;
		$login           = Mage::app()->getRequest()->getParam('login');
		$event          = $Observer->getEvent();
		$model          = $event->getModel();
		$password       = $event->getPassword();
		$session	= Mage::getSingleton('customer/session');
		$customer=$model->loadByEmail($login['username']);
		Mage::helper('flux')->setFluxCustomerPassword($password);
		if($customer instanceof Mage_Customer_Model_Customer) 
		{	$segments=explode(':',$customer->getPasswordHash());
			if((count($segments)==2 && $segments[1]!="MD") || count($segments)==1) 
			{       $customer->setPassword($password);
				$customer->save();
				/*$result=Mage::helper('flux')->needChangePassword($customer->getEmail(),
									     $password,
									     false,
									     $password,
									     false );
				
				if($result==1)
				{	
				Mage::helper('flux')->changeCustomerPassword($customer->getEmail(),
									     $password,
									     false,
									     $password,
									     false );
				}
				if($result==2)
				{    
				   $iTransactionId = '99999' . $customer->getId();				
				   Mage::helper('flux')->createShopUser($iTransactionId,$customer->getEmail(),$password);
				}*/
			}
		}
		return $this;
	}
	
  	public function checkoutCartProductAddAfter(Varien_Event_Observer $observer)
	{	
		$item = $observer->getQuoteItem();
		$productId=$item->getProduct()->getId();
		
		$product = Mage::getModel('catalog/product')->load($productId);

		$attributeSetModel = Mage::getModel("eav/entity_attribute_set");
		$attributeSetModel->load( $product->getAttributeSetId() );
		$attributeSetName  = $attributeSetModel->getAttributeSetName();
		
		if($attributeSetName=='FlickRocketProduct')
		{
			$license=null;
			$project=null;
			$projectId=$product->getProjectId();
			$licenseId=$product->getLicenseId();
			
			$licenses = Mage::helper('flux')->getLicenses();
			$projects = Mage::helper('flux')->getProjects();
			
			$options=array();
			foreach($licenses as $license)
			{
				if($license->ID==$licenseId)
				{				   
				   $options[] = array(
				        'label' => 'Licence',
				        'value' => $license->Name,
				        'print_value' => $license->Name,
				        'option_id' => '133',
		            		);
				   
				   
				}
			}
			
			$infoArr = array();
		
			if ($info = $item->getProduct()->getCustomOption('info_buyRequest')) 
			{
			   $infoArr = unserialize($info->getValue());
			
				/////////////////////////////////////////
			
			
			
			    $additionalOptions = $options;
			
			     
			
			    $item->addOption(array(
				'code' => 'additional_options',
				'value' => serialize($additionalOptions),
			    ));
			
			    $infoArr['additional_options'] = $additionalOptions;

		            $info->setValue(serialize($infoArr));
		            $item->addOption($info);
                    }
		    // Update info_buyRequest to reflect changes
		    /*if ($infoArr &&
		        isset($infoArr['options']) &&
		        isset($infoArr['options'][$option['option_id']]))
		       {
		        // Remove real custom option
		        unset($infoArr['options'][$option['option_id']]);

		        // Add replacement additional option for reorder (see above)
		        $infoArr['additional_options'] = $additionalOptions;

		        $info->setValue(serialize($infoArr));
		        $item->addOption($info);
		    }*/
           
           }//attributeName
            
 	 }//function
	

	
	public function salesConvertQuoteItemToOrderItem(Varien_Event_Observer $observer)
	{
		$quoteItem = $observer->getItem();
		if ($additionalOptions = $quoteItem->getOptionByCode('additional_options')) {
			$orderItem = $observer->getOrderItem();
			$options = $orderItem->getProductOptions();
			$options['additional_options'] = unserialize($additionalOptions->getValue());
			$orderItem->setProductOptions($options);
		}
	}
		
}
