<?php

class Olctw_Debug_Block_Preferences extends Olctw_Debug_Block_Abstract {

    public function getPanels() {
        /* @var $debugBlock Olctw_Debug_Block_Debug */
        $debugBlock = $this->getParentBlock();
        return $debugBlock->getPanels();
    }

    public function getFormUrl() {
        return Mage::getUrl('debug/preferences/updatePost');
    }

}
