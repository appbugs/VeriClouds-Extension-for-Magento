Install and config

1.Install the extension from Magento Connect https://www.magentocommerce.com/magento-connect/secure-accounts.html. Do not install the code from this GitHub as the code here is in development mode and thus stable version.
2.After installation is done, log into the admin portal of your Magento-based webiste. Go to System->Configuration. Then click on "Secure Login Settings" under "SECURE ACCOUNT" in left panel. 
3.Put the activation code in Token Key field and Click "Save Config" to save the configurations. To get a valid activation code, sign up from VeriClouds: https://vericlouds.com/wp/compromised-accounts-detection/

How to use

1.Log into the admin portal of your Magento-based website. 
2.Go to Customers->Manage Customers section. This section lists all accounts of your customers. 
3.Our extension added a new column titled "Compromised" to the table. You can see which customer account has its password compromised online. If compromised, the column shows a "Reset password" button. If not compromised, the column simply shows "No". Once you click on a "Reset password" button, a password reset email will be sent to the corresponding customer's email address. After the customer reset his/her password, the button will be removed and the column will show "No" for this customer account. 
4.You can also use the "Bulk Reset" button displayed on the top right corner to send password reset emails for all compromised accounts. 
