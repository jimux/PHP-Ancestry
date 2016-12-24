<?
include("../include/session.php");

$session->showHeader("View Person");
if(!$session->logged_in){
  die("You must be logged in");
}
?>

</table>
<form action="../process.php" method="POST">
<table align="left" border="0" cellspacing="0" cellpadding="3">
  <tr><td>Subject:</td><td>
<? echo "    <input type=\"text\" size=\"80\" name=\"subject\" maxlength=\"200\" value=\"" . "\">"; ?>
    </td><td></td></tr>
  <tr><td colspan="2">Body:</td></tr>
  <tr><td colspan="2">
<? echo "    <textarea type=\"text\" cols=\"80\" rows=\"40\" name=\"body\" maxlength=\"56000\" value=\"" . "\">Enter your comment here....</textarea>"; ?>
    </td><td></td></tr>
  <tr><td colspan="2" align="right">
    <input type="hidden" name="subpersoncomment" value="1">
    <input type="submit" value="Post Comment"></td></tr>
<? echo "  <tr><td colspan=\"2\" align=\"left\"><a href=\"" . docRoot . "/ancestors/viewperson.php?person=" . $_GET['id'] . "\">Back to person</a></td></tr>"; ?>
  <tr><td COLSPAN="3"></td><td></td><td></td></tr>
</table>
