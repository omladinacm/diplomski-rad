<?php


class VideoUploadData
{
    private $videoDataArray;
    private $title;
    private $description;
    private $privacy;
    private $category;
    private $uploadedBy;

    public function __construct($videoDataArray, $title, $description, $privacy, $category, $uploadedBy)
    {
        $this->videoDataArray = $videoDataArray;
        $this->title = $title;
        $this->description = $description;
        $this->privacy = $privacy;
        $this->category = $category;
        $this->uploadedBy = $uploadedBy;
    }

    /**
     * @return mixed
     */
    public function getVideoDataArray()
    {
        return $this->videoDataArray;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return mixed
     */
    public function getPrivacy()
    {
        return $this->privacy;
    }

    /**
     * @return mixed
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @return mixed
     */
    public function getUploadedBy()
    {
        return $this->uploadedBy;
    }

    public function updateDetails(PDO $con, int $videoId)
    {
        $query = $con->prepare("UPDATE videos SET title=:title, description=:description, privacy=:privacy,
                                category=:category WHERE id=:videoId");
        $query->bindParam(":title", $this->title);
        $query->bindParam(":description", $this->description);
        $query->bindParam(":privacy", $this->privacy);
        $query->bindParam(":category", $this->category);
        $query->bindParam(":videoId", $videoId);

        return $query->execute();
    }
}