<?php


use JetBrains\PhpStorm\Pure;

class SettingsFormProvider
{
    #[Pure] public function createUserDetailsForm($firstName, $lastName, $email): string
    {
        $firstNameInput = $this->createFirstNameInput($firstName);
        $lastNameInput = $this->createLastNameInput($lastName);
        $emailInput = $this->createEmailInput($email);
        $saveButton = $this->createSaveUserDetailsButton();

        return "<form action='settings.php' method='POST'>
                    <span class='title'>User details</span>
                    $firstNameInput
                    $lastNameInput
                    $emailInput
                    $saveButton
                </form>";
    }

    #[Pure] public function createPasswordForm(): string
    {
        $oldPasswordInput = $this->createPasswordInput("oldPassword", "Old password");
        $newPasswordInput = $this->createPasswordInput("newPassword", "New password");
        $newPassword2Input = $this->createPasswordInput("newPassword2", "Confirm new password");
        $saveButton = $this->createSavePasswordButton();

        return "<form action='settings.php' method='POST' enctype='multipart/form-data'>
                    <span class='title'>Update password</span>
                    $oldPasswordInput
                    $newPasswordInput
                    $newPassword2Input
                    $saveButton
                </form>";
    }

    private function createFirstNameInput($value): string
    {
        if ($value == null) $value = "";

        return "<div class='form-group'>
                    <input class='form-control' type='text' placeholder='First name' name='firstName' value='$value' required>
                </div>";
    }

    private function createLastNameInput($value): string
    {
        if ($value == null) $value = "";

        return "<div class='form-group'>
                    <input class='form-control' type='text' placeholder='Last name' name='lastName' value='$value' required>
                </div>";
    }

    private function createEmailInput($value): string
    {
        if ($value == null) $value = "";

        return "<div class='form-group'>
                    <input class='form-control' type='email' placeholder='Email' name='email' value='$value' required>
                </div>";
    }

    private function createPasswordInput($name, $placeholder): string
    {
        return "<div class='form-group'>
                    <input class='form-control' type='password' placeholder='$placeholder' name='$name' required>
                </div>";
    }

    private function createSaveUserDetailsButton(): string
    {
        return "<button type='submit' class='btn btn-primary' name='saveDetailsButton'>Save</button>";
    }

    private function createSavePasswordButton(): string
    {
        return "<button type='submit' class='btn btn-primary' name='savePasswordButton'>Save</button>";
    }
}