<?php

class Olctw_Debug_Block_Rewrites extends Olctw_Debug_Block_Abstract {
    /*
     * Forked from http://marius-strajeru.blogspot.tw/2013/03/get-class-rewrites.html
     */

    protected function getItems() {
        $folders = array('app/code/local/', 'app/code/community/'); //folders to parse
        $configFiles = array();
        foreach ($folders as $folder) {
            $files = glob($folder . '*/*/etc/config.xml'); //get all config.xml files in the specified folder
            if (is_array($files)) {
                $configFiles = array_merge($configFiles, $files); //merge with the rest of the config files
            }
        }
        $items = array(); //list of all rewrites

        foreach ($configFiles as $file) {
            $dom = new DOMDocument;
            $dom->loadXML(file_get_contents($file));
            $xpath = new DOMXPath($dom);
            $path = '//rewrite/*'; //search for tags named 'rewrite'
            $text = $xpath->query($path);
            foreach ($text as $rewriteElement) {
                $type = $rewriteElement->parentNode->parentNode->parentNode->tagName; //what is overwritten (model, block, helper)
                $parent = $rewriteElement->parentNode->parentNode->tagName; //module identifier that is being rewritten (core, catalog, sales, ...)
                $name = $rewriteElement->tagName; //element that is rewritten (layout, product, category, order)
                foreach ($rewriteElement->childNodes as $element) {
                    $refObj = new ReflectionClass($element->textContent);
                    $items[$type][$parent . '/' . $name][] = array(
                        'object' => $element->textContent, //class that rewrites it
                        'file' => $refObj->getFileName(),
                    );
                }
            }
        }
        ksort($items);
        return $items;
    }

}

