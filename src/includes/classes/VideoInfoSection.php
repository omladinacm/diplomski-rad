<?php

require_once 'VideoInfoControls.php';
require_once 'ButtonProvider.php';

class VideoInfoSection
{
    /**
     * @var \PDO
     */
    private PDO $con;
    /**
     * @var \Video
     */
    private Video $video;
    /**
     * @var \User
     */
    private User $userLoggedInObj;

    public function __construct(PDO $con, Video $video, User $userLoggedInObj)
    {
        $this->con = $con;
        $this->video = $video;
        $this->userLoggedInObj = $userLoggedInObj;
    }

    public function create(): string
    {
        return $this->createPrimaryInfo() . $this->createSecondaryInfo();
    }

    private function createPrimaryInfo(): string
    {
        $title = $this->video->getTitle();
        $views = $this->video->getViews();

        $videoInfoControls = new VideoInfoControls($this->video);
        $controls = $videoInfoControls->create();

        return "<div class='videoInfo'>
                    <h1>$title</h1>
                    <div class='bottomSection'>
                        <span class='viewCount'>$views views</span>
                        $controls
                    </div>
                </div>";
    }

    private function createSecondaryInfo(): string
    {
        $description = $this->video->getDescription();
        $uploadDate = $this->video->getUploadDate();
        $uploadedBy = $this->video->getUploadedBy();
        $profileButton = ButtonProvider::createUserProfileButton($this->con, $uploadedBy);

        if ($uploadedBy == $this->userLoggedInObj->getUsername()) {
            $actionButton = ButtonProvider::createEditVideoButton($this->video->getId());
        } else {
            $userToObject = new User($this->con, $uploadedBy);
            $actionButton = ButtonProvider::createSubscriberButton($userToObject, $this->userLoggedInObj);
        }

        return "<div class='secondaryInfo'>
                    <div class='topRow'>
                        $profileButton
                        <div class='uploadInfo'>
                            <span class='owner'>
                                <a href='profile.php?username=$uploadedBy'>
                                    $uploadedBy
                                </a>
                            </span>
                            <span class='date'>Published on $uploadDate</span>
                        </div>
                        $actionButton
                    </div>
                    
                    <div class='descriptionContainer'>
                        $description
                    </div>
                </div>";
    }
}