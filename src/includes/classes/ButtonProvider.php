<?php


class ButtonProvider
{
    public static $signInFunction = "notSignedIn()";

    public static function createLink($link) {
        return User::isLoggedIn() ? $link : self::$signInFunction;
    }

    public static function createButton($text, $imageSrc, $action, $class)
    {
        $image = ($imageSrc == null) ? "" : "<img src='$imageSrc'>";

        $action = self::createLink($action);

        return "<button class='$class' onclick='$action'>
                    $image
                    <span class='text'>$text</span>
                </button>";
    }

    public static function createHyperlinkButton($text, $imageSrc, $href, $class)
    {
        $image = ($imageSrc == null) ? "" : "<img src='$imageSrc'>";

        return "<a href='$href'>
                    <button class='$class'>
                        $image
                        <span class='text'>$text</span>
                    </button>
                </a>";
    }

    public static function createUserProfileButton(PDO $con, string $username)
    {
        $userObj = new User($con, $username);
        $profilePic = $userObj->getProfilePicture();
        $link = "profile.php?username=$username";

        $html = "<a href='$link'>
                    <img src='$profilePic' class='profilePicture'>
                </a>";
        return $html;
    }

    public static function createEditVideoButton(int $videoId)
    {
        $href = "editVideo.php?videoId=$videoId";

        $button = self::createHyperlinkButton("EDIT VIDEO", null, $href, "edit button");

        $html = "<div class='editVideoButtonContainer'>
                    $button
                </div>";

        return $html;
    }

    public static function createSubscriberButton(PDO $con, User $userToObj, User $userLoggedInObj)
    {
        $userTo = $userToObj->getUsername();
        $userLoggedIn = $userLoggedInObj->getUsername();

        $isSubscribedTo = $userLoggedInObj->isSubscribedTo($userTo);
        $buttonText = $isSubscribedTo ? "SUBSCRIBED" : "SUBSCRIBE";
        $buttonText .= " " . $userToObj->getSubscriberCount();

        $buttonClass = $isSubscribedTo ? "unsubscribe button" : "subscribe button";

        $action = "subscribe(\"$userTo\", \"$userLoggedIn\", this)";

        $button = self::createButton($buttonText, null, $action, $buttonClass);

        $html = "<div class='subscribeButtonContainer'>
                    $button
                </div>";

        return $html;
    }

    public static function createUserProfileNavigationButton($con, $username)
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