<?php

class Mage_Catalog_Block_Product_Popular extends Mage_Catalog_Block_Product_Abstract
{
    protected $_productCollection;

    protected function _getProductCollection($categoryID = null)
    {
        if (is_null($this->_productCollection)) {
            $layer = Mage::getSingleton('catalog/layer');

            $origCategory = null;
            $categoryID = $categoryID ? $categoryID : $this->getCategoryId();


            $category = Mage::getModel('catalog/category')->load($categoryID ? $categoryID : 17);
                if ($category->getId()) {
                    $origCategory = $layer->getCurrentCategory();
                    $layer->setCurrentCategory($category);
                }
            $this->_productCollection = $layer->getProductCollection();

            $this->prepareSortableFieldsByCategory($layer->getCurrentCategory());

            if ($origCategory) {
                $layer->setCurrentCategory($origCategory);
            }
        }
        return $this->_productCollection;
    }

    /**
     * Retrieve loaded category collection
     *
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    public function getLoadedProductCollection($categoryID = null)
    {
        return $this->_getProductCollection($categoryID);
    }

    public function prepareSortableFieldsByCategory($category) {
        if (!$this->getAvailableOrders()) {
            $this->setAvailableOrders($category->getAvailableSortByOptions());
        }
        $availableOrders = $this->getAvailableOrders();
        if (!$this->getSortBy()) {
            if ($categorySortBy = $category->getDefaultSortBy()) {
                if (!$availableOrders) {
                    $availableOrders = $this->_getConfig()->getAttributeUsedForSortByArray();
                }
                if (isset($availableOrders[$categorySortBy])) {
                    $this->setSortBy($categorySortBy);
                }
            }
        }


        return $this;
    }
}

?>