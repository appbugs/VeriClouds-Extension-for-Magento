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
class SecureAccounts_ManageAccounts_AjaxController extends Mage_Core_Controller_Front_Action {

    public function indexAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function resetpasswordpageAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * @category    Preksh
     * @package     SecureAccounts_ManageAccounts
     * @function    Function To Reset Customer Password 
     * @author      Priyanka <priyanka@preksh.com>
     * @date        4th April, 2016
     */
    public function resetpasswordAction() {
        $result = array('success' => false);
        $newpassword = $this->getRequest()->getPost('newpassword');
        $custpwdtoken = $this->getRequest()->getPost('token');
        $customer = Mage::getModel('customer/customer')
                ->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
                ->load($this->getRequest()->getPost('custid'));
        if ($customer->getId()):
            try {
                $customer->setPassword($newpassword)->save();
                Mage::helper('manageaccounts')->deletecompromised($customer->getId());
                $result['message'] = 'Please login to your account';
                $result['redirect'] = Mage::getUrl('customer/account/login');
            } catch (Exception $ex) {
                $result['message'] = $ex->getMessage();
            }
        else:
            $result['message'] = 'you are account is not there our database please register';
        endif;
        return $this->getResponse()->setBody(json_encode($result));
    }

    /**
     * @category    Preksh
     * @package     SecureAccounts_ManageAccounts
     * @function    Function To Check Customer Is Compromised Or Not
     * @author      Priyanka <priyanka@preksh.com>
     * @date        4th April, 2016
     */
    public function loginAction() {
        $postVal = $this->getRequest()->getPost('login');
        $email = $postVal['username'];
        $password = $postVal['password'];
        $temp = explode(':', Mage::helper('core')->getHash($password, Mage_Admin_Model_User::HASH_SALT_LENGTH));
        $password_hash = $temp[0];
        $salt = $temp[1];
        $token = Mage::getStoreConfig('manageaccounts/general/tokenkey');

        $anonymized_email = $email;
        $anonymized_email[0] = '_';
        $anonymized_email[1] = '_';
        //Get API base url from store configuration
        $API_BASE_URL = Mage::getStoreConfig('manageaccounts/general/appbaseurl');
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
                        //Save customer id in compromised table
                        
                        $customer = Mage::getModel('customer/customer')->setWebsiteId(1)->loadByEmail($email);
                        if ($customer):
                            $temp = explode(':', $customer->getData("password_hash"));
                            $phash = $temp[0];
                        endif;
                        Mage::helper('manageaccounts')->compromised($row['email'], $phash, 1);
                        return 'Yes';
                    } else {
                        return 'No';
                    }
                }
            }
        }
        return "No";
    }

}
