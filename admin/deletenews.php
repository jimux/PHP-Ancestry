<?
include("../include/session.php");
?>

<? $session->showHeader("Home");
if(!$session->isAdmin()){
  $database->postToLog("User " . $session->username . " tried to delete post #" .  $_GET['id']);
  die("You must be an admin");
} else {
  $database->postToLog("User " . $session->username . " silencing post #" . $_GET['id']);
  $postid = trim($_GET['id']);
  $q = "DELETE FROM news WHERE id = '" . $postid . "'";
  $query = "UPDATE news SET silenced='1' WHERE id='" . $postid . "'";
  $retval = mysql_query($query, $database->connection) or die('Invalid query: ' . mysql_error());

  echo "<p>Post deleted";
}
?>