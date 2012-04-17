<?php

class TM_Featured_Block_Featured extends Mage_Catalog_Block_Product_Abstract
{
    protected $_productsCount = null;

    const DEFAULT_PRODUCTS_COUNT = 5;
    
    protected function _beforeToHtml()
    {
        $collection = Mage::getResourceModel('catalog/product_collection');
        
        Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($collection);
        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection);
        
        $collection = $this->_addProductAttributesAndPrices($collection)
            ->addStoreFilter(Mage::app()->getStore()->getId())
            ->addAttributeToFilter('featured', array('Yes' => true))
            ->setPageSize($this->getProductsCount())
            ->setCurPage(1);

        $this->addPriceBlockType('bundle', 'bundle/catalog_product_price', 'bundle/catalog/product/price.phtml');
        $this->setProductCollection($collection);
        
        return parent::_beforeToHtml();
    }
    
    public function setProductsCount($count)
    {
        $this->_productsCount = $count;
        return $this;
    }

    public function getProductsCount()
    {
        if (null === $this->_productsCount) {
            $this->_productsCount = self::DEFAULT_PRODUCTS_COUNT;
        }
        return $this->_productsCount;
    }
}