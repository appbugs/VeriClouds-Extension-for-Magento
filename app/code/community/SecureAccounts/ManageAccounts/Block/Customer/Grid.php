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
 * Class For Adding Column To Customer Grid In Admin Section
 *
 * @category    Preksh
 * @package     SecureAccounts_ManageAccounts
 * @author      Priyanka <priyanka@preksh.com>
 * @date        4th April, 2016
 */
?>
<?php

class SecureAccounts_ManageAccounts_Block_Customer_Grid extends Mage_Adminhtml_Block_Customer_Grid {

    public function setCollection($collection) {

        $customers = Mage::getModel("customer/customer")->getCollection()->getData();
        foreach ($customers as $customer):
            $customerId = $customer['entity_id'];
            $email = $customer['email'];
            $loadcustomer = Mage::getModel('customer/customer')->load($customerId);
            $temp = explode(':', $loadcustomer->getData("password_hash"));
            $hash = $temp[0];
            Mage::helper('manageaccounts')->checkdeletecustomer($customerId, $hash);
            $isexists = Mage::helper('manageaccounts')->getIfCustomer($customerId);
            if (!$isexists):
                $getallcompromised[] = $customerId;
            endif;

        endforeach;


//        $getallcompromised = Mage::helper('manageaccounts')->getallcompromised();

        Mage::getSingleton('core/session')->setAllCompromised($getallcompromised);
        $collection->addAttributeToSelect('compromise');
        parent::setCollection($collection);
    }

    /**
     * @category    Preksh
     * @package     SecureAccounts_ManageAccounts
     * @function    For Adding Compromised Column In Customer Grid
     * @author      Priyanka <priyanka@preksh.com>
     * @date        4th April, 2016
     */
    protected function _prepareColumns() {

        parent::_prepareColumns();

        if (Mage::helper('manageaccounts')->ismoduleenabled()):
            $this->addColumn('compromise', array(
                'header' => Mage::helper('customer')->__('Compromised'),
                'index' => 'compromise',
                'type' => 'text',
                'width' => '100px',
                'renderer' => 'SecureAccounts_ManageAccounts_Block_Render_Compromise',
            ));
        endif;

        return parent::_prepareColumns();
    }

}
