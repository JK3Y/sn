<?php
session_start();
require_once('scripts/php/classes.php');
$profile = User::fetchProfileInfo();
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>SN!</title>

        <link rel="stylesheet" href="scripts/style.css" type="text/css" />
        <link rel="stylesheet" href="scripts/jquery/ui/jquery-ui.css">
    </head>
    <body>
            <?php include('headers/header.php'); ?>

            <div id="content">
            	<div class="wrapper">
                    <!-- Confirmation dialog for post deletion -->
                    <div id="dialog" title="Confirm Deletion">
                        <p><span class="ui-icon-alert" style="float: left; margin: 0 7px 20px 0;"></span>This post will be deleted. This cannot be undone. Are you sure?</p>
                    </div>

	            	<div id="posts" class="panel left">
	                    <h1><?= $profile['username']; ?>'s Profile</h1>
	                    <div class="postWrapper">
	                        <a href="user.php?userid=<?= $profile['id']; ?>"><img class="avatar" src="<?= $profile['avatar']; ?>"></a>
	                        <span class="name"><a href="user.php?userid=<?= $profile['id']; ?>"><?= $profile['username'];?></a></span><span class="time"><?php if (isset($profile['time'])){echo $profile['time'];}?></span>
	                        <p>
	                            <?php if (isset($profile['latestPost'])){echo $profile['latestPost'];} else {echo 'They haven\'t posted anything yet!';}?><br />
                                <?php if (isset($profile['numOfPosts'])){echo $profile['numOfPosts'];} else {echo '0';}?> posts<span class="spacing"><?php if (isset($profile['numOfFollowers'])) {echo $profile['numOfFollowers'];} else {echo '0';}?> followers</span><span class="spacing"><?php if (isset($profile['numFollowing'])) {echo $profile['numFollowing'];} else {echo '0';}?> following</span>
	                        </p>
	                    </div>
	                </div>

                    <div class = "panel right">
                        <h1>Followers</h1>
                        <center>
                        <?php
                        $followers = User::fetchFollowers();
                        $following = User::fetchFollowing();
                        if (empty($followers)) {
                            echo "Nobody's following " . $profile['username'] . " yet.";
                        }
                        else {
                            foreach ($followers as $follower) {
                                echo '<div class="fd">
                                <a href="user.php?userid=' . $follower['followerid'] . '"><img src="' . $follower['avatar'] . '" class="avatar"></a>
                                <span class="name"><a href="user.php?userid=' . $follower['followerid'] . '">' . $follower['username'] . '</a></span>';
                                
                                if (in_array($follower['followerid'], $following)) {
                                    if ($follower['followerid'] != $_SESSION['id']) {
                                        echo '<div class="unfollow' . $follower['followerid'] . '" style="clear: right; text-align: right;"><input type="submit" value="Unfollow" class="btnUnfollow" id="' . $follower['followerid'] . '"></div><div class="follow' . $follower['followerid'] . '" style="clear: both; text-align: right; display: none;"><input type="submit" value="Follow" class="btnFollow" id="' . $follower['followerid'] . '"></div>';
                                    }
                                } 
                                else {
                                    if ($follower['followerid'] != $_SESSION['id']) {
                                        echo '<div class="follow' . $follower['followerid'] . '" style="clear: right; text-align: right;"><input type="submit" value="Follow" class="btnFollow" id="' . $follower['followerid'] . '"></div><div class="unfollow' . $follower['followerid'] . '" style="clear: both; text-align: right; display: none;"><input type="submit" value="Unfollow" class="btnUnfollow" id="' . $follower['followerid'] . '"></div>';
                                    }
                                } 
                                echo '</div>';
                            }
                        }
                        ?>
                        </center>
                    </div>

	                <div class="panel left">
                        <h1><?= $profile['username'];?>'s Posts</h1>
                        <?php 
                        $posts = Post::myPosts();
                        if ($posts != NULL) {
                            $userid = $_SESSION['id'];
                            $following = User::fetchFollowing();
                            foreach ($posts as $post) {
                                echo '<div class="postWrapper post_' . $post->postid . '">
                                <a href="user.php?userid=' . $post->userid . '"><img class="avatar" src="' . $post->avatar . '"></a>
                                <span class="name"><a href="user.php?userid=' . $post->userid . '">' . $post->username . '</a></span><span class="time"><a href="viewpost.php?userid=' . $post->userid . '&postid=' . $post->postid . '">' . $post->timestamp . '</a></span>';
                                echo '<p>' . $post->body . '</p>';
                                // if post belongs to the user logged in, add a delete link
                                if ($_SESSION['id'] == $post->userid) {
                                    echo '<div class="deleteLink" style="clear: both; text-align: right;"><input type="submit" value="delete" class="btnDelete" id="' . $post->postid .'"></div>';
                                }
                                if (in_array($post->userid, $following)) {
                                    echo '<div class="unfollow' . $post->userid . '" style="clear: both; text-align: right;"><input type="submit" value="Unfollow" class="btnUnfollow" id="' . $post->userid . '"></div><div class="follow' . $post->userid . '" style="clear: both; text-align: right; display: none;"><input type="submit" value="Follow" class="btnFollow" id="' . $post->userid . '"></div>';
                                } 
                                else {
                                    if ($post->userid != $_SESSION['id']) {
                                        echo '<div class="follow' . $post->userid . '" style="clear: both; text-align: right;"><input type="submit" value="Follow" class="btnFollow" id="' . $post->userid . '"></div><div class="unfollow' . $post->userid . '" style="clear: both; text-align: right; display: none;"><input type="submit" value="Unfollow" class="btnUnfollow" id="' . $post->userid . '"></div>';
                                    }
                                }
                                echo '</div>';
                            }
                        }
                        else {
                            echo '<center>There are no posts here!</center>';
                        }
                        ?>
                    </div>
	            </div>
            </div>
            <script src="scripts/jquery/jquery.js"></script>
            <script src="scripts/jquery/ui/jquery-ui.js"></script>
            <script src="scripts/js/click.js"></script>
            <?php include_once('headers/footer.php'); ?>
    </body>