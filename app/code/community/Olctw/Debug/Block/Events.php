<?php

class Olctw_Debug_Block_Events extends Olctw_Debug_Block_Abstract {

    private $events = array();
    private $filteredEvents = array();

    private function getEvents() {
        if (empty($this->events)) {
            $this->events = Mage::getSingleton('debug/observer')->getEvents();
        }
        return $this->events;
    }

    private function getFilteredEvents() {
        if (empty($this->filteredEvents)) {
            $this->filteredEvents = Mage::getSingleton('debug/observer')->getFilteredEvents();
        }
        return $this->filteredEvents;
    }

    protected function getAllEvents() {
        return $this->getFilteredEvents();
    }

    protected function getEventsWithObservers() {
        $eventsWithObservers = array_filter($this->getFilteredEvents(), function($item) {
                    return count($item['observers']) > 0;
                });
        return $eventsWithObservers;
    }

    protected function getFilteredEventsCount() {
        return count($this->getFilteredEvents());
    }

    protected function getFilteredEventsWithObserversCount() {
        $eventsWithObservers = array_filter($this->getFilteredEvents(), function($item) {
                    return count($item['observers']) > 0;
                });
        return count($eventsWithObservers);
    }

}