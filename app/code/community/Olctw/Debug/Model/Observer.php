<?php

class Olctw_Debug_Model_Observer {

    private $_actions = array();
    // List of assoc array with class, type and sql keys
    private $collections = array();
    // private $layoutUpdates = array();
    private $models = array();
    private $blocks = array();
    private $layoutBlocks = array();
    private $events = array();

    public function getModels() {
        return $this->models;
    }

    public function getCollections() {
        return $this->collections;
    }

    public function getEvents() {
        return $this->events;
    }

    public function getFilteredEvents() {
        $flags = array();
        $eventsFiltered = array_filter($this->events, function($item) use (&$flags) {
                    if (array_key_exists($item['name'], $flags)) {
                        $flags[$item['name']] += 1;
                        return false;
                    } else {
                        $flags[$item['name']] = 1;
                        return true;
                    }
                });
        array_walk($eventsFiltered, function(&$item) use ($flags) {
                    $item['count'] = $flags[$item['name']];
                });
        return $eventsFiltered;
    }

    public function getBlocks() {
        $blocks = $this->blocks;
        $result = array();
        self::addBlockLevelRecurse($result, $blocks);
        return $result;
    }

    public function getLayoutBlocks() {
        $blocks = $this->layoutBlocks;
        $result = array();
        self::addBlockLevelRecurse($result, $blocks);
        return $result;
    }

    static protected function addBlockLevelRecurse(&$tree, $blocks, $name = '', $level = 0) {
        foreach ($blocks as $key => $block) {
            if ($block['parent'] == $name) {
                $block['level'] = $level;
                $tree[$key] = $block;
                self::addBlockLevelRecurse($tree, $blocks, $block['layout_name'], $level + 1);
            }
        }
    }

    // public function getLayoutUpdates() { return $this->layoutUpdates; }

    /**
     *
     * TODO: Make this a setting
     *
     * @return bool
     */
    protected function _skipCoreBlocks() {
        return false;
    }

    /**
     * Logic that checks if we should ignore this block
     *
     * @param $block Mage_Core_Block_Abstract
     * @return bool
     */
    protected function _skipBlock($block) {
        $blockClass = get_class($block);

        if ($this->_skipCoreBlocks() && strpos($blockClass, 'Mage_') === 0) {
            return true;
        }

        // Don't list blocks from Debug module
        if (strpos($blockClass, 'Olctw_Debug_Block') === 0) {
            return true;
        }

        return false;
    }

    public function getQueries() {
        //TODO: implement profiler for connections other than 'core_write'  
        $profiler = Mage::getSingleton('core/resource')->getConnection('core_write')->getProfiler();
        $queries = array();

        if ($profiler) {
            $queries = $profiler->getQueryProfiles();
        }

        return $queries;
    }

    public function onEventDispatch(Varien_Event_Observer $observer) {
        $event = $observer->getEvent();
        $eventStruct = array(
            'name' => $event->getEventname(),
            'observers' => $event->getEventobservers()
        );
        $this->events[] = $eventStruct;
    }

    public function onLayoutGenerate(Varien_Event_Observer $observer) {
        $layout = $observer->getEvent()->getLayout();
        $layoutBlocks = $layout->getAllBlocks();

        // After layout generates all the blocks
        foreach ($layoutBlocks as $block) {
            $blockStruct = array();
            $parentBlock = $block->getParentBlock();
            $blockStruct['parent'] = isset($parentBlock) ? $parentBlock->getNameInLayout() : "";
            $blockStruct['class'] = get_class($block);
            $blockStruct['layout_name'] = $block->getNameInLayout();
            if (method_exists($block, 'getTemplateFile')) {
                $blockStruct['template'] = $block->getTemplateFile();
            } else {
                $blockStruct['template'] = '';
            }
            if (method_exists($block, 'getViewVars')) {
                $blockStruct['context'] = $block->getViewVars();
            } else {
                $blockStruct['context'] = NULL;
            }
            Mage::getSingleton('debug/observer')->layoutBlocks[] = $blockStruct;
        }
    }

    /**
     * Listens to core_block_abstract_to_html_before event and records blocks
     * that are about to being rendered.
     *
     * @param Varien_Event_Observer $observer
     * @return Olctw_Debug_Model_Observer
     */
    public function onBlockToHtml(Varien_Event_Observer $observer) {
        /** @var $event Varien_Event */
        $event = $observer->getEvent();
        /* @var $block Mage_Core_Block_Abstract */
        $block = $event->getBlock();

        if ($this->_skipBlock($block)) {
            return $this;
        }

        $blockStruct = array();
        $parentBlock = $block->getParentBlock();
        $blockStruct['parent'] = isset($parentBlock) ? $parentBlock->getNameInLayout() : "";
        $blockStruct['class'] = get_class($block);
        $blockStruct['layout_name'] = $block->getNameInLayout();
        $blockStruct['rendered_at'] = microtime(true);

        if (method_exists($block, 'getTemplateFile')) {
            $blockStruct['template'] = $block->getTemplateFile();
        } else {
            $blockStruct['template'] = '';
        }
        if (method_exists($block, 'getViewVars')) {
            $blockStruct['context'] = $block->getViewVars();
        } else {
            $blockStruct['context'] = NULL;
        }

        Mage::getSingleton('debug/observer')->blocks[$block->getNameInLayout()] = $blockStruct;

        return $this;
    }

