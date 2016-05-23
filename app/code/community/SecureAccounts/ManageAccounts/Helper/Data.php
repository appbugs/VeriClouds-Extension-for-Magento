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
 * Helper For SecureAccounts_ManageAccounts
 *
 * @category    Preksh
 * @package     SecureAccounts_ManageAccounts
 * @author      Priyanka <priyanka@preksh.com>
 * @date        4th April, 2016
 */
?>
<?php

class SecureAccounts_ManageAccounts_Helper_Data extends Mage_Core_Helper_Abstract {

    /**
     * Helper Function To Insert Row In Compromised Table
     *
     * @category    Preksh
     * @package     SecureAccounts_ManageAccounts
     * @author      Priyanka <priyanka@preksh.com>
     * @date        4th April, 2016
     */
    public function compromised($email, $hashpassword, $compromised_status) {
        date_default_timezone_set(Mage::getStoreConfig('general/locale/timezone'));
        $today = date("Y-m-d H:i:s");
        $customer = Mage::getModel("customer/customer");
        $customer->setWebsiteId(1)
                ->loadByEmail($email);
        $model = Mage::getModel("secureaccounts_manageaccounts/compromisedusers")->getCollection()
                ->addFieldToFilter('customer_id', $customer->getId())
                ->getData();
        if (count($model) == 0):
            $compromisedcollection = Mage::getModel("secureaccounts_manageaccounts/compromisedusers");
            //Insert row if it is not already present in table

            $compromisedcollection->setCustomerId($customer->getId())
                    ->setCompromised($compromised_status)
                    ->setPasswordHash($hashpassword)
                    ->setUpdatedDate($today)
                    ->setCheckedDate($today);
            try {
                $compromisedcollection->save();
                return true;
            } catch (Exception $ex) {
                return false;
            }
        endif;
    }

    public function getallcompromised() {
        $customerstocallapi = array();
        $customers = Mage::getModel("customer/customer")->getCollection()->getData();

        $model = Mage::getModel("secureaccounts_manageaccounts/compromisedusers")->getCollection()
                ->addFieldToFilter('compromised', array(1, 2))
                ->addFieldToSelect('customer_id')
                ->getData();
        foreach ($model as $key => $val):
            $customerids[] = $val['customer_id'];
        endforeach;


        foreach ($customers as $customer):
            if (!in_array($customer['entity_id'], $customerids)):
                $customerstocallapi[] = $customer['entity_id'];
            endif;
        endforeach;



        return $customerstocallapi;
    }

    /**
     * Helper Function To Delete Compromised Customer From Table
     *
     * @category    Preksh
     * @package     SecureAccounts_ManageAccounts
     * @author      Priyanka <priyanka@preksh.com>
     * @date        4th April, 2016
     */
    public function checkdeletecustomer($customerId, $hash) {
        //check more than predefined days
        $model = Mage::getModel("secureaccounts_manageaccounts/compromisedusers")
                ->getCollection()
                ->addFieldToFilter('customer_id', $customerId)
                ->getFirstItem();
        date_default_timezone_set(Mage::getStoreConfig('general/locale/timezone'));
        $today = date("Y-m-d");
        $day15 = date('Y-m-d', strtotime($model->getCheckedDate() . ' + ' . Mage::getStoreConfig('manageaccounts/general/noofdays') . ' days'));

        //check if customer has reset his password

        try {
            if (($today > $day15) || ($hash != $model->getPasswordHash())):
                $model->setId($model->getId())->delete();
            endif;
            return true;
        } catch (Exception $ex) {
            return false;
        }
    }

    /**
     * Helper Function To Delete Compromised Customer From Table After Resetting The Password
     *
     * @category    Preksh
     * @package     SecureAccounts_ManageAccounts
     * @author      Priyanka <priyanka@preksh.com>
     * @date        4th April, 2016
     */
    public function deletecompromised($customerid) {
        $model = Mage::getModel("secureaccounts_manageaccounts/compromisedusers")->getCollection()
                        ->addFieldToFilter('customer_id', $customerid)->getFirstItem();
        try {
            //Delete cusomer details from compromised table
            $model->setId($model->getId())->delete();
            return true;
        } catch (Exception $ex) {
            return false;
        }
    }

    /**
     * Helper Function To Check Customer Is Compromised Or Not
     *
     * @category    Preksh
     * @package     SecureAccounts_ManageAccounts
     * @author      Priyanka <priyanka@preksh.com>
     * @date        4th April, 2016
     */
    public function getIfCustomer($customerid) {
        $compromisedcollection = Mage::getModel("secureaccounts_manageaccounts/compromisedusers")->getCollection()
                ->addFieldToFilter('customer_id', $customerid)
                ->getData();

        return count($compromisedcollection);
    }

    public function getIfCompromisedCustomer($customerid) {
        $compromisedcollection = Mage::getModel("secureaccounts_manageaccounts/compromisedusers")->getCollection()
                ->addFieldToFilter('customer_id', $customerid)
                ->addFieldToFilter('compromised', 2)
                ->getData();

        return count($compromisedcollection);
    }
    
    /**
     * Helper Function To Check Module Is Enabled Or Not
     *
     * @category    Preksh
     * @package     SecureAccounts_ManageAccounts
     * @author      Priyanka <priyanka@preksh.com>
     * @date        4th April, 2016
     */
    public function ismoduleenabled() {
        return Mage::getStoreConfig('manageaccounts/general/active');
    }

