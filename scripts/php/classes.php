<?php
/**************************************
	CONNECTION
**************************************/
class Db {
	private static $instance = NULL;
    private function __construct() {}
    private function __clone() {}
    
    public static function getInstance() {
        if (!isset(self::$instance)) {
            $pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
            self::$instance = new PDO('mysql:host=localhost; dbname=sn', 'connect', 'D$SPT{4dR)ORsT^', $pdo_options);
            //self::$instance = new PDO('mysql:host=localhost; dbname=testdb', 'jacob', 'root', $pdo_options);
        }
        return self::$instance;
    }
}



/**************************************
	USER CONTROLLER
**************************************/
class User {
	/**************************************
		LOGOUT FUNCTION
	**************************************/
	public static function logoutUser() {
		if (!isset($_SESSION['id'])) {
			header('location: index.php');
			session_destroy();
			exit();
		}
		else {
			// destroy the variables
			$_SESSION = array();
			// destroy the session
			session_destroy();
			// destroy the cookie
			setcookie('PHPSESSID', '', time()-3600, '/', '', 0, 0);
			// redirect
			header("location: index.php");
			exit();
		}
	}

	/**************************************
		GET PROFILE INFORMATION 
		RETURNS: USERNAME, USERID, AVATAR, POST COUNT, FOLLOWING COUNT, FOLLOWER COUNT, LATEST POST, TIMESTAMP
	**************************************/
	public static function fetchProfileInfo() {
		if (!isset($_GET['userid'])) {
			$userid = $_SESSION['id'];
		}
		else {
			$userid = $_GET['userid'];
		}
		return UserModel::getProfileInfo($userid);
	}

	/**************************************
		GET LIST OF USERS BEING FOLLOWED BY CURRENT USER
		RETURNS AN ARRAY LIST
	**************************************/
	public static function fetchFollowing() {
		$userid = $_SESSION['id'];
		return UserModel::getFollowingList($userid);
	}

	/**************************************
		GET USERS BEING FOLLOWED BY A SPECIFIC USER
	**************************************/
	public static function fetchFollowers() {
		if (isset($_GET['userid'])) {
			$userid = $_GET['userid'];
		}
		else {
			$userid = $_SESSION['id'];
		}
		return UserModel::getFollowers($userid);
	}

	/**************************************
		GET USERS BEING FOLLOWED BY CURRENT USER
	**************************************/
	public static function fetchPeopleIFollow() {
		$userid = $_SESSION['id'];
		return UserModel::getPeopleIFollow($userid);
	}
}




/**************************************
	POST CONTROLLER
**************************************/
class Post {
	/**************************************
		CREATE A NEW POST
	**************************************/
	public static function createPost() {
		$userid = $_SESSION['id'];
		$body = htmlspecialchars(substr(trim($_POST['postText']), 0, 140));
		PostModel::makePost($userid, $body);
		header('location: myhome.php');
	}

	/**************************************
		GET ALL POSTS (GLOBAL)
	**************************************/
	public static function allPosts() {
		$listofposts = PostModel::getAllPosts();
		return $listofposts;
	}

	/**************************************
		GET JUST THE POSTS OF THE CURRENT USER (PERSONAL FEED)
	**************************************/
	public static function myPosts() {
		if (isset($_GET['userid'])) {
			$userid = $_GET['userid'];
		}
		else {
			$userid = $_SESSION['id'];
		}
		$listofposts = PostModel::getMyPosts($userid);
		return $listofposts;
	}

	/**************************************
		GET A SINGLE POST (FOR VIEWPOST.PHP)
	**************************************/
    public static function getPost($postid) {
    	return PostModel::getSinglePost($postid);
    }

	/**************************************
		GET POSTS FROM CURRENT USER AS WELL AS THE POSTS FROM PEOPLE THEY FOLLOW
	**************************************/
    public static function fetchTimeline() {
    	if (isset($_GET['userid'])) {
			$userid = $_GET['userid'];
		}
		else {
			$userid = $_SESSION['id'];
		}
		$timeline = PostModel::getTimeline($userid);
		return $timeline;
    }
}




