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
 * Installer For Sql Tables
 *
 * @category    Preksh
 * @package     SecureAccounts_ManageAccounts
 * @author      Priyanka <priyanka@preksh.com>
 * @date        4th April, 2016
 */
?>
<?php

$installer = $this;
$installer->startSetup();
$table = $installer->getConnection()
        ->newTable($installer->getTable('secureaccounts_manageaccounts/compromisedusers'))
        ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, 11, array(
            'identity' => true,
            'unsigned' => false,
            'nullable' => false,
            'primary' => true,
                ), 'Id')
        ->addColumn('customer_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 11, array(
            'nullable' => false,
            'comment' => 'customer id',
                ), 'Customer Id')
        ->addColumn('compromised', Varien_Db_Ddl_Table::TYPE_INTEGER, 11, array(
            'nullable' => false,
            'comment' => 'compromised',
                ), 'Compromised')
        ->addColumn('password_hash', Varien_Db_Ddl_Table::TYPE_VARCHAR, null, array(
            'nullable' => true,
            'comment' => 'hash password',
                ), 'Hash Password')
        ->addColumn('updated_date', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
            'nullable' => true,
            'default' => Varien_Db_Ddl_Table::TIMESTAMP_INIT,
            'comment' => 'updated date',
                ), 'Date Updated')
        ->addColumn('checked_date', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
    'nullable' => true,
    'comment' => 'checked date',
        ), 'Date Checked');
$installer->getConnection()->createTable($table);
$installer->endSetup();