    /**
     * Helper Function To Return Url To Check Customer Is Compromised Or Not 
     *
     * @category    Preksh
     * @package     SecureAccounts_ManageAccounts
     * @author      Priyanka <priyanka@preksh.com>
     * @date        4th April, 2016
     */
    public function getloginurl() {
        return Mage::getBaseUrl() . 'manageaccounts/ajax/login';
    }

    /**
     * Helper Function To Check Email Is Sent Or Not 
     *
     * @category    Preksh
     * @package     SecureAccounts_ManageAccounts
     * @author      Priyanka <priyanka@preksh.com>
     * @date        4th April, 2016
     */
    public function checkemailstatus($email) {
        date_default_timezone_set(Mage::getStoreConfig('general/locale/timezone'));
        $today = date("Y-m-d H:i:s");
        $customer = Mage::getModel("customer/customer");
        $customer->setWebsiteId(1)
                ->loadByEmail($email);
        $compromisedcollection = Mage::getModel("secureaccounts_manageaccounts/compromisedusers")->getCollection()
                ->addFieldToFilter('customer_id', $customer->getId())
                ->getFirstItem();
        //If 1=> Not Sent , 2=> Sent
        if ($compromisedcollection->getCompromised() == 2):
            $send_date = date_format(date_create($compromisedcollection->getUpdatedDate()), "M d,Y g:i A");
            return "Mail Sent On " . $send_date;
        elseif ($compromisedcollection->getCompromised() == 1):
            $url = Mage::helper("adminhtml")->getUrl('manageaccount/password/reset/customer_id/' . $customer->getId());
            return '<a href="' . Mage::helper("adminhtml")->getUrl('manageaccounts/password/reset/customer_id/' . $customer->getId()) . '">Reset password</a>';
        else:
            return 'No';
        endif;
    }

    /**
     * Helper Function To Change Compromised Status After Sending Mail
     *
     * @category    Preksh
     * @package     SecureAccounts_ManageAccounts
     * @author      Priyanka <priyanka@preksh.com>
     * @date        4th April, 2016
     */
    public function changeemailstatus($customerid) {
        date_default_timezone_set(Mage::getStoreConfig('general/locale/timezone'));
        $today = date("Y-m-d H:i:s");

        $compromisedcollection = Mage::getModel("secureaccounts_manageaccounts/compromisedusers")->getCollection()
                ->addFieldToFilter('customer_id', $customerid)
                ->getFirstItem();
        if (count($compromisedcollection) > 0):
            //Change compromised status to 2
            $compromised = Mage::getModel("secureaccounts_manageaccounts/compromisedusers")->load($compromisedcollection->getId());
            $compromised->setCompromised(2)
                    ->setUpdatedDate($today);
            try {
                $compromised->save();
                return true;
            } catch (Exception $ex) {
                return false;
            }
        endif;
    }

    /**
     * Helper Function For Encryption
     *
     * @category    Preksh
     * @package     SecureAccounts_ManageAccounts
     * @author      Priyanka <priyanka@preksh.com>
     * @date        4th April, 2016
     */
    private $rounds;

    public function __construct($rounds = 12) {
        if (CRYPT_BLOWFISH != 1) {
            throw new Exception("bcrypt not supported in this installation. See http://php.net/crypt");
        }

        $this->rounds = $rounds;
    }

    public function hash($input, $salt) {
        $hash = crypt($input, $salt);

        if (strlen($hash) > 13)
            return $hash;

        return false;
    }

    public function verify($input, $existingHash) {
        $hash = crypt($input, $existingHash);

        return $hash === $existingHash;
    }

    private function getSalt() {
        $salt = sprintf('$2a$%02d$', $this->rounds);

        $bytes = $this->getRandomBytes(16);

        $salt .= $this->encodeBytes($bytes);

        return $salt;
    }

    private $randomState;

    private function getRandomBytes($count) {
        $bytes = '';

        if (function_exists('openssl_random_pseudo_bytes') &&
                (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN')) { // OpenSSL is slow on Windows
            $bytes = openssl_random_pseudo_bytes($count);
        }

        if ($bytes === '' && is_readable('/dev/urandom') &&
                ($hRand = @fopen('/dev/urandom', 'rb')) !== FALSE) {
            $bytes = fread($hRand, $count);
            fclose($hRand);
        }

        if (strlen($bytes) < $count) {
            $bytes = '';

            if ($this->randomState === null) {
                $this->randomState = microtime();
                if (function_exists('getmypid')) {
                    $this->randomState .= getmypid();
                }
            }

            for ($i = 0; $i < $count; $i += 16) {
                $this->randomState = md5(microtime() . $this->randomState);

                if (PHP_VERSION >= '5') {
                    $bytes .= md5($this->randomState, true);
                } else {
                    $bytes .= pack('H*', md5($this->randomState));
                }
            }

            $bytes = substr($bytes, 0, $count);
        }

        return $bytes;
    }

    private function encodeBytes($input) {
        // The following is code from the PHP Password Hashing Framework
        $itoa64 = './ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';

        $output = '';
        $i = 0;
        do {
            $c1 = ord($input[$i++]);
            $output .= $itoa64[$c1 >> 2];
            $c1 = ($c1 & 0x03) << 4;
            if ($i >= 16) {
                $output .= $itoa64[$c1];
                break;
            }

            $c2 = ord($input[$i++]);
            $c1 |= $c2 >> 4;
            $output .= $itoa64[$c1];
            $c1 = ($c2 & 0x0f) << 2;

            $c2 = ord($input[$i++]);
            $c1 |= $c2 >> 6;
            $output .= $itoa64[$c1];
            $output .= $itoa64[$c2 & 0x3f];
        } while (1);

        return $output;
    }

}
