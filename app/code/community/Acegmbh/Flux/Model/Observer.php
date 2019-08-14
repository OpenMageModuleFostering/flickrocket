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
		$strHashedPw = Mage::getModel('customer/customer')->hashPassword($strPassword);
		$iTransactionId = 'FR-M1-'.$Customer->getId();
		
		self::_log('email: ' . $strEmail);
		self::_log('transactionid: ' . $iTransactionId);

		$FluxHelper = Mage::helper('flux');
		$isDigital = $FluxHelper->hasOrderDigitalItems($Order);
										
		if( !empty($strPassword) && $isDigital )
		{
			$strResultCreate = Mage::helper('flux')->createShopUser($iTransactionId,
										$strEmail,
										$strPassword,
										false );
			if( $strResultCreate == 'OK' )
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
				
		if($isDigital)
		{ 
		  $FluxHelper->createShopOrder($Order);		
		}
		
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
	
	public function flickrocket_sync(Varien_Event_Observer $Observer)
	{
		self::_log('flickrocket_sync');

        //get input params
        $request = Mage::app()->getFrontController()->getRequest();
        $fr_email = $request->getParam('email');
		$fr_new_email = $request->getParam('new_email');
		$fr_old_pw_hash = $request->getParam('old_pw_hash');
		$fr_new_pw_hash = $request->getParam('new_pw_hash');
		$fr_pw_hash_type = $request->getParam('pw_hash_type');
		$fr_signature = $request->getParam('signature');


		if ($fr_pw_hash_type == "6" && self::flux_check_signature($fr_email, $fr_new_email, $fr_old_pw_hash, $fr_new_pw_hash, $fr_pw_hash_type, $fr_signature))
		{
			$success = false;

			$customer = Mage::getModel("customer/customer"); 
			$customer->setWebsiteId(Mage::app()->getWebsite()->getId()); 
			$customer->loadByEmail($fr_email);

			if($customer->getId())
			{
				if ($fr_new_email != "" && $fr_new_email != $fr_email)
				{
					//Store new email
					$customer->setEmail($fr_new_email);
					$success = true;  
				}
				if ($fr_new_pw_hash != "")
				{
					//Store new password
					$customer->setPassword($fr_new_pw_hash);
					$success = true;  
				}
			}
			if ($success)
			{
				$customer->save();
		        $response = Mage::app()->getFrontController()->getResponse()
					->clearHeaders()
					->setHeader('HTTP/1.0', 200, true);			
			}
			else
			{
		        $response = Mage::app()->getFrontController()->getResponse()
					->clearHeaders()
					->setHeader('HTTP/1.0', 409, true);			
			}
		}
		else
		{
		        $response = Mage::app()->getFrontController()->getResponse()
					->clearHeaders()
					->setHeader('HTTP/1.0', 405, true);	
		}
	}

	function flux_check_signature($fr_email, $fr_new_email, $fr_old_pw_hash, $fr_new_pw_hash, $fr_pw_hash_type, $fr_signature) 
	{
		$returnval = false;

		$fr_sync_secret = Mage::getStoreConfig('acegmbh_flux/sync/flux_syncsecret');
		$fr_string_to_hash = $fr_sync_secret.":email=".$fr_email.":new_email=".$fr_new_email.":new_pw_hash=".$fr_new_pw_hash.":old_pw_hash=".$fr_old_pw_hash.":pw_hash_type=".$fr_pw_hash_type;
		$fr_string_to_hash = utf8_encode ( $fr_string_to_hash);
		$sha1 = sha1 ( $fr_string_to_hash );

		if ($sha1 == $fr_signature) $returnval = true;

		return $returnval;
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
	
	public function setAllowedGuestCheckout(Varien_Event_Observer $observer)
	{
		$FluxHelper = Mage::helper('flux');
		$isDidgital=$FluxHelper->isDidgital();
		if($isDidgital)
		{	$result=$observer->getResult();
			$result->setIsAllowed(false);
		}

	}	
	
	public function flickrocket_wait(Varien_Event_Observer $Observer)
	{
		self::_log('flickrocket_wait');

        //get input params
        $request = Mage::app()->getFrontController()->getRequest();
        $fr_flickrocket_wait = htmlspecialchars($request->getParam('flickrocket_wait'));
		
		if ($fr_flickrocket_wait != '')
		{
			$fr_flickrocket_wait = urldecode($fr_flickrocket_wait);
			$response = Mage::app()->getFrontController()->getResponse();

			$output_html = '
<!DOCTYPE html>
<html>
<head>
<style>
/* Center the loader */
#loader {
  position: absolute;
  left: 50%;
  top: 50%;
  z-index: 1;
  width: 150px;
  height: 150px;
  margin: -75px 0 0 -75px;
  border: 16px solid #f3f3f3;
  border-radius: 50%;
  border-top: 16px solid #3498db;
  width: 60px;
  height: 60px;
  -webkit-animation: spin 2s linear infinite;
  animation: spin 2s linear infinite;
}

@-webkit-keyframes spin {
  0% { -webkit-transform: rotate(0deg); }
  100% { -webkit-transform: rotate(360deg); }
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

/* Add animation to "page content" */
.animate-bottom {
  position: relative;
  -webkit-animation-name: animatebottom;
  -webkit-animation-duration: 1s;
  animation-name: animatebottom;
  animation-duration: 1s
}

@-webkit-keyframes animatebottom {
  from { bottom:-100px; opacity:0 }
  to { bottom:0px; opacity:1 }
}

@keyframes animatebottom {
  from{ bottom:-100px; opacity:0 }
  to{ bottom:0; opacity:1 }
}

#myDiv {
  display: none;
  text-align: center;
}
</style>
</head>
<body onload="myFunction()" style="margin:0;">

<div id="loader"></div>

<div style="display:none;" id="myDiv" class="animate-bottom">
  <h2>Tada!</h2>
  <p>Some text in my newly loaded page..</p>
</div>

<script>
var myVar;

function myFunction() 
{
    myVar = setTimeout(showPage, 300);
}

function showPage() 
{
    window.location.href = "'.$fr_flickrocket_wait.'";
}
</script>

</body>
</html>';

	        $response->clearBody();
	        $response->setBody($output_html);
			$response->sendResponse();
			exit;
		}

	}
}
