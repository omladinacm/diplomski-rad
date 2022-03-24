<?php


class SelectThumbnail
{
    /**
     * @var \PDO
     */
    private PDO $con;
    /**
     * @var \Video
     */
    private Video $video;

    public function __construct(PDO $con, Video $video)
    {
        $this->con = $con;
        $this->video = $video;
    }

    public function create(): string
    {
        $thumbnailData = $this->getThumbnailData();

        $html = "";

        foreach ($thumbnailData as $data) {
            $html .= $this->createThumbnailItem($data);
        }

        return "<div class='thumbnailItemsContainer'>
                    $html
                </div>";
    }

    private function getThumbnailData(): array
    {
        $data = [];
        $videoId = $this->video->getId();

        $query = $this->con->prepare("SELECT * FROM thumbnails WHERE videoId = :videoId");
        $query->bindParam(":videoId", $videoId);
        $query->execute();

        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }

        return $data;
    }

    private function createThumbnailItem($data): string
    {
        $id = $data["id"];
        $url = $data["filePath"];
        $videoId = $data["videoId"];
        $selected = $data["selected"] == 1 ? "selected" : "";

        return "<div class='thumbnailItem $selected' onclick='setNewThumbnail($id, $videoId, this)'>
                    <img src='$url' alt='thumbnail item'>
                </div>";
    }
}