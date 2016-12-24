<?
include("../include/session.php");
?>

<? $session->showHeader("Home");
if(!$session->isAdmin()){
  die("You must be an admin");
}
?>

<form action="../process.php" method="POST">
<table align="left" border="0" cellspacing="0" cellpadding="3">
<tr><td>Subject:</td><td>
  <input type="text" size="80" name="subject" maxlength="200" value="<? echo $form->value("subject"); ?>">
</td><td><? echo $form->error("user"); ?></td></tr>
<tr><td colspan="2">Body:</td></tr>
<tr><td colspan="2">
  <textarea type="text" cols="80" rows="40" name="body" maxlength="56000" value="<? echo $form->value("body"); ?>">Enter your news here....</textarea>
</td><td><? echo $form->error("pass"); ?></td></tr>
<tr><td colspan="2" align="right">
<input type="hidden" name="subnews" value="1">
<input type="submit" value="Post News"></td></tr>
<tr><td colspan="2" align="left"><a href="../main.php">Back to Main</a></td></tr>
<tr><td COLSPAN="3"><? echo $form->error("error"); ?></td><td></td><td></td></tr>
</table>
</form>