<?php


class LikedVideosProvider
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
        $username = $this->userLoggedInObj->getUsername();

        $query = $this->con->prepare("
SELECT videoId 
FROM likes 
WHERE username = :username
AND commentId = 0
ORDER BY id DESC
");
        $query->bindParam(":username", $username);
        $query->execute();

        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $videos[] = new Video($this->con, $row["videoId"], $this->userLoggedInObj);
        }

        return $videos;
    }
}