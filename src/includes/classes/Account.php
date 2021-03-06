<?php


class Account
{
    private PDO $con;
    private array $errorArray = [];

    public function __construct(PDO $con)
    {
        $this->con = $con;
    }

    public function register($firstName, $lastName, $username, $email, $email2, $password, $password2): bool
    {
        $this->validateFirstName($firstName);
        $this->validateLastName($lastName);
        $this->validateUsername($username);
        $this->validateEmail($email, $email2);
        $this->validatePassword($password, $password2);

        if (empty($this->errorArray)) {
            return $this->insertUserDetails($firstName, $lastName, $username, $email, $password);
        } else {
            return false;
        }
    }

    public function updateDetails($firstName, $lastName, $email, $username): bool
    {
        $this->validateFirstName($firstName);
        $this->validateLastName($lastName);
        $this->validateNewEmail($email, $username);

        if (empty($this->errorArray)) {
            $query = $this->con->prepare("UPDATE users SET firstName = :firstName, lastName = :lastName, email = :email WHERE username = :username");
            $query->bindParam(":firstName", $firstName);
            $query->bindParam(":lastName", $lastName);
            $query->bindParam(":email", $email);
            $query->bindParam(":username", $username);

            return $query->execute();
        } else {
            return false;
        }
    }

    public function insertUserDetails($firstName, $lastName, $username, $email, $password): bool
    {
        $password = hash("sha512", $password);
        $profilePic = "assets/images/profilePictures/default.png";

        $query = $this->con->prepare("INSERT INTO users(firstName, lastName, username, email, password, profilePicture) 
                                    VALUES (:firstName, :lastName, :username, :email, :password, :profilePicture)");
        $query->bindParam(":firstName", $firstName);
        $query->bindParam(":lastName", $lastName);
        $query->bindParam(":username", $username);
        $query->bindParam(":email", $email);
        $query->bindParam(":password", $password);
        $query->bindParam(":profilePicture", $profilePic);

        return $query->execute();
    }

    private function validateFirstName($firstName)
    {
        if (strlen($firstName) > 25 || strlen($firstName) < 2) {
            $this->errorArray[] = Constants::$firstNameCharacters;
        }
    }

    private function validateLastName($lastName)
    {
        if (strlen($lastName) > 25 || strlen($lastName) < 2) {
            $this->errorArray[] = Constants::$lastNameCharacters;
        }
    }

    private function validateUsername($username)
    {
        if (strlen($username) > 25 || strlen($username) < 5) {
            $this->errorArray[] = Constants::$usernameCharacters;
            return;
        }

        $query = $this->con->prepare("SELECT username FROM users WHERE username=:username");
        $query->bindParam(":username", $username);
        $query->execute();

        if ($query->rowCount() != 0) {
            $this->errorArray[] = Constants::$usernameTaken;
        }
    }

    private function validateEmail($email, $email2)
    {
        if ($email != $email2) {
            $this->errorArray[] = Constants::$emailsDoNotMatch;
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->errorArray[] = Constants::$emailInvalid;
            return;
        }

        $query = $this->con->prepare("SELECT email FROM users WHERE email=:email");
        $query->bindParam(":email", $email);
        $query->execute();

        if ($query->rowCount() != 0) {
            $this->errorArray[] = Constants::$emailTaken;
        }
    }

    private function validateNewEmail($email, $username)
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->errorArray[] = Constants::$emailInvalid;
            return;
        }

        $query = $this->con->prepare("SELECT email FROM users WHERE email=:email AND username != :username");
        $query->bindParam(":email", $email);
        $query->bindParam(":username", $username);
        $query->execute();

        if ($query->rowCount() != 0) {
            $this->errorArray[] = Constants::$emailTaken;
        }
    }

    private function validatePassword($password, $password2)
    {
        if ($password != $password2) {
            $this->errorArray[] = Constants::$passwordsDoNotMatch;
            return;
        }

        if (preg_match("/[^A-Za-z0-9]/", $password)) {
            $this->errorArray[] = Constants::$passwordNotAlphanumeric;
            return;
        }

        if (strlen($password) > 30 || strlen($password) < 5) {
            $this->errorArray[] = Constants::$passwordLength;
            return;
        }
    }

    public function getError(string $error)
    {
        if (in_array($error, $this->errorArray)) {
            return "<span class='errorMessage'>$error</span>";
        }
    }

    public function login(string $username, string $password): bool
    {
        $password = hash("sha512", $password);

        $query = $this->con->prepare("SELECT * FROM users WHERE username = :username AND password = :password");
        $query->bindParam(":username", $username);
        $query->bindParam(":password", $password);

        $query->execute();

        if ($query->rowCount() == 1) {
            return true;
        } else {
            $this->errorArray[] = Constants::$loginFailed;
            return false;
        }
    }

    public function getFirstError()
    {
        if (!empty($this->errorArray)) {
            return $this->errorArray[0];
        } else {
            return "";
        }
    }

    public function updatePassword(string $oldPassword, string $newPassword, string $newPassword2, string $username): bool
    {
        $this->validateOldPassword($oldPassword, $username);
        $this->validatePassword($newPassword, $newPassword2);

        if (empty($this->errorArray)) {
            $newPassword = hash("sha512", $newPassword);
            $query = $this->con->prepare("UPDATE users SET password = :newPassword WHERE username = :username");
            $query->bindParam(":newPassword", $newPassword);
            $query->bindParam(":username", $username);

            return $query->execute();
        } else {
            return false;
        }
    }

    private function validateOldPassword(string $oldPassword, string $username)
    {
        $oldPassword = hash("sha512", $oldPassword);

        $query = $this->con->prepare("SELECT * FROM users WHERE username = :username AND password = :password");
        $query->bindParam(":username", $username);
        $query->bindParam(":password", $oldPassword);

        $query->execute();

        if ($query->rowCount() == 0) {
            $this->errorArray[] = Constants::$passwordIncorrect;
        }
    }
}