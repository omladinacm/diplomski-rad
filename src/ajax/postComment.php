<?php
require_once '../includes/config.php';
require_once '../includes/classes/User.php';
require_once '../includes/classes/Comment.php';

if (isset($_POST['commentText']) && isset($_POST['postedBy']) && isset($_POST['videoId'])) {

    $postedBy = $_POST['postedBy'];
    $videoId = $_POST['videoId'];
    $responseToComment = $_POST['responseToComment'] ?? 0;
    $body = $_POST['commentText'];

    $userLoggedInObj = new User($con, $_SESSION['userLoggedIn']);

    $query = $con->prepare("INSERT INTO comments (postedBy, videoId, responseToComment, body) 
                            VALUES (:postedBy, :videoId, :responseToComment, :body)");
    $query->bindParam(":postedBy", $postedBy);
    $query->bindParam(":videoId", $videoId);
    $query->bindParam(":responseToComment", $responseToComment);
    $query->bindParam(":body", $body);


    $query->execute();


    $newComment = new Comment($con, $con->lastInsertId(), $userLoggedInObj, $videoId);

    echo $newComment->create();
} else {
    echo "One or more parameters are not passed to subscribe.php file";
}