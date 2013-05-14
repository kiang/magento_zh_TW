<?php

class Olctw_Debug_Block_Versions extends Olctw_Debug_Block_Abstract {

    protected function getItems() {
        $items = array(
            'local' => array(),
            'community' => array(),
            'core' => array(),
        );
        $items['core'][] = array(
            'module' => 'Magento',
            'codePool' => 'core',
            'active' => 'true',
            'version' => Mage::getVersion());

        $modulesConfig = Mage::getConfig()->getModuleConfig();
        foreach ($modulesConfig as $node) {
            foreach ($node as $module => $data) {
                $codePool = $data->codePool->asArray();
                if (empty($codePool))
                    continue;
                if (is_array($codePool)) {
                    $codePool = implode('.', $codePool);
                }
                $items[$codePool][] = array(
                    "module" => $module,
                    "codePool" => $codePool,
                    "active" => $data->active,
                    "version" => $data->version
                );
            }
        }
        return $items;
    }

}