    /**
     * Listens to core_block_abstract_to_html_after event end computes the time
     * spent in block's _toHtml (rendering time).
     *
     * @param Varien_Event_Observer $observer
     * @return Olctw_Debug_Model_Observer
     */
    public function onBlockToHtmlAfter(Varien_Event_Observer $observer) {
        $event = $observer->getEvent();
        /* @var $block Mage_Core_Block_Abstract */
        $block = $event->getBlock();

        // Don't list blocks from Debug module
        if ($this->_skipBlock($block)) {
            return $this;
        }

        $blockStruct = Mage::getSingleton('debug/observer')->blocks[$block->getNameInLayout()];

        $duration = microtime(true) - $blockStruct['rendered_at'];
        Mage::getSingleton('debug/observer')->blocks[$block->getNameInLayout()]['rendered_in'] = $duration;
    }

    function onActionPostDispatch(Varien_Event_Observer $event) {
        $action = $event->getControllerAction();

        $actionStruct = array();
        $actionStruct['class'] = get_class($action);
        $actionStruct['action_name'] = $action->getFullActionName();
        $actionStruct['route_name'] = $action->getRequest()->getRouteName();

        Mage::getSingleton('debug/observer')->_actions[] = $actionStruct;
    }

    // controller_action_layout_generate_blocks_after
    function onCollectionLoad(Varien_Event_Observer $event) {
        /** @var Mage_Core_Model_Mysql4_Store_Collection */
        $collection = $event->getCollection();

        $collectionStruct = array();
        $collectionStruct['sql'] = $collection->getSelectSql(true);
        $collectionStruct['type'] = 'mysql';
        $collectionStruct['class'] = get_class($collection);
        Mage::getSingleton('debug/observer')->collections[] = $collectionStruct;
    }

    function onEavCollectionLoad(Varien_Event_Observer $event) {
        $collection = $event->getCollection();
        $sqlStruct = array();
        $sqlStruct['sql'] = $collection->getSelectSql(true);
        $sqlStruct['type'] = 'eav';
        $sqlStruct['class'] = get_class($collection);
        Mage::getSingleton('debug/observer')->collections[] = $sqlStruct;
    }

    /* function onPrepareLayout(Varien_Event_Observer $observer){
      $block = $observer->getEvent()->getBlock();
      var_dump(array_keys($observer->getEvent()->getData()));
      // Mage::log('onPrepareLayout: ' . get_class($observer) . 'block=";

      $layoutUpdate = array();
      $layoutUpdate['block'] = get_class($observer->getBlock());
      $layoutUpdate['name'] = get_class($observer->getName());
      $this->layoutUpdates[] = $layoutUpdate;
      } */

    function onModelLoad(Varien_Event_Observer $observer) {
        $event = $observer->getEvent();
        $object = $event->getObject();
        $key = get_class($object);
        $currentModels = Mage::getSingleton('debug/observer')->getModels();

        if (array_key_exists($key, $currentModels)) {
            $currentModels[$key]['occurrences']++;
        } else {
            $model = array();
            $model['class'] = get_class($object);
            $model['resource_name'] = $object->getResourceName();
            $model['occurrences'] = 1;
            $currentModels[$key] = $model;
        }
        Mage::getSingleton('debug/observer')->models = $currentModels;

        return $this;
    }

    /**
     * We listen to this event to filter access to actions defined by Debug module.
     * We allow only actions if debug toolbar is on and ip is listed in Developer Client Restrictions
     *
     * @param Varien_Event_Observer $observer
     *
     * @return void
     */
    function onActionPreDispatch(Varien_Event_Observer $observer) {
        $action = $observer->getEvent()->getControllerAction();
        $moduleName = $action->getRequest()->getControllerModule();
        if (strpos($moduleName, "Olctw_Debug") === 0 && !Mage::helper('debug')->isRequestAllowed()) {

            Mage::log("Access to Olctw_Debug's actions blocked: dev mode is set to false.");
            // $response = $action->getResponse();
            // $response->setHttpResponseCode(404);
            // $response->setBody('Site access denied.');
            //$action->setDispatched(true)
            //
            exit();
        }
    }

}