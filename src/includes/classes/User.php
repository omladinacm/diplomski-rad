<?php


class User
{
    /**
     * @var \PDO
     */
    private PDO $con;
    /**
     * @var array|bool|mixed
     */
    private array|bool $sqlData;

    public function __construct(PDO $con, string $username)
    {
        $this->con = $con;

        $query = $this->con->prepare("SELECT * FROM users WHERE username = :username");
        $query->bindParam(":username", $username);
        $query->execute();

        $this->sqlData = $query->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * @return bool
     */
    public static function isLoggedIn(): bool
    {
        return isset($_SESSION["userLoggedIn"]);
    }

    /**
     * @return string|null
     */
    public function getUsername(): ?string
    {
        return $this->sqlData["username"];
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->sqlData["firstName"] . " " . $this->sqlData["lastName"];
    }

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->sqlData["firstName"];
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->sqlData["lastName"];
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->sqlData["email"];
    }

    /**
     * @return string
     */
    public function getProfilePicture(): string
    {
        return $this->sqlData["profilePicture"];
    }

    /**
     * @return string
     */
    public function getSignUpDate(): string
    {
        return $this->sqlData["signUpDate"];
    }

    /**
     * @param $userTo
     *
     * @return bool
     */
    public function isSubscribedTo($userTo): bool
    {
        $username = $this->getUsername();

        $query = $this->con->prepare("SELECT * FROM subscribers WHERE userTo = :userTo AND userFrom = :userFrom");
        $query->bindParam(":userTo", $userTo);
        $query->bindParam(":userFrom", $username);
        $query->execute();

        return $query->rowCount() > 0;
    }

    /**
     * @return int
     */
    public function getSubscriberCount(): int
    {
        $username = $this->getUsername();

        $query = $this->con->prepare("SELECT * FROM subscribers WHERE userTo = :userTo");
        $query->bindParam(":userTo", $username);
        $query->execute();

        return $query->rowCount();
    }

    /**
     * @return array
     */
    public function getSubscriptions(): array
    {
        $username = $this->getUsername();

        $query = $this->con->prepare("SELECT userTo FROM subscribers WHERE userFrom = :userFrom");
        $query->bindParam(":userFrom", $username);
        $query->execute();

        $subs = [];

        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $user = new User($this->con, $row["userTo"]);
            $subs[] = $user;
        }

        return $subs;
    }
}