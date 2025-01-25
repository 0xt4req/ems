<?php 

class PublicEvents extends BaseEvent {

    public function getAll() {
        return Parent::getAll();
    }

    // check if the event exists
    public function checkEventExists($eventId) {
        return parent::checkEventExists($eventId);
    }

    // totalEvents
    public function totalEvents() {
        return parent::totalEvents();
    }

}


?>