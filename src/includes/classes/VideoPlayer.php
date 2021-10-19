<?php


class VideoPlayer
{
    /**
     * @var \Video
     */
    private Video $video;

    public function __construct(Video $video)
    {
        $this->video = $video;
    }

    public function create($autoPlay): string
    {
        if ($autoPlay) {
            $autoPlay = "autoplay";
        } else {
            $autoPlay = "";
        }

        $filePath = $this->video->getFilePath();
        return "<video class='videoPlayer' controls $autoPlay>
                    <source src='$filePath' type='video/mp4'>
                    Your browser doesn't support video tag
                </video>";
    }
}