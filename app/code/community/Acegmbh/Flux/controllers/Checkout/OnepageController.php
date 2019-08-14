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
			if (empty($data['email']) || empty($data['customer_password'])){
			
				parent::saveBillingAction();
				return;

			}
			
        	$strEmail = $data['email'];
        	$strPassword = $data['customer_password'];
        	$_SESSION['flux_customer_password'] = $strPassword;
        	$strResultCheck = Mage::helper('flux')->checkUserExists($strEmail,
												        			$strPassword,
												        			false);
        	if( $strResultCheck=='PASSWORD_WRONG' )
        	{
				$result = array('error' => 1,
        						'message' => Mage::helper('flux')->__('Ihre E-Mail-Adresse ist bereits in Flux angelegt. Bitte verwenden Sie das Passwort aus Ihrem Flux Account.')
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