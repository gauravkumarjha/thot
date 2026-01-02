#Installation

Magento2 PhonePe Payment Gateway module installation is very easy, please follow the steps for installation-

1. Please add the below mentioned repository details to the composer.json file which is located in the Magento installation root directory.
    "repositories": [
        {
            "type": "package",
            "package": [
                {
                    "dist": {
                        "type": "zip",
                        "url": "https://phonepe.mycloudrepo.io/public/repositories/phonepe-pg-php-sdk/phonepe-pg-php-sdk.zip"
                    },
                    "name": "phonepe/phonepe-pg-php-sdk",
                    "version": "1.0.0",
                    "autoload": {
                        "classmap": ["/"]
                    }
                }
            ]
        }
    ]

2. Unzip the respective extension zip and then move "app" folder (inside "src" folder) into magento root directory.

Run Following Command via terminal
-----------------------------------
composer require --prefer-source phonepe/phonepe-pg-php-sdk
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
