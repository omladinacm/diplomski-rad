<?php


use JetBrains\PhpStorm\ArrayShape;

class ProfileData
{
    /**
     * @var \PDO
     */
    private PDO $con;
    /**
     * @var string|\User
     */
    private string|User $profileUserObj;

    public function __construct(PDO $con, string $profileUsername)
    {
        $this->con = $con;
        $this->profileUserObj = new User($con,$profileUsername);
    }

    public function getProfileUserObj(): User|string
    {
        return $this->profileUserObj;
    }

    public function getProfileUsername(): string
    {
        return $this->profileUserObj->getUsername();
    }

    public function userExists(): bool
    {
        $profileUsername = $this->getProfileUsername();
        $query = $this->con->prepare("SELECT * FROM users WHERE username = :username");
        $query->bindParam(":username", $profileUsername);
        $query->execute();

        return $query->rowCount() != 0;
    }

    public function getCoverPhoto(): string
    {
        return "assets/images/coverPhotos/default-cover-photo.jpg";
    }

    public function getProfileUserFullName(): string
    {
        return $this->profileUserObj->getName();
    }

    public function getProfilePicture(): string
    {
        return $this->profileUserObj->getProfilePicture();
    }

    public function getSubscriberCount(): int
    {
        return $this->profileUserObj->getSubscriberCount();
    }

    public function getUsersVideos(): array
    {
        $username = $this->getProfileUsername();

        $query = $this->con->prepare("SELECT * FROM videos WHERE uploadedBy = :uploadedBy ORDER BY uploadDate DESC");
        $query->bindParam(":uploadedBy", $username);
        $query->execute();

        $videos = [];
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $videos[] = new Video($this->con, $row, $this->profileUserObj);
        }

        return $videos;
    }

    #[ArrayShape([
        "Name" => "string",
        "Username" => "string",
        "Subscribers" => "int",
        "Total views" => "int",
        "Sign up date" => "string"
    ])] public function getAllUserDetails(): array
    {
        return [
            "Name" => $this->getProfileUserFullName(),
            "Username" => $this->getProfileUsername(),
            "Subscribers" => $this->getSubscriberCount(),
            "Total views" => $this->getTotalViews(),
            "Sign up date" => $this->getSignUpDate()
        ];
    }

    private function getTotalViews(): int
    {
        $username = $this->getProfileUsername();

        $query = $this->con->prepare("SELECT SUM(views) FROM videos WHERE uploadedBy = :uploadedBy");
        $query->bindParam(":uploadedBy", $username);
        $query->execute();

        return $query->fetchColumn();
    }

    private function getSignUpDate(): string
    {
        $date = strtotime($this->profileUserObj->getSignUpDate());

        return date("F jS, Y", $date);
    }
}