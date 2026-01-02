#Installation

Magento2 PhonePe Payment Gateway module installation is very easy, please follow the steps for installation-

1. Before installing the module, please run the following command to add the repository details to your `composer.json` file:

composer config repositories.phonepe-pg-php-sdk-v2 '{"type": "package", "url": "./vendor/phonepe/pg-sdk-php/", "package": {"name": "phonepe/pg-php-sdk-v2", "version": "2.0.0", "dist": {"url": "https://phonepe.mycloudrepo.io/public/repositories/phonepe-pg-php-sdk/v2-sdk.zip", "type": "zip"}, "autoload": {"classmap": ["/"]}}}'

2. Unzip the respective extension zip and create Webkul(vendor) and PhonePe(module) name folder inside your magento/app/code/ directory and then move all module's files into magento root directory Magento2/app/code/Webkul/PhonePe/ folder.

Run Following Command via terminal
-----------------------------------
composer require phonepe/pg-php-sdk-v2:^2.0 vlucas/phpdotenv:^5.6 netresearch/jsonmapper:^4.4
php bin/magento setup:upgrade
php bin/magento setup:di:compile
php bin/magento setup:static-content:deploy

3. Flush the cache and reindex all.
 
now module is properly installed

#User Guide

For Magento2 PhonePe Payment Gateway module's working process follow user guide - https://webkul.com/blog/

Find us our support policy - https://store.webkul.com/support.html/

#Refund

Find us our refund policy - https://store.webkul.com/refund-policy.html/
