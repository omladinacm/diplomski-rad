<?php


class SearchResultsProvider
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

    public function getVideos($term, $orderBy): array
    {
        $query = $this->con->prepare("SELECT * FROM videos WHERE title LIKE CONCAT('%', :term, '%') 
                                        OR uploadedBy LIKE CONCAT('%', :term, '%') ORDER BY $orderBy DESC");
        $query->bindParam(":term", $term);
        $query->execute();

        $videos = [];
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $video = new Video($this->con, $row, $this->userLoggedInObj);
            array_push($videos, $video);
        }

        return $videos;
    }
}