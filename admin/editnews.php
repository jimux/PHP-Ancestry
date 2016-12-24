<?
include("../include/session.php");
?>

<? $session->showHeader("Home");
if(!$session->isAdmin()){
  die("You must be an admin");
}

$postid = trim($_GET['item']);
$q = "SELECT " . $postid . " AS post FROM news";
$result = mysql_query($q, $database->connection);

$resarray = mysql_fetch_array($result);
$index = intval($resarray['post']);

// Fetch it
$q2 = "SELECT * FROM news WHERE id = '". $index ."'";
$result = mysql_query($q2, $database->connection);
$dbarray = mysql_fetch_array($result);
$postid = $_GET['item'];
?>

<form action="../process.php" method="POST">
<table align="left" border="0" cellspacing="0" cellpadding="3">
<tr><td>Subject:</td><td>
  <input type="text" size="80" name="subject" maxlength="200" value="<? echo $dbarray['subject'] ?>">
</td><td><? echo $form->error("user"); ?></td></tr>
<tr><td colspan="2">Body:</td></tr>
<tr><td colspan="2">
  <textarea type="text" cols="80" rows="40" name="body" maxlength="56000" value=""><? echo $dbarray['body'] ?></textarea>
</td><td><? echo $form->error("pass"); ?></td></tr>
<tr><td colspan="2" align="right">
<? echo "<input type=\"hidden\" name=\"postid\" value=\"" . $postid . "\">"; ?>
<input type="hidden" name="subeditnews" value="1">
<input type="submit" value="Post News"></td></tr>
<tr><td colspan="2" align="left"><a href="../main.php">Back to Main</a></td></tr>
<tr><td COLSPAN="3"><? echo $form->error("error"); ?></td><td></td><td></td></tr>
</table>
</form>