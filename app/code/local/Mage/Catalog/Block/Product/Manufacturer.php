<?php

class Mage_Catalog_Block_Product_Manufacturer extends Mage_Core_Block_Template
{
    public function getManufacturersCollection()
    {
      $optionCollection = Mage::getResourceModel('eav/entity_attribute_option_collection')
                ->setAttributeFilter(66)
                ->setPositionOrder('asc', true)
                ->load();
      return $optionCollection;
    }
}
?>