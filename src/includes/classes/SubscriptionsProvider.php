<?php


class SubscriptionsProvider
{
    /**
     * @var \PDO
     */
    private $con;
    /**
     * @var \User
     */
    private $userLoggedInObj;

    public function __construct(PDO $con, User $userLoggedInObj)
    {
        $this->con = $con;
        $this->userLoggedInObj = $userLoggedInObj;
    }

    public function getVideos()
    {
        $videos = [];
        $subUserNames = [];
        $subscriptions = $this->userLoggedInObj->getSubscriptions();

        if (sizeof($subscriptions) > 0) {
            foreach ($subscriptions as $subscription) {
                array_push($subUserNames,$subscription->getUsername());
            }

            $userNames = implode("', '", $subUserNames);

            $query = $this->con->prepare("SELECT * FROM videos WHERE uploadedBy IN ('$userNames')");
            $query->execute();

            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $video = new Video($this->con, $row, $this->userLoggedInObj);
                array_push($videos, $video);
            }
        }

        return $videos;
    }
}