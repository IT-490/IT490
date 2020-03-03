<?php

require_once('function.php');

// The requested show id. Default is 0
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
    <title>Home</title>
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
            <h4>Upcoming episodes:</h4>
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
                <a id="like-button" style="display: none;" href="#" class="has-session"><button class="btn btn-primary">LIKE</button></a>
                <a id="follow-button" style="display: none;" href="#" class="has-session"><button class="btn btn-primary">FOLLOW</button></a>
                <a id="forums-button" href="#"><button class="btn btn-primary">GO TO FORUMS</button></a>
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

        // User clicked on the "Like" button.
        $("#like-button").on("click", function(e) {
            $.ajax({
                url: 'shows.php', // The php script that clears the session
                type: 'POST',
                data: { "ajax": true, "action": "like_show", "id": <?php echo $showId ?> },
                dataType: 'json'
            })
                .done(function(data) {
                    if(data.success) alert("Show liked!")
                })
        });


        // User clicked on the "Follow" button.
        $("#follow-button").on("click", function(e) {
            $.ajax({
                url: 'shows.php', // The php script that clears the session
                type: 'POST',
                data: { "ajax": true, "action": "follow_show", "id": <?php echo $showId ?> },
                dataType: 'json'
            })
                .done(function(data) {
                    if(data.success) alert("Show followed!")
                })
        });

    });

</script>

</body>
</html>