<?php
    # Controllers are not autoloaded so we will have to do it manually:
    $strPath = Mage::getBaseDir('code') . DIRECTORY_SEPARATOR . 
    							'core' . DIRECTORY_SEPARATOR . 
    							'Mage' . DIRECTORY_SEPARATOR .
    							'Checkout' . DIRECTORY_SEPARATOR .
    							'controllers' . DIRECTORY_SEPARATOR .
    							'OnepageController.php';
    require_once($strPath); 
    class Acegmbh_Flux_Checkout_OnepageController extends Mage_Checkout_OnepageController
    {
/*    	
        # Overloaded indexAction
        public function indexAction() {
//            die('Yes, I did it!');

            parent::indexAction();
        }
 */       
         public function saveBillingAction() 
        {
        	$data = $this->getRequest()->getPost('billing', array());
			//let the normal validation handle this
			/*if (empty($data['email']) || empty($data['customer_password'])){
			
				parent::saveBillingAction();
				return;

			}*/
		$strEmail=null;
		$strPassword=null;
		if(!empty($data['email']) && !empty($data['customer_password']))
		{
		   $strEmail=$data['email'];
		   $strPassword=$data['customer_password'];
		   Mage::helper('flux')->setFluxCustomerPassword($strPassword);
		}
		
		if(Mage::getSingleton('customer/session')->isLoggedIn() && (empty($data['email']) || empty($data['customer_password'])))
		{
			$customer=Mage::getSingleton('customer/session')->getCustomer();
			$strEmail=$customer->getEmail();
			$strPassword=Mage::helper('flux')->getFluxCustomerPassword();
		}
			
        	//$strEmail = $data['email'];
        	//$strPassword = $data['customer_password'];
        	//$_SESSION['flux_customer_password'] = $strPassword;
        	$strResultCheck=null;
        	
        	$FluxHelper = Mage::helper('flux');
		$isDidgital=$FluxHelper->isDidgital();
		if($isDidgital)
        	{
        	$strResultCheck = Mage::helper('flux')->checkUserExists($strEmail,
									$strPassword,
									false);
		}
        	if( $strResultCheck=='PASSWORD_WRONG' )
        	{
				$result = array('error' => 1,
        						'message' => Mage::helper('flux')->__('Your E-Mail Address is already registered at Flickrocket/FluxPlayer. Please use the password from your existing account.')
        						);
				$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
        	}
        	else
        	{
        		parent::saveBillingAction();
        	}
        	
        	//die();
        	//die('saveBillingAction');
        } 
    }
