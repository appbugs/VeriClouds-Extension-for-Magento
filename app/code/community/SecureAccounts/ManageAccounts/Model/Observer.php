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
 * Observer For Adding Button To Customer Grid
 *
 * @category    Preksh
 * @package     SecureAccounts_ManageAccounts
 * @author      Priyanka <priyanka@preksh.com>
 * @date        4th April, 2016
 */
?>
<?php

class SecureAccounts_ManageAccounts_Model_Observer {

    /**
     * Function To Add Bulk Reset Button In Customer Grid
     *
     * @category    Preksh
     * @package     SecureAccounts_ManageAccounts
     * @author      Priyanka <priyanka@preksh.com>
     * @date        4th April, 2016
     */
    public function addButtonTest($observer) {
        if (Mage::helper('manageaccounts')->ismoduleenabled()):
            $container = $observer->getBlock();
            if (null !== $container && $container->getType() == 'adminhtml/customer') {
                $data = array(
                    'label' => 'Bulk Reset',
                    'class' => 'some-class',
                    'onclick' => 'setLocation(\'' . Mage::helper("adminhtml")->getUrl('manageaccount/password/bulkreset/') . '\')',
                );
                $container->addButton('button_identifier', $data);
            }
            return $this;
        endif;
    }

}
