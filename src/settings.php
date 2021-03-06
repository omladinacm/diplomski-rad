<?php
require_once 'includes/header.php';
require_once 'includes/classes/Account.php';
require_once 'includes/classes/FormSanitizer.php';
require_once 'includes/classes/SettingsFormProvider.php';
require_once 'includes/classes/Constants.php';

if (!User::isLoggedIn()) {
    header("Location: signIn.php");
}

$detailsMessage = "";
$passwordMessage = "";

$formProvider = new SettingsFormProvider();

if (isset($_POST["saveDetailsButton"])) {
    $account = new Account($con);

    $firstName = FormSanitizer::sanitizeFormString($_POST["firstName"]);
    $lastName = FormSanitizer::sanitizeFormString($_POST["lastName"]);
    $emailName = FormSanitizer::sanitizeFormEmail($_POST["email"]);

    if ($account->updateDetails($firstName, $lastName, $emailName, $userLoggedInObj->getUsername())) {
        $detailsMessage = "<div class='alert alert-success'>
                            <strong>SUCCESS!</strong> Details updated successfully!
                            </div>";
    } else {
        $errorMessage = $account->getFirstError();

        if ($errorMessage == "") $errorMessage = "Something went wrong!";

        $detailsMessage = "<div class='alert alert-danger'>
                            <strong>ERROR!</strong> $errorMessage
                            </div>";
    }
}

if (isset($_POST["savePasswordButton"])) {
    $account = new Account($con);

    $oldPassword = FormSanitizer::sanitizeFormPassword($_POST["oldPassword"]);
    $newPassword = FormSanitizer::sanitizeFormPassword($_POST["newPassword"]);
    $newPassword2 = FormSanitizer::sanitizeFormPassword($_POST["newPassword2"]);

    if ($account->updatePassword($oldPassword, $newPassword, $newPassword2, $userLoggedInObj->getUsername())) {
        $passwordMessage = "<div class='alert alert-success'>
                            <strong>SUCCESS!</strong> Password updated successfully!
                            </div>";
    } else {
        $errorMessage = $account->getFirstError();

        if ($errorMessage == "") $errorMessage = "Something went wrong!";

        $passwordMessage = "<div class='alert alert-danger'>
                            <strong>ERROR!</strong> $errorMessage
                            </div>";
    }
}
?>

<div class="settingsContainer column">
    <div class="formSection">
        <div class="message">
            <?php
            echo $detailsMessage;
            ?>
        </div>
        <?php
        echo $formProvider->createUserDetailsForm(
            $_POST["firstName"] ?? $userLoggedInObj->getFirstName(),
            $_POST["lastName"] ?? $userLoggedInObj->getLastName(),
            $_POST["email"] ?? $userLoggedInObj->getEmail()
        );
        ?>
    </div>

    <div class="formSection">
        <div class="message">
            <?php
            echo $passwordMessage;
            ?>
        </div>
        <?php
        echo $formProvider->createPasswordForm();
        ?>
    </div>
</div>
