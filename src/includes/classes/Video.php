<?php


class Video
{
    private PDO $con;
    private mixed $sqlData;
    private User $userLoggedInObj;

    public function __construct(PDO $con, $input, User $userLoggedInObj)
    {
        $this->con = $con;
        $this->userLoggedInObj = $userLoggedInObj;

        if (is_array($input)) {
            $this->sqlData = $input;
        } else {
            $query = $this->con->prepare("SELECT * FROM videos WHERE id = :id");
            $query->bindParam(":id", $input);
            $query->execute();

            $this->sqlData = $query->fetch(PDO::FETCH_ASSOC);
        }
    }

    public function getId(): int
    {
        return $this->sqlData["id"];
    }

    public function getUploadedBy(): string
    {
        return $this->sqlData["uploadedBy"];
    }

    public function getTitle(): string
    {
        return $this->sqlData["title"];
    }

    public function getDescription(): string
    {
        return $this->sqlData["description"];
    }

    public function getPrivacy(): bool
    {
        return $this->sqlData["privacy"];
    }

    public function getFilePath(): string
    {
        return $this->sqlData["filePath"];
    }

    public function getCategory(): int
    {
        return $this->sqlData["category"];
    }

    public function getUploadDate(): string
    {
        return date("M j, Y",strtotime($this->sqlData["uploadDate"]));
    }

    public function getTimeStamp(): string
    {
        return date("M jS, Y",strtotime($this->sqlData["uploadDate"]));
    }

    public function getViews(): int
    {
        return $this->sqlData["views"];
    }

    public function getDuration(): string
    {
        return $this->sqlData["duration"];
    }

    public function incrementViews()
    {
        $videoId = $this->getId();

        $query = $this->con->prepare("UPDATE videos SET views = views + 1 WHERE id=:id");
        $query->bindParam(":id", $videoId);

        $query->execute();

        $this->sqlData["views"] = $this->sqlData["views"] + 1;
    }

    public function getLikes(): int
    {
        $videoId = $this->getId();

        $query = $this->con->prepare("SELECT count(*) as 'count' FROM likes WHERE videoId = :videoId");
        $query->bindParam(":videoId", $videoId);
        $query->execute();

        $data = $query->fetch(PDO::FETCH_ASSOC);

        return $data["count"];
    }

    public function getDislikes(): int
    {
        $videoId = $this->getId();

        $query = $this->con->prepare("SELECT count(*) as 'count' FROM dislikes WHERE videoId = :videoId");
        $query->bindParam(":videoId", $videoId);
        $query->execute();

        $data = $query->fetch(PDO::FETCH_ASSOC);

        return $data["count"];
    }

    public function like(): bool|string
    {
        $videoId = $this->getId();
        $username = $this->userLoggedInObj->getUsername();

        if ($this->wasLikedBy()) {
            // User has already liked
            $query = $this->con->prepare("DELETE FROM likes WHERE username = :username AND videoId = :videoId");
            $query->bindParam(":username", $username);
            $query->bindParam(":videoId", $videoId);
            $query->execute();

            $result = ["likes" => -1, "dislikes" => 0];
        } else {
            $query = $this->con->prepare("DELETE FROM dislikes WHERE username = :username AND videoId = :videoId");
            $query->bindParam(":username", $username);
            $query->bindParam(":videoId", $videoId);
            $query->execute();
            $count = $query->rowCount();

            $query = $this->con->prepare("INSERT INTO likes (username, videoId) VALUES (:username, :videoId) ");
            $query->bindParam(":username", $username);
            $query->bindParam(":videoId", $videoId);
            $query->execute();

            $result = ["likes" => 1, "dislikes" => 0 - $count];
        }

        return json_encode($result);
    }

    public function wasLikedBy(): bool
    {
        $videoId = $this->getId();
        $username = $this->userLoggedInObj->getUsername();

        $query = $this->con->prepare("SELECT * FROM likes WHERE username = :username AND videoId = :videoId");
        $query->bindParam(":username", $username);
        $query->bindParam(":videoId", $videoId);

        $query->execute();

        return $query->rowCount() > 0;
    }

    public function wasDislikedBy(): bool
    {
        $videoId = $this->getId();
        $username = $this->userLoggedInObj->getUsername();

        $query = $this->con->prepare("SELECT * FROM dislikes WHERE username = :username AND videoId = :videoId");
        $query->bindParam(":username", $username);
        $query->bindParam(":videoId", $videoId);

        $query->execute();

        return $query->rowCount() > 0;
    }

    public function dislike(): bool|string
    {
        $videoId = $this->getId();
        $username = $this->userLoggedInObj->getUsername();

        if ($this->wasDislikedBy()) {
            // User has already disliked
            $query = $this->con->prepare("DELETE FROM dislikes WHERE username = :username AND videoId = :videoId");
            $query->bindParam(":username", $username);
            $query->bindParam(":videoId", $videoId);
            $query->execute();

            $result = ["likes" => 0, "dislikes" => -1];
        } else {
            $query = $this->con->prepare("DELETE FROM likes WHERE username = :username AND videoId = :videoId");
            $query->bindParam(":username", $username);
            $query->bindParam(":videoId", $videoId);
            $query->execute();
            $count = $query->rowCount();

            $query = $this->con->prepare("INSERT INTO dislikes (username, videoId) VALUES (:username, :videoId) ");
            $query->bindParam(":username", $username);
            $query->bindParam(":videoId", $videoId);
            $query->execute();

            $result = ["likes" => 0 - $count, "dislikes" => 1];
        }

        return json_encode($result);
    }

    public function getNumberOfComments(): int
    {
        $videoId = $this->getId();

        $query = $this->con->prepare("SELECT * FROM comments WHERE videoId = :videoId");
        $query->bindParam(":videoId", $videoId);
        $query->execute();

        return $query->rowCount(); //Replace with count(*)
    }

    public function getComments(): array
    {
        $videoId = $this->getId();

        $query = $this->con->prepare("SELECT * FROM comments WHERE videoId = :videoId AND responseToComment = 0 ORDER BY datePosted DESC");
        $query->bindParam(":videoId", $videoId);
        $query->execute();

        $comments = [];

        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $comment = new Comment($this->con, $row, $this->userLoggedInObj, $videoId);
            array_push($comments, $comment);
        }

        return $comments;
    }

    public function getThumbnail(): string
    {
        $videoId = $this->getId();

        $query = $this->con->prepare("SELECT filePath FROM thumbnails WHERE videoId = :videoId AND selected = 1");
        $query->bindParam(":videoId", $videoId);
        $query->execute();

        return $query->fetchColumn();
    }
}