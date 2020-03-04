<?php

require_once('function.php');

// The requested show id. Default is 1
$showId = isset($_REQUEST['id']) && !empty($_REQUEST['id']) ? trim($_REQUEST['id']) : 1;
$action = isset($_REQUEST['action']) && !empty($_REQUEST['action']) ? trim($_REQUEST['action']) : 'get_show';

if($action == 'get_show')
    $data = ['action' => 'get_show', 'show_id' => $showId];
if($action == 'get_shows')
    $data = ['action' => 'get_shows'];
if($action == 'like_show')
    $data = ['action' => 'like_show', 'show_id' => $showId];
if($action == 'follow_show')
    $data = ['action' => 'follow_show', 'show_id' => $showId];
if($action == 'unlike_show')
    $data = ['action' => 'unlike_show', 'show_id' => $showId];
if($action == 'unfollow_show')
    $data = ['action' => 'unfollow_show', 'show_id' => $showId];

$response = sRabbit($data);

// If the request was sent via AJAX
if(isset($_REQUEST['ajax'])) {
    echo json_encode($response);
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
    <title>Shows</title>
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

<nav class="no-session navbar navbar-expand-sm bg-light navbar-light">
    <a class="navbar-brand" href="index.html">Go to Home</a>
    <ul class="navbar-nav ml-auto">
        <li class="nav-item">
            <a class="nav-link" href="login.html"><button type="button" class="btn btn-primary">Login</button></a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="register.html"><button type="button" class="btn btn-success">Register</button></a>
        </li>
    </ul>
</nav>

<nav style="display: none" class="has-session navbar navbar-expand-sm bg-light navbar-light">
    <a class="navbar-brand" href="index.html">Home</a>
    <span id="welcome-message" class="ml-auto navbar-text mr-3"></span>
</nav>


<div class="container mt-4 mb-4">
    <h2 class="text-center">Show Details</h2>
    <hr>

    <div class="row">
        <div class="col-sm-4">
            <img id="show-poster-graphic" src="<?php echo $response['poster_graphic'] ?>" alt="Show's Poster Graphic" class="img-fluid">
        </div>
        <div class="col-sm-8">
            <h4>Show: <span id="show-name" class="text-muted"><?php echo $response['name'] ?></span></h4>
            <h4>Genre: <span id="show-genre" class="text-muted"><?php echo $response['genre'] ?></span></h4>
            <h4>Upcoming Episodes: </h4>
            <table id="upcoming-episodes" class="table table-bordered table-striped table-hover">
                <thead>
                <tr>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Channel</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach($response['upcoming_episodes'] as $upcomingEpisode) { ?>
                    <tr>
                        <td><?php echo $upcomingEpisode['date'] ?></td>
                        <td><?php echo $upcomingEpisode['time'] ?></td>
                        <td><?php echo $upcomingEpisode['channel'] ?></td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>

            <div class="mt-4">
                <a id="like-button" data-liked="<?php echo $response['liked'] == true ? 1 : 0 ?>" style="display: none;" href="#" class="has-session"><button class="btn btn-primary"><?php echo $response['liked'] == true ? 'UNLIKE' : 'LIKE' ?></button></a>
                <a id="follow-button" data-following="<?php echo $response['following'] == true ? 1 : 0 ?>" style="display: none;" href="#" class="has-session"><button class="btn btn-primary"><?php echo $response['following'] == true ? 'UNFOLLOW' : 'FOLLOW' ?></button></a>
                <a id="forums-button" href="#"><button class="btn btn-primary">Go to Forums</button></a>
            </div>
        </div>
    </div>

    <hr>

</div>


<script src="jquery.min.js"></script>
<script>
    $(window).on('load', function() {
        $.ajax({
            url: 'validate.php', // The php script that checks if the session exists
            type: 'GET',
            dataType: 'json'
        })
            .done(function(data) {
                if(data.set) { // A Session exists. Switch to the signed-in version
                    $("#welcome-message").text("Welcome, " + data.username + "!");
                    $(".no-session").hide();
                    $(".has-session").show();
                } else { // No session. Switch to the signed-out version
                    $("#welcome-message").text("");
                    $(".has-session").hide();
                    $(".no-session").show();
                }
            })

    });

    $(document).ready(function() {
        // Toggle "Liked" status
        $("#like-button").on("click", function(e) {
            $(this).data('liked') === 1 ? unlikeShow() : likeShow();
        });

        // Toggle "Following" status
        $("#follow-button").on("click", function(e) {
            $(this).data('following') === 1 ? unfollowShow() : followShow();
        });
    });

    function likeShow() {
        $("#like-button").data('liked', 1);
        $("#like-button button").text('UNLIKE');

        $.ajax({
            url: 'shows.php',
            type: 'POST',
            data: { "ajax": true, "action": "like_show", "id": <?php echo $showId ?> },
            dataType: 'json'
        }).done(function(data) {

        })
    }

    function unlikeShow() {
        $("#like-button").data('liked', 0);
        $("#like-button button").text('LIKE');

        $.ajax({
            url: 'shows.php',
            type: 'POST',
            data: { "ajax": true, "action": "unlike_show", "id": <?php echo $showId ?> },
            dataType: 'json'
        }).done(function(data) {

        })
    }

    function followShow() {
        $("#follow-button").data('following', 1);
        $("#follow-button button").text('UNFOLLOW');

        $.ajax({
            url: 'shows.php',
            type: 'POST',
            data: { "ajax": true, "action": "follow_show", "id": <?php echo $showId ?> },
            dataType: 'json'
        }).done(function(data) {

        })
    }

    function unfollowShow() {
        $("#follow-button").data('following', 0);
        $("#follow-button button").text('FOLLOW');

        $.ajax({
            url: 'shows.php',
            type: 'POST',
            data: { "ajax": true, "action": "unfollow_show", "id": <?php echo $showId ?> },
            dataType: 'json'
        }).done(function(data) {

        })
    }

</script>

</body>
</html>