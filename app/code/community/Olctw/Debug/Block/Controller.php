<?php

class Olctw_Debug_Block_Controller extends Olctw_Debug_Block_Abstract {

    protected function getItems() {
        return Mage::getSingleton('debug/debug')->getBlocks();
    }

    protected function getTemplateDirs() {
        return NULL;
    }

}
