<?php


	class Webai_Custfields_Model_Observer extends Mage_Adminhtml_Block_Cms_Block_Edit_Form
	{


		public function cmsFields($observer)
		{
			$model = Mage::registry('cms_page');
			$form  = $observer->getForm();

			$fieldset = $form->addFieldset(
				'webai_custfields_content_fieldset',
				array(
					'legend' => Mage::helper('cms')->__('Additional fields'),
					'class' => 'fieldset-wide')
			);

			$fieldset->addField('custfield_title', 'text', array(
				'name'      => 'custfield_title',
				'label'     => Mage::helper('cms')->__('Title'),
				'title'     => Mage::helper('cms')->__('Title'),
				'disabled'  => false,
				//set field value
				'value'     => $model->getCustomfieldTitle()
			));


			$wysiwyg_config = Mage::getSingleton('cms/wysiwyg_config')->getConfig(array('add_variables' => false, 'add_widgets'
			=>false)); //justi n case there are some 3rd party attachments to wysiwyg

			$fieldset->addField(
				'custfield_content',
				'editor',
				array(
					'name' => 'custfield_content',
					'label' => Mage::helper('cms')->__('Description'),
					'title' => Mage::helper('cms')->__('Description'),
					'style' => 'max-height: 600px;',
					'wysiwyg' => true,
					'required' => true,
					'config' => $wysiwyg_config,
					'value' => $model->getCustomfieldContent(),
				)
			);

		}

	}