<?php

class Olctw_Debug_Block_Debug extends Olctw_Debug_Block_Abstract {

    public function _prepareLayout() {
        return parent::_prepareLayout();
    }

    public function renderView() {
        // Render Debug toolbar only if allowed 
        if (Mage::helper('debug')->isRequestAllowed()) {
            return parent::renderView();
        }
    }

    public function getVersion() {
        return (string) Mage::getConfig()->getNode('modules/Olctw_Debug/version');
    }

    protected function createVersionsPanel() {
        $title = 'Versions';
        $content = '';
        $panel = array(
            'title' => $title,
            'has_content' => true,
            'url' => NULL,
            'dom_id' => 'debug-panel-' . $title,
            'nav_title' => $title,
            'nav_subtitle' => 'Magento modules',
            'template' => 'olctw_debug_versions_panel', // child block defined in layout xml
        );
        return $panel;
    }

    protected function createPerformancePanel() {
        $title = 'Performance';
        $helper = Mage::helper('debug');
        $panel = array(
            'title' => $title,
            'has_content' => true,
            'url' => NULL,
            'dom_id' => 'debug-panel-' . $title,
            'nav_title' => $title,
            'nav_subtitle' => "TIME: {$helper->getScriptDuration()}s MEM: {$helper->getMemoryUsage()}",
            'template' => 'olctw_debug_performance_panel',
        );
        return $panel;
    }

    protected function createConfigPanel() {
        $title = 'Configuration';
        $content = '';
        $panel = array(
            'title' => $title,
            'has_content' => true,
            'url' => NULL,
            'dom_id' => 'debug-panel-' . $title,
            'nav_title' => $title,
            'nav_subtitle' => "Search configurations",
            'template' => 'olctw_debug_config_panel', // child block defined in layout xml
        );
        return $panel;
    }

    protected function createBlocksPanel() {
        $title = 'Blocks';
        $nBlocks = count(Mage::getSingleton('debug/observer')->getBlocks());

        $panel = array(
            'title' => $title,
            'has_content' => true,
            'url' => NULL,
            'dom_id' => 'debug-panel-' . $title,
            'nav_title' => $title,
            'nav_subtitle' => "{$nBlocks} used blocks",
            'template' => 'olctw_debug_blocks_panel', // child block defined in layout xml
        );
        return $panel;
    }

    protected function createLayoutPanel() {
        $title = 'Layout';
        $panel = array(
            'title' => $title,
            'has_content' => true,
            'url' => NULL,
            'dom_id' => 'debug-panel-' . $title,
            'nav_title' => $title,
            'nav_subtitle' => "Layout handlers",
            'template' => 'olctw_debug_layout_panel', // child block defined in layout xml
        );
        return $panel;
    }

    protected function createControllerPanel() {
        $title = 'Controller';
        $content = '';
        $panel = array(
            'title' => $title,
            'has_content' => true,
            'url' => NULL,
            'dom_id' => 'debug-panel-' . $title,
            'nav_title' => $title,
            'nav_subtitle' => 'Controller and request',
            'template' => 'olctw_debug_controller_panel', // child block defined in layout xml
        );
        return $panel;
    }

    protected function createModelsPanel() {
        $title = 'Models';
        $nModels = count(Mage::getSingleton('debug/observer')->getModels());
        $nQueries = count(Mage::getSingleton('debug/observer')->getQueries());
        $panel = array(
            'title' => $title,
            'has_content' => true,
            'url' => NULL,
            'dom_id' => 'debug-panel-' . $title,
            'nav_title' => $title,
            'nav_subtitle' => "{$nModels} models, {$nQueries} queries",
            'template' => 'olctw_debug_models_panel', // child block defined in layout xml
        );
        return $panel;
    }

    protected function createUtilsPanel() {
        $title = 'Utilities';

        $panel = array(
            'title' => $title,
            'has_content' => true,
            'url' => NULL,
            'dom_id' => 'debug-panel-' . $title,
            'nav_title' => $title,
            'nav_subtitle' => "Quick actions",
            'template' => 'olctw_debug_utils_panel', // child block defined in layout xml
        );
        return $panel;
    }

    protected function createLogsPanel() {
        $title = 'Logs';

        $panel = array(
            'title' => $title,
            'has_content' => true,
            'url' => NULL,
            'dom_id' => 'debug-panel-' . $title,
            'nav_title' => $title,
            'nav_subtitle' => "View logs",
            'template' => 'olctw_debug_logs_panel', // child block defined in layout xml
        );
        return $panel;
    }

    protected function createPreferencesPanel() {
        $title = 'Preferences';
        $panel = array(
            'title' => $title,
            'has_content' => true,
            'url' => NULL,
            'dom_id' => 'debug-panel-' . $title,
            'nav_title' => $title,
            'nav_subtitle' => "Customize Magneto Debug",
            'template' => 'olctw_debug_preferences_panel', // child block defined in layout xml
        );
        return $panel;
    }

    protected function createRewritesPanel() {
        $title = 'Rewrites';
        $panel = array(
            'title' => $title,
            'has_content' => true,
            'url' => NULL,
            'dom_id' => 'debug-panel-' . $title,
            'nav_title' => $title,
            'nav_subtitle' => "Show all rewrites",
            'template' => 'olctw_debug_rewrites_panel', // child block defined in layout xml
        );
        return $panel;
    }

    protected function createEventsPanel() {
        $title = 'Events';
        $nEvents = count(Mage::getSingleton('debug/observer')->getFilteredEvents());
        $panel = array(
            'title' => $title,
            'has_content' => true,
            'url' => NULL,
            'dom_id' => 'debug-panel-' . $title,
            'nav_title' => $title,
            'nav_subtitle' => "{$nEvents} dispatched events",
            'template' => 'olctw_debug_events_panel'        // child block defined in layout xml
        );
        return $panel;
    }

    public function getPanels() {
        $panels = array();
        $panels[] = $this->createVersionsPanel();
        $panels[] = $this->createPerformancePanel();
        $panels[] = $this->createConfigPanel();
        $panels[] = $this->createControllerPanel();
        $panels[] = $this->createModelsPanel();
        $panels[] = $this->createLayoutPanel();
        $panels[] = $this->createEventsPanel();
        $panels[] = $this->createBlocksPanel();
        $panels[] = $this->createRewritesPanel();
        $panels[] = $this->createUtilsPanel();
        $panels[] = $this->createLogsPanel();
        // TODO: Implement preferences panel (toggle panels visibility from toolbar)
//        $panels[] = $this->createPreferencesPanel();

        return $panels;
    }

    public function getVisiblePanels() {
        /* @var $helper Olctw_Debug_Helper_Data */
        $helper = Mage::helper('debug');
        $panels = $this->getPanels();
        $visiblePanels = array();

        foreach ($panels as $panel) {
            if ($helper->isPanelVisible($panel['title'])) {
                $visiblePanels[] = $panel;
            }
        }

        return $visiblePanels;
    }

    public function getDebugMediaUrl() {
        return Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN) . 'frontend/base/default/olctw_debug/';
    }

}
