<?
include("include/session.php");
?>

<? $session->showHeader("Help"); ?>

<br><b>Q: How do I create a person?</b>
<br>A: You can find the link to create a new person in the "My Account" page.

<?
if($session->isAdmin()){
	echo "<p><b>Q: How do I </b>";
	echo "<br>A: ";
}
?>

</body></html>