// THE MODELS DO ALL THE DATABASE STUFF
/**************************************
	USER MODEL
**************************************/
class UserModel {
	/**************************************
		UPDATE ACCOUNT INFORMATION (EMAIL, PASSWORD)
	**************************************/
	public static function updateAccount($updates) {
		$userid = $updates['userid'];
		$email = NULL;
		$password = NULL;
		$currentpassword = $updates['currentpassword'];

		// If the $updates array contains the Email key
		if (isset($updates['email'])) {
			$emailValid = self::checkIfEmailExists($updates['email']);
			// If email does NOT exist (is unique)
			if (empty($emailValid)) {
				$email = $updates['email'];
			}
		}

		// If $updates array contains a NEW password
		if (isset($updates['newpassword'])) {
			$password = SHA1($updates['newpassword']);
		}

		try {
			$db = Db::getInstance();

			$sql = $db->prepare('UPDATE user
								SET email 	 = IF (:email 	 IS NULL, email, 	:email)
								,	password = IF (:password IS NULL, password, :password)
								WHERE id= :id
								LIMIT 1');
			$sql->execute(array('email' => $email, 'password' => $password, 'id' => $userid));
			return TRUE;
		}
		catch (Exception $e) {
			return FALSE;
		}
	}

	/**************************************
		CREATE A NEW USER
	**************************************/
	public static function createNewUser($username, $password, $email) {
		$db = Db::getInstance();
		try {
			$sql = $db->prepare("INSERT INTO user VALUES('', :username, :password, :email, '', NOW(), 'src/profiles/default.png')");
			$sql->execute(array('username' => $username, 'password' => SHA1($password), 'email' => $email));
			return TRUE;
		}
		catch (Exception $e) {
			return FALSE;
		}
	}

	/**************************************
		CHECK LOGIN CREDENTIALS
	**************************************/
	public static function checkLoginCredentials($username, $password) {
		$db = Db::getInstance();
		$sql = $db->prepare("SELECT id, username, password, user_level, avatar 
							FROM user 
							WHERE username= :username AND password= :password");
		$sql->execute(array('username' => $username, 'password' => SHA1($password)));
		// IF SQL STATEMENT WAS SUCCESSFUL
		// (return a 1 for true, 0 for false)
		if ($sql->rowCount()) {
			// dumps variables into SESSION
			$_SESSION = $sql->fetch(PDO::FETCH_ASSOC);
			$_SESSION['user_level'] = (int) $_SESSION['user_level'];
			return TRUE;
		}
		else {
			return FALSE;
		}
	}

	/**************************************
		GET PROFILE INFORMATION OF A USER
	**************************************/
	public static function getProfileInfo($userid) {
		$db = Db::getInstance();
        // TWO SUB QUERIES TO PULL NUMBER OF POSTS AND USERNAME, AND LATEST POST ALONG WITH THE PROPER DATE FORMAT.
        $sql = $db->prepare("SELECT username, user.id, avatar, (SELECT COUNT(postid) 
		                                                        FROM posts 
		                                                        WHERE userid = :id) AS numOfPosts, 
		                                                       (SELECT COUNT(following_id)
		                                                        FROM follow
		                                                        WHERE user_id= :id) AS numFollowing,
		                                                       (SELECT COUNT(user_id)
		                                                        FROM follow
		                                                        WHERE following_id= :id) AS numOfFollowers,
		                                                        body AS 'latestPost', postid, DATE_FORMAT(time_stamp, '%b %d %Y %h:%i %p') as 'time' 

                            FROM posts 
                          	JOIN user on posts.userid = user.id 
                            WHERE user.id = :id AND time_stamp = (SELECT MAX(time_stamp) FROM posts WHERE userid= :id) 
                            GROUP BY userid");
        $sql->execute(array('id' => $userid));
        $result = $sql->fetch(PDO::FETCH_ASSOC);
        // if array turns up empty (new user)
        if (empty($result)) {
            $sql = $db->prepare("SELECT username, user.id, avatar
                                FROM user
                                WHERE user.id= :id");
            $sql->execute(array('id' => $userid));
            return $sql->fetch(PDO::FETCH_ASSOC);
        }
        // if array comes back with info, return profile info
        else {
            return $result;
        }
	}

	/**************************************
		RETURN ALL USERS THAT SOMEBODY IS FOLLOWING AS AN ARRAY 
		(USED TO CHECK IF THE USER IS BEING FOLLOWED)
		RETURN AN ARRAY LIST
	**************************************/
	public static function getFollowingList($userid) {
		$db = Db::getInstance();
		$sql = $db->prepare("SELECT following_id as 'followid', username, avatar
							 FROM follow
							 JOIN user ON following_id = user.id
							 WHERE user_id= :userid
							 ORDER BY follow_id DESC");
		$sql->execute(array('userid' => $userid));
		foreach ($sql->fetchAll() as $follow) {
			$result[] = $follow['followid'];
			$result[] = $follow['username'];
			$result[] = $follow['avatar'];
		}
		return $result;
	}

	/**************************************
		RETURN ALL USERS THAT CURRENT USER IS FOLLOWING
	**************************************/
	public static function getPeopleIFollow($userid) {
		$db = Db::getInstance();
		$sql = $db->prepare("SELECT following_id as 'followid', username, avatar
							FROM follow
							JOIN user ON following_id = user.id
							WHERE user_id= :userid
							ORDER BY follow_id DESC");
		$sql->execute(array('userid' => $userid));
		$result = $sql->fetchAll(PDO::FETCH_ASSOC);
		return $result;
	}

	/**************************************
		RETURN ALL USERS THAT A USER IS FOLLOWING
	**************************************/
	public static function getFollowers($userid) {
		$db = Db::getInstance();
		$sql = $db->prepare("SELECT user_id as 'followerid', username, avatar
							FROM follow 
							JOIN user ON user.id = user_id
							WHERE following_id= :userid");
		$sql->execute(array('userid' => $userid));
		$result = $sql->fetchAll(PDO::FETCH_ASSOC);
		return $result;
	}

	/**************************************
		CHECK IF EMAIL IS ALREADY IN THE DATABASE
	**************************************/
	public static function checkIfEmailExists($email) {
		$db = Db::getInstance();
		$sql = $db->prepare('SELECT * 
							FROM user 
							WHERE email= :email');
	    $sql->execute(array('email' => $email));
	    $result = $sql->fetchAll();
	    return $result;
	}

	/**************************************
		CHECK IF USERNAME IS ALREADY IN THE DATABASE
	**************************************/
	public static function checkIfUsernameExists($username) {
		$db = Db::getInstance(); 
		$sql = $db->prepare('SELECT * 
							FROM user 
							WHERE username= :username');
	    $sql->execute(array('username' => $username));
	    $result = $sql->fetchAll();
	    return $result;
	}

	/**************************************
		CHECK IS THE PASSWORD ENTERED MATCHES THE ONE IN THE DATABASE
	**************************************/
	public static function checkCurrentPassword($user, $password) {
		$db = Db::getInstance();
		$sql = $db->prepare('SELECT *
							FROM user
							WHERE id= :user AND password= :password');
		$sql->execute(array('user' => $user, 'password' => SHA1($password)));
		$result = $sql->fetchAll();
		return $result;
	}

	/**************************************
		FOLLOW A USER
	**************************************/
	public static function followUser($userid, $followingid) {
		$db = Db::getInstance();
		$sql = $db->prepare("INSERT INTO follow VALUES('', :userid, :followingid)");
		$sql->execute(array('userid' => $userid, 'followingid' => $followingid));
		if ($sql->rowCount()) {
			return TRUE;
		}
		else {
			return FALSE;
		}
	}

	/**************************************
		UNFOLLOW A USER
	**************************************/
	public static function unfollowUser($userid, $followingid) {
		$db = Db::getInstance();
		$sql = $db->prepare("DELETE FROM follow WHERE user_id= :userid AND following_id= :followingid");
		$sql->execute(array('userid' => $userid, 'followingid' => $followingid));
		if ($sql->rowCount()) {
			return TRUE;
		}
		else {
			return FALSE;
		}
	}

}



/**************************************
	POST MODEL
**************************************/
class PostModel {
	public $postid;
	public $userid;
	public $username;
	public $body;
	public $timestamp;
	public $avatar;

	public function __construct($postid, $username, $userid, $body, $timestamp, $avatar) {
		$this->postid 	 = $postid;
		$this->username  = $username;
		$this->userid 	 = $userid;
		$this->body 	 = $body;
		$this->timestamp = $timestamp;
		$this->avatar	 = $avatar;
	}

	/**************************************
		MAKE A POST - RETURNS NOTHING
	**************************************/
	public static function makePost($userid, $body) {
		try {
		$db = Db::getInstance();
		$sql = $db->prepare("INSERT INTO posts VALUES('', :userid, :body, NOW())");
		$sql->execute(array('userid' => $userid, 'body' => $body));
		}
		catch (Exception $e) {
			echo 'Caught Exception: ' . $e->getMessage();
		}
	}

	/**************************************
		FETCH ALL POSTS (GLOBAL)
	**************************************/
	public static function getAllPosts() {
		$db = Db::getInstance();
		$sql = $db->prepare("SELECT postid, username, avatar, userid, body, DATE_FORMAT(time_stamp, '%b %d %Y %h:%i %p') AS 'time' 
							FROM posts JOIN user ON user.id=posts.userid 
							ORDER BY time_stamp DESC");
		$sql->execute();
		foreach ($sql->fetchAll() as $post) {
			$list[] = new PostModel($post['postid'], $post['username'], $post['userid'], $post['body'], $post['time'], $post['avatar']);
		}
		return $list;
	}

	/**************************************
		FETCH THE POSTS OF THE CURRENT USER (PERSONAL FEED)
	**************************************/
	public static function getTimeline($userid) {
		$db = Db::getInstance();
		$sql = $db->prepare("SELECT postid, body, DATE_FORMAT(time_stamp, '%b %d %Y %h:%i %p') AS 'time', username, user.avatar AS 'avatar', id AS 'userid'
							FROM posts
							JOIN user ON id = userid
							WHERE userid IN (SELECT DISTINCT following_id
							                 FROM follow
							                 WHERE user_id = :userid) OR userid = :userid
							ORDER BY time_stamp DESC");
		$sql->execute(array('userid' => $userid));
		foreach ($sql->fetchAll() as $post) {
			$list[] = new PostModel($post['postid'], $post['username'], $post['userid'], $post['body'], $post['time'], $post['avatar']);
		}
		if (empty($list)) {
			return NULL;
		}
		else {
			return $list;
		}
	}

	/**************************************
		FETCH ALL THE POSTS OF THE CURRENT USER
	**************************************/
	public static function getMyPosts($userid) {
		$db = Db::getInstance();
		$sql = $db->prepare("SELECT postid, username, avatar, userid, body, DATE_FORMAT(time_stamp, '%b %d %Y %h:%i %p') AS 'time' 
							FROM posts JOIN user ON user.id=posts.userid 
							WHERE user.id = :userid 
							ORDER BY time_stamp DESC");
		$sql->execute(array('userid' => $userid));
		foreach ($sql->fetchAll() as $post) {
			$list[] = new PostModel($post['postid'], $post['username'], $post['userid'], $post['body'], $post['time'], $post['avatar']);
		}
		if (empty($list)) {
			return NULL;
		}
		else {
			return $list;
		}
	}

	/**************************************
		FETCH A SINGLE POST (USED FOR VIEWPOST.PHP)
	**************************************/
	public static function getSinglePost($postid) {
        $db = Db::getInstance();
        $sql = $db->prepare("SELECT postid, username, avatar, userid, body, DATE_FORMAT(time_stamp, '%b %d %Y %h:%i %p') AS 'time' 
                            FROM posts JOIN user ON user.id=posts.userid 
                            WHERE postid= :postid");
        $sql->execute(array('postid' => $postid));
        $post = $sql->fetch(PDO::FETCH_OBJ);
        return $post;
	}

	/**************************************
		DELETE A POST
	**************************************/
	public static function deletePost($userid, $postid) {
        $db = Db::getInstance();
        $sql = $db->prepare("DELETE FROM posts WHERE postid= :postid AND userid= :userid LIMIT 1");
        $sql->execute(array('postid' => $postid, 'userid' => $userid));
        if ($sql->rowCount()) {
			return TRUE;
		}
		else {
			return FALSE;
		}
    }
}
?>