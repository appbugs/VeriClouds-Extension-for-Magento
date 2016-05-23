<?php

/**
 * Preksh
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * It is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to akash@preksh.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. 
 *
 * @category    Preksh
 * @package     SecureAccounts_ManageAccounts
 * @copyright  ©Copyright 2015-16. Preksh Innovations Pvt. Ltd.(www.preksh.com) All Rights Reserved.
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
/**
 * Class For Encrypting Customer's Passwords
 *
 * @category    Preksh
 * @package     SecureAccounts_ManageAccounts
 * @author      Priyanka <priyanka@preksh.com>
 * @date        4th April, 2016
 */
?>
<?php

/**
 * Block For Checking Compromised Customers
 *
 * @category    Preksh
 * @package     SecureAccounts_ManageAccounts
 * @author      Priyanka <priyanka@preksh.com>
 * @date        4th April, 2016
 */
class SecureAccounts_ManageAccounts_Block_Render_Compromise extends
Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    /**
     * @category    Preksh
     * @package     SecureAccounts_ManageAccounts
     * @function    For Rendering Compromised Column In Customer Grid 
     * @author      Priyanka <priyanka@preksh.com>
     * @date        4th April, 2016
     */
    public function render(Varien_Object $row) {
        $callapiflag = 0;
        $email = $row->getData("email");
        $customerId = $row->getData("entity_id");
        $customer = Mage::getModel('customer/customer')->load($customerId);
        $temp = explode(':', $customer->getData("password_hash"));
        $hash = $temp[0];
        $salt = $temp[1];
        $token = Mage::getStoreConfig('manageaccounts/general/tokenkey');
        $noofcustomers = Mage::getStoreConfig('manageaccounts/general/noofcustomers');
        $getapi = array_slice(Mage::getSingleton('core/session')->getAllCompromised(), 0, $noofcustomers);
        if (in_array($customerId, $getapi)):
            $data = $this->private_preserving_compromise_detection($customerId, $email, $hash, $salt, $token);
        else:
            $data = Mage::helper('manageaccounts')->checkemailstatus($email);
        endif;
        return $data;
    }

    /**
     * @category    Preksh
     * @package     SecureAccounts_ManageAccounts
     * @function    For Detecting Compromised Customers
     * @author      Priyanka <priyanka@preksh.com>
     * @date        4th April, 2016
     */
    public function private_preserving_compromise_detection($customerId, $email, $password_hash, $salt, $token) {
        //Get API base url from store configuration
        $API_BASE_URL = Mage::getStoreConfig('manageaccounts/general/appbaseurl');

        //Create anonymized email for sending to API
        $anonymized_email = $email;
        $anonymized_email[0] = '_';
        $anonymized_email[1] = '_';

        //Call to API 
        $url_req = $API_BASE_URL . 'token=' . $token . '&mode=privacy_preserving_account_query_salt&email=' . $anonymized_email . '&salt=' . $salt; //to test server
        $ch = curl_init();
        $timeout = 600;
        curl_setopt($ch, CURLOPT_URL, $url_req);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $json_str = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($json_str, true);
        if ($result['result'] != 'succeeded') {
            return "unknown";
        }

        //Checking for compromised status
        $records = $result['records'];
        foreach ($records as $row) {

            if ($email == $row['email']) {
                $hash_algorithm = $row['hash_algorithm'];
                if ($hash_algorithm['ca_hash'] == 'bcrypt') {
                    if (!isset($bcrypt))
//                        $bcrypt = new Bcrypt();
                        $hash = Mage::helper('manageaccounts')->hash($password_hash, $hash_algorithm['ca_salt']);
                    if ($hash == $row['password_hash']) {
                        $compromised_status = 1;
                        //Save customer in compromised table
//                        Mage::helper('manageaccounts')->compromised($row['email'], $password_hash);
                        //Check email is sent or not
//                        return Mage::helper('manageaccounts')->checkemailstatus($row['email']);
                    } else {
                        $compromised_status = 0;
                    }
                }
            }
        }

        Mage::helper('manageaccounts')->compromised($email, $password_hash, $compromised_status);
       return Mage::helper('manageaccounts')->checkemailstatus($email);
        
    }

}
