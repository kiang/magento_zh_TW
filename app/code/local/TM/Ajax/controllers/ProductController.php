<?php

require_once 'Mage/Catalog/controllers/ProductController.php';

class TM_Ajax_ProductController extends Mage_Catalog_ProductController
{
    public function quickViewAction()
    {
        if (!$this->getRequest()->isXmlHttpRequest()){
            $this->_redirect('/');
        }
        
        $result = '';
        
        if ($product = $this->_initProduct()) {
            Mage::dispatchEvent('catalog_controller_product_view', array('product' => $product));
            Mage::getSingleton('catalog/session')->setLastViewedProductId($product->getId());
            
            $productName = $product->getName();
            $productPrice = $product->getFormatedPrice();
            $productPicture = Mage::helper('catalog/image')->init($product, 'image')->resize(230, 230); 
            $productDescription = $product->getShortDescription();
            $productAvaliable = $product->isInStock();
            $productUrl = $product->getProductUrl(); 
            
            // @todo move to block (use createBlock->toHtml();)
            $result .= "<div class='product-essential'>
                    <div class='product-img-box'>
                        <a href='$productUrl'><img src='$productPicture'/></a>
                    </div>
                    
                    <div class='product-shop'>
                        <h3 class='product-name'>" . $product->getName() . "</h3>";
            
            if($product->isSaleable()) {
                $result .= "<button class='button' onclick=\"setLocation('".Mage::helper('checkout/cart')->getAddUrl($product)."')\"><span><span>".Mage::helper('catalog')->__('Add to Cart')."</span></span></button> OR  ";
            }
            
            if (Mage::helper('wishlist/data')->isAllow()) {
                $result .= "<a href='".Mage::helper('wishlist/data')->getAddUrl($product)."'><span>".Mage::helper('catalog')->__('Add to Wishlist')."</span></a> <br/>";
            }
            
            $result .= "<div class='divider'></div>";
            
            if ($product->getShortDescription()) {
			
				// $ShortDescription=$this->helper('catalog/output')->productAttribute($_product, $_product->getShortDescription(), 'short_description');
                $result .= "<h4>".Mage::helper('catalog')->__('Quick Overview')."</h4>
                <div class='short-description'>" . nl2br($product->getShortDescription()). "</div>";
            }
            
            $result .= "</div>
                    <div class='clear'></div>
                </div>
            </div>"; 
        } else {
            $result .= Mage::helper('catalog')->__('Error. Product not found');
        }
        echo $result;
    }
}


               


    