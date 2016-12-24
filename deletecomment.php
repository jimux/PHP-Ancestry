<?
include("include/session.php");

$id = trim($_GET['id']);
$type = $_GET['type'];

$comment = $database->getCommentByCID($id);

$session->showHeader("Delete Comment");

// Make sure the user is an admin or at least owns the comment
if($session->isAdmin() || $session->username == $comment['username']){
  $retval = $database->silenceCommentByCID($id);
  if($retval){
    echo "<p>Post deleted";
  } else {
    echo "<p>Error while processing delete request";
  }
} else {
  $database->postToLog("User " . $session->username . " tried to delete post #" .  $id);
  die("You must be an admin or own the comment");
}
?>