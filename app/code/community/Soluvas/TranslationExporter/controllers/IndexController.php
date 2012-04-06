<?php
/**
 * Soluvas
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 *
 * @category   Soluvas
 * @package    Soluvas_TranslationExporter
 * @copyright  Copyright (c) 2010 Soluvas (http://www.soluvas.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Translation Exporter Admin controller for index.
 */
class Soluvas_TranslationExporter_IndexController
	extends Mage_Adminhtml_Controller_Action
{

	/**
	 * Display view.
	 */
	public function indexAction() {
		$this->_title($this->__('System'))->_title($this->__('Tools'))->_title('Translation Exporter');

        $this->loadLayout();
//        $this->getLayout()->getBlock('content')->append(
//            $this->getLayout()->createBlock('customer/account_dashboard')
//        );
        $this->_setActiveMenu('system/tools');
        $this->renderLayout();
	}

	/**
	 * Do the export.
	 */
	public function exportAction() {
		Mage::log("TranslationExporter: Export started");
		Mage::log("TranslationExporter - var directory: " . Mage::getBaseDir('var'));
		$translate = Mage::getSingleton('core/translate');
		
		$dbtrans = $translate->getResource()->getTranslationArray(null, $translate->getLocale());
		Mage::log("TranslationExporter: " . count($dbtrans) . " translation rows from DB");
		//var_dump(dbtrans);
		
		// for each module:
		// - for each CSV file in that module:
		//   1. read it to memory
		//   2. modify it according to DB translation for that module
		//   3. write it back to dest dir
		
		foreach ($translate->getModulesConfig() as $moduleName=>$info) {
            $info = $info->asArray();
            Mage::log("TranslationExporter: Exporting module $moduleName");
            //var_dump($moduleName);
            //var_dump($info);
            foreach ($info['files'] as $file) {
            	//var_dump($file);
            	//$filePath = $translate->_getModuleFilePath($moduleName, $file); // protected!
                $filePath = Mage::getBaseDir('locale');
        		$filePath.= DS. $translate->getLocale() .DS. $file;
            	Mage::log("TranslationExporter: Reading $filePath");
        		//var_dump($filePath);
        		
				//$fileData = $translate->_getFileData($filePath); // protected!
            	$data = array();
		        if (file_exists($filePath)) {
		            $parser = new Varien_File_Csv();
		            $parser->setDelimiter(Mage_Core_Model_Translate::CSV_SEPARATOR);
		            $data = $parser->getDataPairs($filePath);
		        }
		        
		        // 2. MODIFY
		        foreach ($data as $key => $val) {
		        	$fullKey = $moduleName .'::'. $key;
		        	if (array_key_exists($fullKey, $dbtrans)) {
		        		Mage::log("TranslationExporter: Rewrite '$fullKey' from '$val' to '". $dbtrans[$fullKey] ."'");
		        		$data[$key] = $dbtrans[$fullKey];
		        	}
		        }
            	//var_dump($data);
            	
		        // 3. WRITE
		        $targetDir = Mage::getBaseDir('var') . '/translations/' . $translate->getLocale();
		        if (!file_exists($targetDir)) {
		        	if (!mkdir($targetDir, null, true)) {
						throw new Exception("Cannot create $targetDir");		        		
		        	}
		        }
		        $targetFile = $targetDir .'/'. $file;
		        $parser = new Varien_File_Csv();
		        $csvdata = array();
		        foreach ($data as $key => $val)
		        	$csvdata[] = array($key, $val);
		        $parser->saveData($targetFile, $csvdata);
		        // do not use PHP native fputcsv because it uses optional enclosing quotes
//		        $fp = fopen($targetFile, 'w');
//		        foreach ($data as $key => $val)
//		        	fputcsv($fp, array($key, $val) );
//		        fclose($fp);
		        Mage::log("TranslationExporter: wrote $targetFile");
            } 
            Mage::log("TranslationExporter: Done.");
        }
		
        $targetDir = Mage::getBaseDir('var') . '/translations/' . $translate->getLocale();
        Mage::getSingleton('adminhtml/session')->addSuccess(
            Mage::helper('compiler')->__("Translations has been exported to '%s'.", $targetDir)
        );
        $this->_redirect('*/*/');
	}
	
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('system/convert/translations');
    }
	
}