<?php
    session_start();
    if (empty($_SESSION)) {
        header('location: index.php');
    }

    require_once('scripts/php/classes.php');
    $userid = $_SESSION['id'];
    $profile = User::fetchProfileInfo();

    //var_dump($profile);
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

                    <!-- BEGIN STATUS UPDATE DIV -->
                    <div id="createPost" class="panel right">
                        <h1>Create Post</h1>
                        <?php
                        if (isset($_POST['btn-Post'])) {
                            Post::createPost();
                        }
                        ?>
                        <form method="POST">    
                            <textarea name="postText" class="postText" placeholder="What's happening?"></textarea>
                            <input type="submit" value="Post!" name="btn-Post">
                            <p style="float: right; padding: 0;"><span class="charcount"></span> characters remaining</p>
                        </form>
                        
                    </div> 


                    <div id="profile" class="panel left">
                        <h1>My Profile</h1>
                        <div class="postWrapper">
                            <a href="user.php?userid=<?= $profile['id'];?>"><img class="avatar" src="<?= $profile['avatar'];?>"></a>
                            <span class="name"><a href="user.php?userid=<?= $profile['id']; ?>"><?= $profile['username'];?></a></span><span class="time"><a href="viewpost.php?userid=<?=$profile['id'];?>&postid=<?=$profile['postid'];?>"><?php if (isset($profile['time'])){echo $profile['time'];}?></a></span>
                            <p>
                                <?php if (isset($profile['latestPost'])){echo $profile['latestPost'];} else {echo 'You haven\'t posted anything yet!';}?><br />
                                <?php if (isset($profile['numOfPosts'])){echo $profile['numOfPosts'];} else {echo '0';}?> posts<span class="spacing"><?php if (isset($profile['numOfFollowers'])) {echo $profile['numOfFollowers'];} else {echo '0';}?> followers</span><span class="spacing"><?php if (isset($profile['numFollowing'])) {echo $profile['numFollowing'];} else {echo '0';}?> following</span>
                            </p>
                        </div>
                    </div>

                    <!-- BEGIN FEED DIV -->
                    <div class="panel left">
                        <!-- BEGIN FEED TABS -->
                        <div class="tabs">
                            <ul>
                                <li><a href="#timeline">Timeline</a></li>
                                <li><a href="#only-my-posts">Personal Feed</a></li>
                                <li><a href="#global-feed">Global Feed</a></li>
                            </ul>

                            <!-- BEGIN USER TIMELINE -->
                            <div id="timeline">
                                <h1>Timeline</h1>
                                <?php 
                                $timeline = Post::fetchTimeline();
                                if ($timeline != NULL) {
                                    $following = User::fetchFollowing();
                                    foreach ($timeline as $time) {
                                        echo '<div class="postWrapper post_' . $time->postid . '">
                                        <a href="user.php?userid=' . $time->userid . '"><img class="avatar" src="' . $time->avatar . '"></a>
                                        <span class="name"><a href="user.php?userid=' . $time->userid . '">' . $time->username . '</a></span>
                                        <span class="time"><a href="viewpost.php?userid=' . $time->userid . '&postid=' . $time->postid . '">' . $time->timestamp . '</a></span>';
                                        
                                        echo '<p>' . $time->body . '</p>';
                                        // if post belongs to the user logged in, add a delete link
                                        if ($_SESSION['id'] == $time->userid) {
                                            echo '<div class="deleteLink" style="clear: right; text-align: right;"><input type="submit" value="delete" class="btnDelete" id="' . $time->postid .'"></div>';
                                        }
                                        if (in_array($time->userid, $following)) {
                                            echo '<div class="unfollow' . $time->userid . '" style="clear: right; text-align: right;"><input type="submit" value="Unfollow" class="btnUnfollow" id="' . $time->userid . '"></div><div class="follow' . $time->userid . '" style="clear: right; text-align: right; display: none;"><input type="submit" value="Follow" class="btnFollow" id="' . $time->userid . '"></div>';
                                        } 
                                        else {
                                            if ($time->userid != $_SESSION['id']) {
                                                echo '<div class="follow' . $time->userid . '" style="clear: right; text-align: right;"><input type="submit" value="Follow" class="btnFollow" id="' . $time->userid . '"></div><div class="unfollow' . $time->userid . '" style="clear: right; text-align: right; display: none;"><input type="submit" value="Unfollow" class="btnUnfollow" id="' . $time->userid . '"></div>';
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
                            <!-- END USER TIMELINE -->

                            <!-- BEGIN PERSONAL FEED -->
                            <div id="only-my-posts">
                                <h1>My Posts</h1>
                                <?php
                                $myposts = Post::myPosts();
                                if ($myposts != NULL) {
                                    foreach ($myposts as $mypost) {
                                        echo '<div class="postWrapper post_' . $mypost->postid . '">
                                        <a href="user.php?userid=' . $mypost->userid . '"><img class="avatar" src="' . $mypost->avatar . '"></a>
                                        <span class="name"><a href="user.php?userid=' . $mypost->userid . '">' . $mypost->username . '</a></span><span class="time"><a href="viewpost.php?userid=' . $mypost->userid . '&postid=' . $mypost->postid . '">' . $mypost->timestamp . '</a></span>';

                                        echo '<p>' . $mypost->body . '</p>';
                                        // if post belongs to the user logged in, add a delete link
                                        if ($_SESSION['id'] == $mypost->userid) {
                                            echo '<div class="deleteLink" style="clear: both; text-align: right;"><input type="submit" value="delete" class="btnDelete" id="' . $mypost->postid .'"></div>';
                                        }
                                        echo '</div>';
                                    }
                                }
                                else {
                                    echo '<center>There are no posts here!</center>';
                                }
                                ?>
                            </div>
                            <!-- END PERSONAL FEED -->


                            <!-- BEGIN GLOBAL FEED -->
                            <div id="global-feed">
                                <h1>Global Feed</h1>
                                <?php 
                                $posts = Post::allPosts();
                                if ($posts != NULL) {
                                    $following = User::fetchFollowing();
                                    foreach ($posts as $post) {
                                        echo '<div class="postWrapper post_' . $post->postid . '">
                                        <a href="user.php?userid=' . $post->userid . '"><img class="avatar" src="' . $post->avatar . '"></a>
                                        <span class="name"><a href="user.php?userid=' . $post->userid . '">' . $post->username . '</a></span>
                                        <span class="time"><a href="viewpost.php?userid=' . $post->userid . '&postid=' . $post->postid . '">' . $post->timestamp . '</a></span>';
                                        
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
                            <!-- END GLOBAL FEED DIV -->
                        </div>
                        <!-- END FEED TABS -->
                    </div>
                    <!-- END FEED DIV-->



                    <!-- BEGIN FOLLOWING DIV -->
                    <div class="panel right">
                        <!-- BEGIN FOLLOW TABS -->
                        <div class="tabs">
                            <ul>
                                
                                <li><a href="#followingList">People I Follow</a></li>
                                <li><a href="#followerList">My Followers</a></li>
                            </ul>

                            <div id="followingList">
                                <h1>People I Follow</h1>
                                <center>
                                <?php
                                $following = User::fetchPeopleIFollow();
                                if (empty($following)) {
                                    echo "You're not following anybody yet!";
                                }
                                else {
                                    foreach ($following as $follow) {
                                        echo '<div class="fd followdiv' . $follow['followid'] . '"> 
                                        <a href="user.php?userid=' . $follow['followid'] . '"><img src="' . $follow['avatar'] . '" class="avatar"></a>
                                        <span class="name"><a href="user.php?userid=' . $follow['followid'] . '">' . $follow['username'] . '</a></span>
                                        <div class="unfollow' . $follow['followid'] . '" style="clear: right; text-align: right;"><input type="submit" value="Unfollow" class="btnUnfollow" id="' . $follow['followid'] . '"></div>
                                        </div>'; 
                                    }
                                }
                                ?>
                                </center>
                            </div>


                            <div id="followerList">
                                <h1>My Followers</h1>
                                <center>
                                <?php
                                $following = User::fetchFollowing();
                                $followers = User::fetchFollowers();
                                if (empty($followers)) {
                                    echo "Nobody's following you yet.";
                                }
                                else {
                                    foreach ($followers as $follower) {
                                        echo '<div class="fd">
                                        <a href="user.php?userid=' . $follower['followerid'] . '"><img src="' . $follower['avatar'] . '" class="avatar"></a>
                                        <span class="name"><a href="user.php?userid=' . $follower['followerid'] . '">' . $follower['username'] . '</a></span>';
                                        
                                        if (in_array($follower['followerid'], $following)) {
                                            echo '<div class="unfollow' . $follower['followerid'] . '" style="clear: right; text-align: right;"><input type="submit" value="Unfollow" class="btnUnfollow" id="' . $follower['followerid'] . '"></div><div class="follow' . $follower['followerid'] . '" style="clear: right; text-align: right; display: none;"><input type="submit" value="Follow" class="btnFollow" id="' . $follower['followerid'] . '"></div>';
                                        } 
                                        else {
                                            if ($follower['followerid'] != $_SESSION['id']) {
                                                echo '<div class="follow' . $follower['followerid'] . '" style="clear: right; text-align: right;"><input type="submit" value="Follow" class="btnFollow" id="' . $follower['followerid'] . '"></div><div class="unfollow' . $follower['followerid'] . '" style="clear: right; text-align: right; display: none;"><input type="submit" value="Unfollow" class="btnUnfollow" id="' . $follower['followerid'] . '"></div>';
                                            }
                                        } 
                                        echo '</div>';
                                    }
                                }
                                ?>
                                </center>
                            </div>

                        </div>
                        <!-- END FOLLOW TABS -->
                    </div>
                    <!-- END FOLLOWING DIV -->

                 </div>
             </div>
             <?php include_once('headers/footer.php'); ?>
            <script src="scripts/jquery/jquery.js"></script>
            <script src="scripts/jquery/ui/jquery-ui.js"></script>
            <script src="scripts/js/click.js"></script>
    <script>
    $(document).ready(function() {
        $('.tabs').tabs();
        charCountdown();
        $(".postText").change(charCountdown);
        $(".postText").keyup(charCountdown);
    });

    function charCountdown() {
            var remaining = 140 - $(".postText").val().length;
            $(".charcount").text(remaining);
            if (remaining < 0) {
                $('.charcount').css('color', 'red');
            }
            else {
                $('.charcount').removeAttr('style');
            }
        }
    </script>
    </body>
</html>