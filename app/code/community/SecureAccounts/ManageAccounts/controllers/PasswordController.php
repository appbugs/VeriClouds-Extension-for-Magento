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
 * Class For Mail Sending For Resetting Compromised Customer's Password
 *
 * @category    Preksh
 * @package     SecureAccounts_ManageAccounts
 * @author      Priyanka <priyanka@preksh.com>
 * @date        4th April, 2016
 */
?>
<?php

class SecureAccounts_ManageAccounts_PasswordController extends Mage_Adminhtml_Controller_Action {

    /**
     * @category    Preksh
     * @package     SecureAccounts_ManageAccounts
     * @function    Function To Send Mail For Individual Reset Password Action
     * @author      Priyanka <priyanka@preksh.com>
     * @date        4th April, 2016
     */
    public function resetAction() {

        $customerId = $this->getRequest()->getParam('customer_id');
        $customer = Mage::getModel('customer/customer')->load($customerId);
        $email = $customer->getData("email");
        $customer_name = $customer->getFirstname() . ' ' . $customer->getLastname();
        $storeId = Mage::app()->getStore()->getId();
        if ($customer->getId()) {
            try {
                $newResetPasswordLinkToken = Mage::helper('customer')->generateResetPasswordLinkToken();
                $customer->changeResetPasswordLinkToken($newResetPasswordLinkToken);
                $template_id = 'password_compromise_reset_template';
                // Load template by template_id
                $email_template = Mage::getModel('core/email_template')->loadDefault($template_id);
                $makeforgoturl = Mage::getBaseUrl() . 'manageaccounts/ajax/resetpasswordpage/' . 'token/' . $newResetPasswordLinkToken . '/customer/' . $customer->getId();
                $email_template_variables = array();
                $email_template_variables['customertoken'] = $newResetPasswordLinkToken;
                $email_template_variables['custid'] = $customer->getId();
                $email_template_variables['forgoturl'] = $makeforgoturl;
                $email_template_variables['customername'] = $customer->getFirstname();
                $email_template_variables['storeurl'] = Mage::getBaseUrl();
                $processed_template = $email_template->getProcessedTemplate($email_template_variables);

                // Get sender name and email from store configuration
                $sender_name = Mage::getStoreConfig('trans_email/ident_support/name');
                $sender_email = Mage::getStoreConfig('trans_email/ident_support/email');
                $email_template->setSenderName($sender_name);
                $email_template->setSenderEmail($sender_email);

                $mail = Mage::getModel('core/email')
                        ->setToName($customer_name)
                        ->setToEmail($email)
                        ->setBody($processed_template)
                        ->setSubject('Subject :Reset Password')
                        ->setFromEmail($sender_email)
                        ->setFromName($sender_name)
                        ->setType('html');
                $mail->send();

                //Change status to mail sent
                Mage::helper('manageaccounts')->changeemailstatus($customer->getId());

                $result['message'] = "Password reset email sent: " . $email;
            } catch (Exception $exception) {
                Mage::log($exception);
                $result['message'] = $exception;
            }
        } else {
            $result['message'] = "Failed to find customer Id";
        }
        Mage::getSingleton('adminhtml/session')->addSuccess($result['message']);
        $this->_redirectUrl(Mage::helper('adminhtml')->getUrl('adminhtml/customer/index/'));
    }

    /**
     * @category    Preksh
     * @package     SecureAccounts_ManageAccounts
     * @function    Function To Send Mail For Bulk Reset Password Action
     * @author      Priyanka <priyanka@preksh.com>
     * @date        4th April, 2016
     */
    public function bulkresetAction() {
        $result = array();
        //Get all compromised customers
        $compromisemodel = Mage::getModel('secureaccounts_manageaccounts/compromisedusers')
                ->getCollection()
                ->addFieldToFilter('compromised', 1)
                ->addFieldToSelect('*');
        if (count($compromisemodel->getData()) > 0):

            foreach ($compromisemodel->getData() as $key => $compromise):

                $customer = Mage::getModel('customer/customer')->load($compromise['customer_id']);
                $email = $customer->getData("email");
                $customer_name = $customer->getFirstname() . ' ' . $customer->getLastname();
                $storeId = Mage::app()->getStore()->getId();
                if ($customer->getId()) {
                    try {
                        $newResetPasswordLinkToken = Mage::helper('customer')->generateResetPasswordLinkToken();
                        $customer->changeResetPasswordLinkToken($newResetPasswordLinkToken);
                        $template_id = 'password_compromise_reset_template';
                        // Load template by template_id
                        $email_template = Mage::getModel('core/email_template')->loadDefault($template_id);
                        $makeforgoturl = Mage::getBaseUrl() . 'manageaccounts/ajax/resetpasswordpage/' . 'token/' . $newResetPasswordLinkToken . '/customer/' . $customer->getId();
                        $email_template_variables = array();
                        $email_template_variables['customertoken'] = $newResetPasswordLinkToken;
                        $email_template_variables['custid'] = $customer->getId();
                        $email_template_variables['forgoturl'] = $makeforgoturl;
                        $email_template_variables['customername'] = $customer->getFirstname();
                        $email_template_variables['storeurl'] = Mage::getBaseUrl();
                        $processed_template = $email_template->getProcessedTemplate($email_template_variables);

                        // Get sender name and email from store configuration
                        $sender_name = Mage::getStoreConfig('trans_email/ident_support/name');
                        $sender_email = Mage::getStoreConfig('trans_email/ident_support/email');
                        $email_template->setSenderName($sender_name);
                        $email_template->setSenderEmail($sender_email);

                        $mail = Mage::getModel('core/email')
                                ->setToName($customer_name)
                                ->setToEmail($email)
                                ->setBody($processed_template)
                                ->setSubject('Subject :Reset Password')
                                ->setFromEmail($sender_email)
                                ->setFromName($sender_name)
                                ->setType('html');
                        $mail->send();

                        //Change status to mail sent
                        Mage::helper('manageaccounts')->changeemailstatus($customer->getId());


                        $result[$key]['message'] .= "Password reset email sent: " . $email;
                    } catch (Exception $exception) {
                        Mage::log($exception);
                        $result[$key]['message'] .= $exception;
                    }
                } else {
                    $result[$key]['message'] .= "Failed to find customer Id";
                }
            endforeach;
        else:
            $result[$key]['message'] .= "There are no compromised customer";
        endif;
        Mage::getSingleton('adminhtml/session')->addSuccess($result[$key]['message']);
        $this->_redirectUrl(Mage::helper('adminhtml')->getUrl('adminhtml/customer/index/'));
    }

}
