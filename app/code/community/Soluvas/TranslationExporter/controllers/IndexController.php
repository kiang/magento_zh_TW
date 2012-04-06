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
class Soluvas_TranslationExporter_IndexController extends Mage_Adminhtml_Controller_Action {

    /**
     * Display view.
     */
    public function indexAction() {
        $this->_title($this->__('System'))->_title($this->__('Tools'))->_title('Translation Exporter');
        $this->loadLayout();
        $this->_setActiveMenu('system/tools');
        $this->renderLayout();
    }

    /**
     * Do the export.
     */
    public function exportAction() {
        Mage::log("TranslationExporter: Export started");
        $translate = Mage::getSingleton('core/translate');
        $locale = $translate->getLocale();
        $targetDir = Mage::getBaseDir('var') . DS . 'translations' . DS . $locale;
        $localeDir = Mage::getBaseDir('locale');
        Mage::log("TranslationExporter - target directory: {$targetDir}");
        
        $dbtrans = $translate->getResource()->getTranslationArray(null, $locale);
        Mage::log("TranslationExporter: " . count($dbtrans) . " translation rows from DB");
        // for each module:
        // - for each CSV file in that module:
        //   1. read it to memory
        //   2. modify it according to DB translation for that module
        //   3. write it back to dest dir
        foreach ($translate->getModulesConfig() as $moduleName => $info) {
            $info = $info->asArray();
            Mage::log("TranslationExporter: Exporting module $moduleName");
            
            foreach ($info['files'] as $file) {
                $enData = array();
                $enFilePath = $localeDir . DS . 'en_US' . DS . $file;
                if (file_exists($enFilePath)) {
                    $parser = new Varien_File_Csv();
                    $parser->setDelimiter(Mage_Core_Model_Translate::CSV_SEPARATOR);
                    $enData = $parser->getDataPairs($enFilePath);
                }
                
                $data = array();
                $filePath = $localeDir . DS . $locale . DS . $file;
                Mage::log("TranslationExporter: Reading {$filePath}");
                if (file_exists($filePath)) {
                    $parser = new Varien_File_Csv();
                    $parser->setDelimiter(Mage_Core_Model_Translate::CSV_SEPARATOR);
                    $data = $parser->getDataPairs($filePath);
                }
                foreach($data AS $key => $val) {
                    if(isset($enData[$key])) {
                        $enData[$key] = $val;
                    }
                }

                // 2. MODIFY
                foreach ($enData as $key => $val) {
                    $fullKey = $moduleName . '::' . $key;
                    if (isset($dbtrans[$fullKey])) {
                        $stack[] = $fullKey;
                        Mage::log("TranslationExporter: Rewrite '{$fullKey}' from '{$val}' to '{$dbtrans[$fullKey]}'");
                        $enData[$key] = $dbtrans[$fullKey];
                    }
                }
                // 3. WRITE
                if (!file_exists($targetDir)) {
                    if (!mkdir($targetDir, 0777, true)) {
                        throw new Exception("Cannot create $targetDir");
                    }
                }
                $targetFile = $targetDir . '/' . $file;
                $parser = new Varien_File_Csv();
                $csvdata = array();
                foreach ($enData as $key => $val)
                    $csvdata[] = array($key, $val);
                $parser->saveData($targetFile, $csvdata);
                Mage::log("TranslationExporter: wrote {$targetFile}");
            }
            Mage::log("TranslationExporter: Done.");
        }

        Mage::getSingleton('adminhtml/session')->addSuccess(
                Mage::helper('compiler')->__("Translations has been exported to '%s'.", $targetDir)
        );
        $this->_redirect('*/*/');
    }

    protected function _isAllowed() {
        return Mage::getSingleton('admin/session')->isAllowed('system/convert/translations');
    }

}