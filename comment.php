<?
include("include/session.php");



$edit = false;
$id = $_GET['id'];
if($_GET['edit'] == "true"){
  $edit = true;
}

$subject = "";
$body = "";

$resource = $database->getCommentByCID($id);

if($id && $edit){
  $comment = $database->getCommentByCID($_GET['id']);
  $subject = $comment['subject'];
  $body = $comment['text'];
}

$session->showHeader("Comment on " . $_GET['type']);
if(!$session->logged_in){
  die("You must be logged in");
}
?>

</table>
<form action="process.php" method="POST">
<table align="left" border="0" cellspacing="0" cellpadding="3">
  <tr><td>Subject:</td><td>
<? echo "    <input type=\"text\" size=\"80\" name=\"subject\" maxlength=\"200\" value=\"" . $subject . "\">"; ?>
    </td><td></td></tr>
  <tr><td colspan="2">Body:</td></tr>
  <tr><td colspan="2">
<?
if($edit){
  echo "    <textarea type=\"text\" cols=\"80\" rows=\"40\" name=\"body\" maxlength=\"56000\" value=\"" . "\">" . $body . "</textarea>";
} else {
  echo "    <textarea type=\"text\" cols=\"80\" rows=\"40\" name=\"body\" maxlength=\"56000\" value=\"" . "\">Enter your comment here....</textarea>";
}
?>
    </td><td></td></tr>
  <tr><td colspan="2" align="right">
<?
   if(intval($edit) > 0){
     echo "\n    <input type=\"hidden\" name=\"edit" . trim($_GET['type']) . "comment\" value=\"" . $_GET['editpersoncomment'] . "\">";
   } else {
     echo "\n    <input type=\"hidden\" name=\"sub" . trim($_GET['type']) . "comment\" value=\"1\">";
   }
   echo "\n    <input type=\"hidden\" name=\"type\" value=\"" . trim($_GET['type']) . "\">";
   echo "\n    <input type=\"hidden\" name=\"id\" value=\"" . $id . "\">";
?>
    <input type="submit" value="Post Comment"></td></tr>
  <tr><td COLSPAN="3"></td><td></td><td></td></tr>
</table>
