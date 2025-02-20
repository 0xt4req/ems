<?php
class PrivateEvent extends BaseEvent
{
    public function __construct($db)
    {
        if (!isset($_SESSION['username'])) {
            http_response_code(302);
            header('Location: http://localhost/ems/public/views/login.php');
            // echo json_encode(["success" => false, "message" => "Unauthorized"]);
            exit;
        }
        parent::__construct($db);
    }

    public function create($name, $description, $date, $time, $location, $maxCapacity)
    {
        return parent::create($name, $description, $date, $time, $location, $maxCapacity);
    }

    public function getAllEventForUser()
    {
        return parent::getAllEventForUser();
    }

    public function update($eventId, $name, $description, $date, $time, $location, $maxCapacity)
    {
        return parent::update($eventId, $name, $description, $date, $time, $location, $maxCapacity);
    }

    public function delete($eventId)
    {
        return parent::delete($eventId);
    }

    public function checkEventOwner($eventId)
    {
        return parent::checkEventOwner($eventId);
    }
}
?>
