<?php

require_once('functions.php');

// The requested show id. Default is 1
session_start();
$id =$_REQUEST['id'];
$response = sendRabbit(array('type'=> 'getShow', 'data'=> $id));

// If the request was sent via AJAX
if(isset($_REQUEST['ajax'])) {
    session_start();
    if(isset($_SESSION['user'])){
        $user =$_SESSION['user'];
    }else{
        return 1;
    }

    if($_REQUEST['action'] == 'review_show') {
        echo sendRabbit(array('type' => 'reviewShow', 'data' => array('user' => $user, 'showID' => $id, 'review' => $_REQUEST['review'])));
    }

    exit();
}

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="bootstrap.min.css">
    <title>Reviews</title>
    <style type="text/css">
        nav {
            box-shadow: 2px 2px 10px #888;
        }
        button {
            border-radius: unset !important;
            box-shadow: 2px 2px #888;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-sm bg-light navbar-light">
    <a class="navbar-brand" href="index.html">Go to Home</a>
    <a id="welcome-message" class="ml-auto navbar-text mr-3"></a>
</nav>

<div class="container mt-4 mb-4">
    <h2 class="text-center"><?php echo $response['show']['name']; ?></h2>
    <hr>
    <form action="" id="review-form">
        <div class="form-group">
            <label for="review" class="control-label">Review:</label>
            <textarea required maxlength="2000" name="review" id="review" rows="8" placeholder=" Type review here.. " class="form-control"></textarea>
        </div>
        <button type="submit" class='btn btn-primary'>Submit</button>
    </form>

</div>


<script src="jquery.min.js"></script>
<script>
    $(window).on('load', function() {
        $.ajax({
            url: 'validate.php', // The php script that checks if the session exists
            type: 'GET',
            dataType: 'json'
        }).done(function(data) {
            if(data.set) { // A Session exists. Switch to the signed-in version
                $("#welcome-message").text("Welcome, " + data.username + "!");
                $("#welcome-message").attr("href", "./profile.php");
                $(".no-session").hide();
                $(".has-session").show();
            } else { // No session. Redirect to home
                window.location.href = "index.html";
            }
        })
    });

    $('#review-form').on('submit', function(e){
        e.stopPropagation();
        e.preventDefault();
        reviewShow();
    });

    function reviewShow() {
        let review = $("#review").val();
        $.ajax({
            url: 'reviews.php',
            type: 'POST',
            data: { "ajax": true, "action": "review_show", "review": review, "id": <?php echo $id ?> },
            dataType: 'json'
        }).done(function(data) {
            location.reload();
        });
    }

</script>

</body>
</html>
