<?php


class NavigationMenuProvider
{
    /**
     * @var \User
     */
    private User $userLoggedInObj;

    public function __construct(User $userLoggedInObj)
    {
        $this->userLoggedInObj = $userLoggedInObj;
    }

    public function create(): string
    {
        $menuHtml = $this->createNavItem("Home", "assets/images/icons/home.png", "index.php");
        $menuHtml .= $this->createNavItem("Trending", "assets/images/icons/trending.png", "trending.php");
        $menuHtml .= $this->createNavItem("Subscriptions", "assets/images/icons/subscriptions.png", "subscriptions.php");
        $menuHtml .= $this->createNavItem("Liked Videos", "assets/images/icons/thumb-up.png", "likedVideos.php");
        if (User::isLoggedIn()) {
            $menuHtml .= $this->createNavItem("Setting", "assets/images/icons/settings.png", "settings.php");
            $menuHtml .= $this->createNavItem("Log Out", "assets/images/icons/logout.png", "logout.php");
            $menuHtml .= $this->createSubscriptionsSection();
        }

        return "<div class='navigationItems'>
                    $menuHtml
                </div>";
    }

    private function createNavItem($text, $icon, $link): string
    {
        return "<div class='navigationItem'>
                    <a href='$link'>
                        <img src='$icon'>
                        <span>$text</span>                    
                    </a>
                </div>";
    }

    private function createSubscriptionsSection(): string
    {
        $subscriptions = $this->userLoggedInObj->getSubscriptions();

        $html = "<span class='heading'>Subscriptions</span>";
        foreach ($subscriptions as $subscription) {
            $subUsername = $subscription->getUsername();
            $html .= $this->createNavItem($subUsername, $subscription->getProfilePicture(), "profile.php?username=$subUsername");
        }

        return $html;
    }
}