<?php

require_once "VideoUploadData.php";
class VideoProcessor
{
    /**
     * @var \PDO
     */
    private PDO $con;
    /**
     * Maximum file size limit for upload in bytes (1GB is set in PHP and Nginx configs)
     * @var int
     */
    private int $sizeLimit = 1073741824;
    /**
     * Array of supported input file video formats
     * @var array|string[]
     */
    private array $allowedTypes = ["mp4", "flv", "webm", "mkv", "vob", "ogv", "ogg", "avi", "wmv", "mov", "mpeg", "mpg"];
    /**
     * @var string
     */
    private string $ffmpegPath;
    /**
     * @var string
     */
    private string $ffprobePath;

    public function __construct(PDO $con)
    {
        $this->con = $con;
        $this->ffmpegPath = "ffmpeg";
        $this->ffprobePath = "ffprobe";
    }

    public function upload(VideoUploadData $videoUploadData)
    {
        $targetDir = "uploads/videos/";
        $videoData = $videoUploadData->getVideoDataArray();

        $tempFilePath = $targetDir . uniqid() . basename($videoData["name"]);
        $tempFilePath = str_replace(" ", "_", $tempFilePath);

        $isValidData = $this->processData($videoData, $tempFilePath);

        if (!$isValidData) {
            return false;
        }

        if (move_uploaded_file($videoData["tmp_name"], $tempFilePath)) {
            $finalFilePath = $targetDir . uniqid() . ".mp4";

            if (!$this->insertVideoData($videoUploadData, $finalFilePath)) {
                echo "Insert query failed";
                return false;
            }

            if (!$this->convertVideoToMp4($tempFilePath, $finalFilePath)) {
                echo "Upload failed";
                return false;
            }

            if (!$this->deleteFile($tempFilePath)) {
                echo "Upload failed";
                return false;
            }

            if (!$this->generateThumbnails($finalFilePath)) {
                echo "Upload failed - could not generate thumbnails\n";
                return false;
            }

            return true;
        }
    }

    private function processData($videoData, $filePath): bool
    {
        $videoType = pathinfo($filePath, PATHINFO_EXTENSION);

        if (!$this->isValidSize($videoData)) {
            echo "File too large!";
            return false;
        } elseif (!$this->isValidType($videoType)) {
            echo "Invalid file type";
            return false;
        } elseif ($this->hasError($videoData)) {
            echo "Error code: " . $videoData["error"];
            return false;
        }

        return true;
    }

    private function isValidSize($data): bool
    {
        return $data["size"] <= $this->sizeLimit;
    }

    private function isValidType(string $videoType): bool
    {
        $lowercase = strtolower($videoType);
        return in_array($lowercase, $this->allowedTypes);
    }

    private function hasError($videoData): bool
    {
        return $videoData["error"] != 0;
    }

    private function insertVideoData(VideoUploadData $videoUploadData, string $filePath): bool
    {
        $title = $videoUploadData->getTitle();
        $uploadedBy = $videoUploadData->getUploadedBy();
        $description = $videoUploadData->getDescription();
        $privacy = $videoUploadData->getPrivacy();
        $category = $videoUploadData->getCategory();

        $query = $this->con->prepare("INSERT INTO videos(title, uploadedBy, description, privacy, category, filePath)
        VALUES(:title, :uploadedBy, :description, :privacy, :category, :filePath)");
        $query->bindParam(":title", $title);
        $query->bindParam(":uploadedBy", $uploadedBy);
        $query->bindParam(":description", $description);
        $query->bindParam(":privacy", $privacy);
        $query->bindParam(":category", $category);
        $query->bindParam(":filePath", $filePath);

        return $query->execute();
    }

    public function convertVideoToMp4($tempFilePath, $finalFilePath): bool
    {
        $cmd = "$this->ffmpegPath -i $tempFilePath $finalFilePath 2>&1";

        $outputLog = [];

        exec($cmd, $outputLog, $returnCode);

        if ($returnCode !=0) {
            // Command failed
            foreach ($outputLog as $line) {
                echo $line . "<br>";
            }
            return false;
        }

        return true;
    }

    private function deleteFile($filePath): bool
    {
        if (!unlink($filePath)) {
            echo "Could not delete file\n";
            return false;
        }

        return true;
    }

    public function generateThumbnails($filePath): bool
    {
        $thumbnailSize = "210x118";
        $numThumbnails = 3;
        $pathToThumbnail = "uploads/videos/thumbnails";

        $duration = $this->getVideoDuration($filePath);

        $videoId = $this->con->lastInsertId();
        $this->updateDuration($duration, $videoId);

        for ($num=1; $num <= $numThumbnails; $num++) {
            $imageName = uniqid() . ".jpg";
            $interval = ($duration * 0.8) / $numThumbnails * $num;
            $fullThumbnailPath = "$pathToThumbnail/$videoId-$imageName";

            $cmd = "$this->ffmpegPath -i $filePath -ss $interval -s $thumbnailSize -vframes 1 $fullThumbnailPath 2>&1";

            $outputLog = [];

            exec($cmd, $outputLog, $returnCode);

            if ($returnCode !=0) {
                foreach ($outputLog as $line) {
                    echo $line . "<br>";
                }
            }

            $query = $this->con->prepare("INSERT INTO thumbnails(videoId, filePath, selected) 
                                        VALUES(:videoId, :filePath, :selected)");
            $query->bindParam(":videoId", $videoId);
            $query->bindParam(":filePath", $fullThumbnailPath);
            $query->bindParam(":selected", $selected);

            $selected = $num == 1 ? 1 : 0;

            $success = $query->execute();

            if (!$success) {
                echo "Error inserting thumbnail\n";
                return false;
            }
        }

        return true;
    }

    private function getVideoDuration($filePath): int
    {
        return (int)shell_exec("$this->ffprobePath -v error -show_entries format=duration -of default=noprint_wrappers=1:nokey=1 $filePath");
    }

    private function updateDuration($duration, $videoId)
    {
        $hours = floor($duration / 3600);
        $minutes = floor(($duration - ($hours*3600)) / 60);
        $seconds = floor($duration % 60);

        if ($hours < 1) {
            $hours = "";
        } else {
            $hours = $hours . ":";
        }
        $hours = ($hours < 1) ? "" : $hours . ":";
        $minutes = ($minutes < 10) ? "0" . $minutes . ":" : $minutes . ":";
        $seconds = ($seconds < 10) ? "0" . $seconds : $seconds;

        $duration = $hours.$minutes.$seconds;

        $query = $this->con->prepare("UPDATE videos SET duration=:duration WHERE id=:videoId");
        $query->bindParam(":duration", $duration);
        $query->bindParam(":videoId", $videoId);
        $query->execute();
    }
}