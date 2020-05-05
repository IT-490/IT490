<?php

//require_once('functions.php');

// The requested show id. Default is 1
session_start();
$id =$_REQUEST['id'];
$response = sendRabbit(array('type'=> 'getShow', 'data'=> $id));

if(isset($_SESSION['user'])){
<<<<<<< Updated upstream
	$user =$_SESSION['user'];
	$liked = sendRabbit(array('type'=> 'isLiked', 'data'=> array('user'=> $user, 'showID'=> $id)));
	$followed = sendRabbit(array('type'=> 'isFollowing', 'data'=> array('user'=> $user, 'showID'=> $id)));
=======
    $user =$_SESSION['user'];
    $liked = sendRabbit(array('type'=> 'isLiked', 'data'=> array('user'=> $user, 'showID'=> $id)));
    $followed = sendRabbit(array('type'=> 'isFollowing', 'data'=> array('user'=> $user, 'showID'=> $id)));
>>>>>>> Stashed changes
}
// If the request was sent via AJAX
if(isset($_REQUEST['ajax'])) {
<<<<<<< Updated upstream
	$id =$_REQUEST['id'];
	session_start();
	if(isset($_SESSION['user'])){
		$user =$_SESSION['user'];
	}else{
		return 1;
	}
	switch($_REQUEST['action']){
		case "like_show":
			echo sendRabbit(array('type'=> 'likeShow', 'data'=> array('user'=> $user, 'showID'=> $id)));
			break;
		case "unlike_show":
			echo sendRabbit(array('type'=> 'unlikeShow', 'data'=> array('user'=> $user, 'showID'=> $id)));
			break;
		case "follow_show":
			echo sendRabbit(array('type'=> 'followShow', 'data'=> array('user'=> $user, 'showID'=> $id)));
			break;
		case "unfollow_show":
			echo sendRabbit(array('type'=> 'unfollowShow', 'data'=> array('user'=> $user, 'showID'=> $id)));
			break;
	}
	exit();
=======
    $id =$_REQUEST['id'];
    session_start();
    if(isset($_SESSION['user'])){
        $user =$_SESSION['user'];
    }else{
        return 1;
    }
    switch($_REQUEST['action']){
        case "like_show":
            echo sendRabbit(array('type'=> 'likeShow', 'data'=> array('user'=> $user, 'showID'=> $id)));
            break;
        case "unlike_show":
            echo sendRabbit(array('type'=> 'unlikeShow', 'data'=> array('user'=> $user, 'showID'=> $id)));
            break;
        case "follow_show":
            echo sendRabbit(array('type'=> 'followShow', 'data'=> array('user'=> $user, 'showID'=> $id)));
            break;
        case "unfollow_show":
            echo sendRabbit(array('type'=> 'unfollowShow', 'data'=> array('user'=> $user, 'showID'=> $id)));
            break;
    }
    exit();
>>>>>>> Stashed changes
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
    <a id="welcome-message" class="ml-auto navbar-text mr-3"></a>
</nav>


<div class="container mt-4 mb-4">
    <h2 class="text-center">Show Details</h2>
    <hr>

    <div class="row">
        <div class="col-sm-4">
            <img id="show-poster-graphic" src="<?php echo $response['show']['poster']; ?>" alt="Show's Poster Graphic" class="img-fluid">
        </div>
        <div class="col-sm-8">
            <h4>Show: <span id="show-name" class="text-muted"><?php echo $response['show']['name']; ?></span></h4>
            <h4>Network: <span id="show-genre" class="text-muted"><?php echo $response['show']['network']; ?></span></h4>
            <h4>Upcoming Episodes: </h4>
            <table id="upcoming-episodes" class="table table-bordered table-striped table-hover">
                <thead>
                <tr>
                    <th>Name</th>
                    <th>Date</th>
                    <th>Time</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach($response['episodes'] as $upcomingEpisode) { ?>
                    <tr>
                        <td><?php echo $upcomingEpisode['name']; ?></td>
                        <td><?php echo date('Y-m-d',strtotime($upcomingEpisode['airdate'])); ?></td>
                        <td><?php echo date('g:i A', strtotime($upcomingEpisode['airdate'])); ?></td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>

            <div class="mt-4">
                <?php
                if(isset($_SESSION['user'])){
                    if($liked === true){
                        echo "<button class='btn btn-primary' onclick=unlikeShow()>Unlike</button> ";
                    }else if($liked === false){
                        echo "<button class='btn btn-primary' onclick=likeShow()>Like</button> ";
                    }

                    if($followed === true){
                        echo "<button class='btn btn-primary' onclick=unfollowShow()>Unfollow</button> ";
                    }else if($followed === false){
                        echo "<button class='btn btn-primary' onclick=followShow()>Follow</button> ";
                    }

                    echo "<a href='reviews.php?id=" . $id . "'><button class='btn btn-primary'>Review</button></a> ";
                }
                ?>

                <a id="forums-button" href='<?php echo "forums/threads.php?id=".$id;?>'><button class="btn btn-primary">Forums</button></a>
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
                    $("#welcome-message").attr("href", "./profile.php");
                    $(".no-session").hide();
                    $(".has-session").show();
                } else { // No session. Switch to the signed-out version
                    $("#welcome-message").text("");
                    $("#welcome-message").attr("href", "");
                    $(".has-session").hide();
                    $(".no-session").show();
                }
            })

    });


    function likeShow() {
        $.ajax({
            url: 'shows.php',
            type: 'POST',
            data: { "ajax": true, "action": "like_show", "id": <?php echo $id ?> },
            dataType: 'json'
        }).done(function(data) {
            location.reload();
        });
    }

    function unlikeShow() {
        $.ajax({
            url: 'shows.php',
            type: 'POST',
            data: { "ajax": true, "action": "unlike_show", "id": <?php echo $id ?> },
            dataType: 'json'
        }).done(function(data) {
            location.reload();
        })
    }

    function followShow() {
        $.ajax({
            url: 'shows.php',
            type: 'POST',
            data: { "ajax": true, "action": "follow_show", "id": <?php echo $id ?> },
            dataType: 'json'
        }).done(function(data) {
            location.reload();
        });
    }

    function unfollowShow() {
        $.ajax({
            url: 'shows.php',
            type: 'POST',
            data: { "ajax": true, "action": "unfollow_show", "id": <?php echo $id ?> },
            dataType: 'json'
        }).done(function(data) {
            location.reload();
        });
    }

</script>

</body>
</html>
