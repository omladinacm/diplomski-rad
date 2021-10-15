<?php


class VideoUploadData
{
    private array $videoDataArray;
    private string $title;
    private string $description;
    private bool $privacy;
    private int $category;
    private string $uploadedBy;

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
     * @return array
     */
    public function getVideoDataArray(): array
    {
        return $this->videoDataArray;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return bool
     */
    public function getPrivacy(): bool
    {
        return $this->privacy;
    }

    /**
     * @return int
     */
    public function getCategory(): int
    {
        return $this->category;
    }

    /**
     * @return string
     */
    public function getUploadedBy(): string
    {
        return $this->uploadedBy;
    }

    public function updateDetails(PDO $con, int $videoId): bool
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