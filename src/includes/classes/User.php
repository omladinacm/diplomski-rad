<?php


class User
{
    private PDO $con;
    private mixed $sqlData;

    public function __construct(PDO $con, string $username)
    {
        $this->con = $con;

        $query = $this->con->prepare("SELECT * FROM users WHERE username = :username");
        $query->bindParam(":username", $username);
        $query->execute();

        $this->sqlData = $query->fetch(PDO::FETCH_ASSOC);
    }

    public static function isLoggedIn(): bool
    {
        return isset($_SESSION["userLoggedIn"]);
    }

    public function getUsername()
    {
        return $this->sqlData["username"];
    }

    public function getName(): string
    {
        return $this->sqlData["firstName"] . " " . $this->sqlData["lastName"];
    }

    public function getFirstName(): string
    {
        return $this->sqlData["firstName"];
    }

    public function getLastName(): string
    {
        return $this->sqlData["lastName"];
    }

    public function getEmail(): string
    {
        return $this->sqlData["email"];
    }

    public function getProfilePicture(): string
    {
        return $this->sqlData["profilePicture"];
    }

    public function getSignUpDate(): string
    {
        return $this->sqlData["signUpDate"];
    }

    public function isSubscribedTo($userTo): bool
    {
        $username = $this->getUsername();

        $query = $this->con->prepare("SELECT * FROM subscribers WHERE userTo = :userTo AND userFrom = :userFrom");
        $query->bindParam(":userTo", $userTo);
        $query->bindParam(":userFrom", $username);
        $query->execute();

        return $query->rowCount() > 0;
    }

    public function getSubscriberCount(): int
    {
        $username = $this->getUsername();

        $query = $this->con->prepare("SELECT * FROM subscribers WHERE userTo = :userTo");
        $query->bindParam(":userTo", $username);
        $query->execute();

        return $query->rowCount();
    }

    public function getSubscriptions(): array
    {
        $username = $this->getUsername();

        $query = $this->con->prepare("SELECT userTo FROM subscribers WHERE userFrom = :userFrom");
        $query->bindParam(":userFrom", $username);
        $query->execute();

        $subs = [];

        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $user = new User($this->con, $row["userTo"]);
            array_push($subs, $user);
        }

        return $subs;
    }
}