# Megamenu Normal

Extract the zip file into magento root folder

then execute the setup upgrade command      

```
php bin/magento setup:upgrade    
php bin/magento setup:static-content:deploy -f
```

then go to admin > Venustheme > Megamenu > Create menu profile with alias = "menu-top"
Default menu profile show on frontend are using alias = "menu-top"
