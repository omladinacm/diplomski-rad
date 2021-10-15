<?php


use JetBrains\PhpStorm\Pure;

class ButtonProvider
{
    public static string $signInFunction = "notSignedIn()";

    #[Pure] public static function createLink($link) {
        return User::isLoggedIn() ? $link : self::$signInFunction;
    }

    #[Pure] public static function createButton($text, $imageSrc, $action, $class): string
    {
        $image = ($imageSrc == null) ? "" : "<img src='$imageSrc' alt='button'>";

        $action = self::createLink($action);

        return "<button class='$class' onclick='$action'>
                    $image
                    <span class='text'>$text</span>
                </button>";
    }

    public static function createHyperlinkButton($text, $imageSrc, $href, $class): string
    {
        $image = ($imageSrc == null) ? "" : "<img src='$imageSrc' alt=''>";

        return "<a href='$href'>
                    <button class='$class'>
                        $image
                        <span class='text'>$text</span>
                    </button>
                </a>";
    }

    public static function createUserProfileButton(PDO $con, string $username): string
    {
        $userObj = new User($con, $username);
        $profilePic = $userObj->getProfilePicture();
        $link = "profile.php?username=$username";

        return "<a href='$link'>
                    <img src='$profilePic' class='profilePicture' alt=''>
                </a>";
    }

    #[Pure] public static function createEditVideoButton(int $videoId): string
    {
        $href = "editVideo.php?videoId=$videoId";

        $button = self::createHyperlinkButton("EDIT VIDEO", null, $href, "edit button");

        return "<div class='editVideoButtonContainer'>
                    $button
                </div>";
    }

    public static function createSubscriberButton(User $userToObj, User $userLoggedInObj): string
    {
        $userTo = $userToObj->getUsername();
        $userLoggedIn = $userLoggedInObj->getUsername();

        $isSubscribedTo = $userLoggedInObj->isSubscribedTo($userTo);
        $buttonText = $isSubscribedTo ? "SUBSCRIBED" : "SUBSCRIBE";
        $buttonText .= " " . $userToObj->getSubscriberCount();

        $buttonClass = $isSubscribedTo ? "unsubscribe button" : "subscribe button";

        $action = "subscribe(\"$userTo\", \"$userLoggedIn\", this)";

        $button = self::createButton($buttonText, null, $action, $buttonClass);

        return "<div class='subscribeButtonContainer'>
                    $button
                </div>";
    }

    public static function createUserProfileNavigationButton(PDO $con, $username): string
    {
        if (User::isLoggedIn()) {
            return ButtonProvider::createUserProfileButton($con, $username);
        } else {
            return "<a href='signIn.php'>
                        <span class='signInLink'>SIGN IN</span>
                    </a>";
        }
    }
}