<?php
	$installer = $this;
	$installer = startSetup();

	$installer->run("ALTER TABLE {$this->getTable('cms_page')} ADD `custfield_title` MEDIUMTEXT NOT NULL DEFAULT '';");
	$installer->run("ALTER TABLE {$this->getTable('cms_page')} ADD `custfield_content` MEDIUMTEXT NOT NULL DEFAULT '';");

	$installer->endSetup();


