<?php


class TrendingProvider
{
    /**
     * @var \PDO
     */
    private PDO $con;
    /**
     * @var \User
     */
    private User $userLoggedInObj;

    public function __construct(PDO $con, User $userLoggedInObj)
    {
        $this->con = $con;
        $this->userLoggedInObj = $userLoggedInObj;
    }

    public function getVideos(): array
    {
        $videos = [];

        $query = $this->con->prepare("
SELECT * 
FROM videos 
WHERE uploadDate >= NOW() - INTERVAL 7 DAY 
ORDER BY views DESC 
LIMIT 15
");
        $query->execute();

        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $video = new Video($this->con, $row, $this->userLoggedInObj);
            array_push($videos, $video);
        }

        return $videos;
    }
}