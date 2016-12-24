<?
include("include/session.php");
?>

<? $session->showHeader("Home"); ?>

<? echo "<table><tr><td>"; ?>

<?
/**
 * User has already logged in, so display relavent links, including
 * a link to the admin center if the user is an administrator.
 */
if($session->logged_in){
echo "<p><h3>News:</h3>";

  for ( $counter = 0; $counter <= NEWSCOUNT; $counter += 1) {
	  $dbarray = $database->getLatestNews($counter);

	  if(count($dbarray) > 5){
        if(!$dbarray['silenced'] || $session->isAdmin()){
          $color1 = userColor1;
          $color2 = userColor2;
  	      if($dbarray['silenced'] == 1){
            $color1 = adminColor1;
	        $color2 = adminColor2;
	      }
          echo "<br>";
          echo "\n<table cellpadding=\"5\" width=\"950\">";
          echo "\n  <tr><td width=\"25\"></td><td bgcolor=\"" . $color1 . "\"><b>Subject: " . $dbarray['subject'] . "</b> ";
          if($session->isAdmin()){
            echo "<a href=\"" . docRoot . "/admin/deletenews?id=" . $dbarray['id'] . "\">[Delete Post]</a> ";
            echo "<a href=\"" . docRoot . "/admin/editnews?item=" . $dbarray['id'] . "\">[Edit Post]</a>";
          }
          echo "</td></tr>";
          echo "\n  <tr><td width=\"25\"></td><td bgcolor=\"". $color2 . "\">Posted: " . $dbarray['time'] . " - by ". $dbarray['userid'] ."</td></tr>";
          echo "\n  <tr><td width=\"25\"></td><td bgcolor=\"". $color1 . "\">Message:<p>" . str_replace(array("\n"), '<br>', $dbarray['body']) . "</td></tr>";
          echo "\n  <tr><td width=\"25\"></td><td bgcolor=\"". $color2 . "\"><a href=\"" . docRoot . "/comment?id=" . $dbarray['id'] . "&type=news\">Post a Comment</a>";
          echo "\n</table>";
          if($session->isAdmin()){
            $comments = $database->getResourceComments($dbarray['id'], 'news', true);
          } else {
            $comments = $database->getResourceComments($dbarray['id'], 'news');
          }
          if($comments){
            foreach($comments as $comment){
              $color1 = userColor1;
              $color2 = userColor2;
              if($comment['silenced']){
                $color1 = adminColor1;
                $color2 = adminColor2;
              }
              echo "\n<table width=\"1000\" cellpadding=\"5\">";
              echo "\n  <tr><td width=100></td><td bgcolor=" . $color1 . ">Subject: <b>" . $comment['subject'] . "</b> ";
              if($session->isAdmin() || $session->username == $comment['username']){
                echo "<a href=\"deletecomment?id=" . $comment['id'] . "&type=" . $comment['type'] . "\">[Delete Comment]</a> ";
                echo "<a href=\"comment?id=" . $comment['id'] . "&type=" . $comment['type'] . "&edit=true\">[Edit Comment]</a> ";
              }
              echo "</td><td width=100></td></tr>";
              echo "\n  <tr><td width=100></td><td bgcolor=" . $color2 . ">Posted by: " . $comment['timestamp'] . " - by " . $comment['username'] . "</td><td width=100></td></tr>";
              echo "\n  <tr><td width=100></td><td bgcolor=" . $color1 . ">Comment:<br>" . $comment['text'] . "</td><td width=100></td></tr>";
              echo "</table>\n";
            }
          }
        }
      }
  }
  
} else {
?>

<h2>Login</h2>
<?
/**
 * User not logged in, display the login form.
 * If user has already tried to login, but errors were
 * found, display the total number of errors.
 * If errors occurred, they will be displayed.
 */
if($form->num_errors > 0){
   echo "<font size=\"2\" color=\"#ff0000\">".$form->num_errors." error(s) found</font>";
}
?>
<form action="process.php" method="POST">
<table align="left" border="0" cellspacing="0" cellpadding="3">
<tr><td>Username:</td><td><input type="text" name="user" maxlength="30" value="<? echo $form->value("user"); ?>"></td><td><? echo $form->error("user"); ?></td></tr>
<tr><td>Password:</td><td><input type="password" name="pass" maxlength="30" value="<? echo $form->value("pass"); ?>"></td><td><? echo $form->error("pass"); ?></td></tr>
<tr><td colspan="2" align="left">
<input type="hidden" name="sublogin" value="1">
<input type="submit" value="Login"></td></tr>
<tr><td colspan="2" align="left"><br><font size="2">[<a href="forgotpass">Forgot Password?</a>]</font></td><td align="right"></td></tr>
</table>
</form>

<?
}

/**
 * Just a little page footer, tells how many registered members
 * there are, how many users currently logged in and viewing site,
 * and how many guests viewing site. Active users are displayed,
 * with link to their user information.
 */
echo "</td></tr><tr><td align=\"center\"><br><br>";
echo "There are $database->num_active_users registered members and ";
echo "$database->num_active_guests guests viewing the site.<br><br>";
?>

<a href="ancestors/stats">View People Statistics</a>

<?
include("include/view_active.php");
?>


</td></tr>
</table>
</body>
</html>
