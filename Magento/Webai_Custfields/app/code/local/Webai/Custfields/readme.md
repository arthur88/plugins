
# Webai_Custfields 


This is very simple module to insert some additionals fields into every CMS page.


To display on frontend, use folowing command

```php
<?php echo Mage::getBlockSingleton('cms/page')->getPage()->getCustomfieldTitle(); ?>
<?php echo Mage::getBlockSingleton('cms/page')->getPage()->getCustomfieldContent(); ?>
```
