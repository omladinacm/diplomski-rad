<?php


class VideoDetailsFormProvider
{
    /**
     * PDO DB connection
     *
     * @var \PDO $con
     */
    private PDO $con;

    public function __construct(PDO $con)
    {
        $this->con = $con;
    }

    /**
     * @return string
     */
    public function createUploadForm(): string
    {
        $fileInput = $this->createFileInput();
        $titleInput = $this->createTitleInput();
        $descriptionInput = $this->createDescriptionInput();
        $privacyInput = $this->createPrivacyInput();
        $categoriesInput = $this->createCategoriesInput();
        $uploadButton = $this->createUploadButton();
        return "<form action='processing.php' method='POST' enctype='multipart/form-data'>
                    $fileInput
                    $titleInput
                    $descriptionInput
                    $privacyInput
                    $categoriesInput
                    $uploadButton
                </form>";
    }

    /**
     * @param \Video $video
     *
     * @return string
     */
    public function createEditDetailsForm(Video $video): string
    {
        $titleInput = $this->createTitleInput($video->getTitle());
        $descriptionInput = $this->createDescriptionInput($video->getDescription());
        $privacyInput = $this->createPrivacyInput($video->getPrivacy());
        $categoriesInput = $this->createCategoriesInput($video->getCategory());
        $saveButton = $this->createSaveButton();
        return "<form method='POST'>
                    $titleInput
                    $descriptionInput
                    $privacyInput
                    $categoriesInput
                    $saveButton
                </form>";
    }

    /**
     * @return string
     */
    private function createFileInput(): string
    {
        return "<div class='form-group'>
                    <input type='file' class='form-control-file' id='exampleFormControlFile1' name='fileInput' required>
                </div>";
    }

    /**
     * @param null $value
     *
     * @return string
     */
    private function createTitleInput($value = null): string
    {
        if ($value == null) $value = "";

        return "<div class='form-group'>
                    <input class='form-control' type='text' placeholder='Title' name='titleInput' value='$value'>
                </div>";
    }

    /**
     * @param null $value
     *
     * @return string
     */
    private function createDescriptionInput($value = null): string
    {
        if ($value == null) $value = "";

        return "<div class='form-group'>
                    <textarea class='form-control' placeholder='Description' name='descriptionInput' rows='3'>$value</textarea>
                </div>";
    }

    /**
     * @param int $value
     *
     * @return string
     */
    private function createPrivacyInput(int $value = 0): string
    {
        if ($value == null) $value = "";

        $privateSelected = ($value == 0) ? "selected='selected'" : "";
        $publicSelected = ($value == 1) ? "selected='selected'" : "";
        return "<div class='form-group'>
                    <select class='form-control' name='privacyInput'>
                      <option value='0' $privateSelected>Private</option>
                      <option value='1' $publicSelected>Public</option>
                    </select>
                </div>";
    }

    /**
     * @param null $value
     *
     * @return string
     */
    private function createCategoriesInput($value = null): string
    {
        if ($value == null) $value = "";

        $query = $this->con->prepare("SELECT * FROM categories");
        $query->execute();

        $html = "<div class='form-group'>
                    <select class='form-control' name='categoryInput'>";

        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $name = $row["name"];
            $id = $row["id"];
            $selected = ($id == $value) ? "selected='selected'" : "";

            $html .= "<option $selected value='$id'>$name</option>";
        }

        $html .= "</select>
                </div>";

        return $html;
    }

    private function createUploadButton(): string
    {
        return "<button type='submit' class='btn btn-primary' name='uploadButton'>Upload</button>";
    }

    private function createSaveButton(): string
    {
        return "<button type='submit' class='btn btn-primary' name='saveButton'>Save</button>";
    }
}