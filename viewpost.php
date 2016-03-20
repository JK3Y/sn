<?php
session_start();
  if (!isset($_GET['postid']) || !isset($_GET['userid'])) {
    header('location: error.php');
  }
  else {
    $postid = $_GET['postid'];
    $userid = $_GET['userid'];
  }

  if (isset($_GET['delete'])) {
      $delete = 'true';
  }
  else {
      $delete = 'false';
  }
  require_once('scripts/php/classes.php');
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
                <center>
                <div class="panel middle">
                    <?php 
                    $post = Post::getPost($postid, $delete); 
                    $following = User::fetchFollowing();
                    echo '<div class="postWrapper post_' . $post->postid . '">
                          <a href="user.php?userid=' . $post->userid . '"><img class="avatar" src="' . $post->avatar . '"></a>
                          <span class="name"><a href="user.php?userid=' . $post->userid . '">' . $post->username . '</a></span><span class="time"><a href="viewpost.php?userid=' . $post->userid . '&postid=' . $post->postid . '">' . $post->time . '</a></span>';
                        
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
                    ?>    
                </div>
                </center>
            </div>
         </div>
         <?php include_once('headers/footer.php'); ?>
        <script src="scripts/jquery/jquery.js"></script>
        <script src="scripts/jquery/ui/jquery-ui.js"></script>
        <script src="scripts/js/click.js"></script>
    </body>
</html>