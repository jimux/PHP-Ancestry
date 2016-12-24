<?
/**
 * Process.php
 * 
 * The Process class is meant to simplify the task of processing
 * user submitted forms, redirecting the user to the correct
 * pages if errors are found, or if form is successful, either
 * way. Also handles the logout procedure.
 *
 * Written by: Jpmaster77 a.k.a. The Grandmaster of C++ (GMC)
 * Last Updated: August 19, 2004
 */
include("include/session.php");

class Process
{
   /* Class constructor */
   function Process(){
      global $session;
      /* User submitted login form */
      if(isset($_POST['sublogin'])){
         $this->procLogin();
      }
      
      /* User is submitting news */
      else if(isset($_POST['subnews'])){
         $this->procPostNews();
      }
      
      /* User is submitting news */
      else if(isset($_POST['subeditnews'])){
         $this->procEditNews();
      }
      
      /* User submitted registration form */
      else if(isset($_POST['subjoin'])){
         $this->procRegister();
      }
      
      /* User is adding a person */
      else if(isset($_POST['subperson'])){
         $this->procNewPerson();
      }
      
      /* User is editing a person */
      else if(isset($_POST['subeditperson'])){
         $this->procEditPerson();
      }
      
      /* User is commenting on or editing a comment on a person */
      else if(isset($_POST['subpersoncomment'])){
        if(intval($_POST['edit']) > 1){
          $this->procEditComment();
        } else {
          $this->procCommentResource('person');
        }
      }
      
      else if(isset($_POST['editnewscomment'])){
        $this->procEditComment("news");
      }
      
      else if(isset($_POST['editpersoncomment'])){
        $this->procEditComment("person");
      }
      
      /* User is commenting on a news item */
      else if(isset($_POST['subnewscomment'])){
         $this->procCommentResource('news');
      }
      
      /* User submitted forgot password form */
      else if(isset($_POST['subforgot'])){
         $this->procForgotPass();
      }
      
      /* User submitted edit account form */
      else if(isset($_POST['subedit'])){
         $this->procEditAccount();
      }
      /**
       * The only other reason user should be directed here
       * is if he wants to logout, which means user is
       * logged in currently.
       */
      else if($session->logged_in){
         $this->procLogout();
      }
      /**
       * Should not get here, which means user is viewing this page
       * by mistake and therefore is redirected.
       */
       else{
          header("Location: index");
       }
   }
   
   function procEditPerson(){
     global $session, $form, $database;
     $database->postToLog("User " . $session->username . " is updating person #" . $_POST['pid']);
     $retval = $database->updatePerson($_POST['first'], $_POST['middle'],
                                  $_POST['last'], $_POST['mother'],
                                  $_POST['father'], $_POST['children'],
                                  $_POST['story'], $_POST['birthMonth'],
                                  $_POST['birthDay'], $_POST['birthYear'],
                                  $_POST['deathMonth'], $_POST['deathDay'],
                                  $_POST['deathYear'], $_POST['sex'], $_POST['pid']);
     /* successful */
     if($retval){
       $_SESSION['value_array'] = $_POST;
       header("Location: ".docRoot."/ancestors/viewperson?person=".$_POST['pid']);
     } /* failed */ else{
       header("Location: ".$session->referrer);
     }
   }
   
   function procEditComment($type){
     global $database, $form;
     $id = $_POST['id'];
     $subject = $_POST['subject'];
     $text = $_POST['body'];
     $comment = $database->getCommentByCID($id);
     $retval = $database->updateComment($id, $subject, $text);
     if($retval){
       if($type == "person"){
         $_SESSION['value_array'] = $_POST;
         header("Location: ".docRoot."/ancestors/viewperson?person=".$comment['resource']);
       } else if($type == "news"){
         header("Location: ".docRoot."/index");
       }
     } else {
       header("Location: ".$session->referrer);
     }
   }
   
   function procCommentResource($type){
     global $session, $database, $form;
     $database->postToLog("User " . $session->username . " is commenting on " . $type . " #" . $_POST['id']);
     $retval = $database->commentResource($session->username, $_POST['subject'], $_POST['body'], $_POST['id'], $type);
     if($retval){
       $_SESSION['value_array'] = $_POST;
       if($type == "person"){
         header("Location: ".docRoot."/ancestors/viewperson?person=".$_POST['id']);
       } else if($type == "news"){
         header("Location: ".docRoot);
       }
     } else {
       header("Location: ".$session->referrer);
     }
   }
   
   /**
    * procNewPerson - Processes a post for a new person in the
    * ancestory database.
    */
   function procNewPerson(){
     global $session, $database, $form;
     $database->postToLog("User " . $session->username . " is creating a new person :" . $_POST['first'] . " " . $_POST['last']);
     $retval = $database->newPerson($_POST['first'], $_POST['middle'],
                               $_POST['last'], $_POST['mother'],
                               $_POST['father'], $_POST['children'],
                               $_POST['story'], $_POST['birthMonth'],
                               $_POST['birthDay'], $_POST['birthYear'],
                               $_POST['deathMonth'], $_POST['deathDay'],
                               $_POST['deathYear'], $_POST['sex']);
      /* successful */
      if($retval){
        $_SESSION['value_array'] = $_POST;
        header("Location: ".docRoot."/ancestors/viewperson?person=".$_POST['id']);
      } /* failed */ else{
        header("Location: ".$session->referrer);
      }
   }
   
   function procNewPersonComment(){
   	 global $session, $database, $form;
   }

   /**
    * procLogin - Processes the user submitted login form, if errors
    * are found, the user is redirected to correct the information,
    * if not, the user is effectively logged in to the system.
    */
   function procLogin(){
      global $session, $form, $database;
      /* Login attempt */
      $retval = $session->login($_POST['user'], $_POST['pass'], true);
      
      /* Login successful */
      if($retval){
         $database->postToLog("User " . $session->username . " is logged in");
         header("Location: ".$session->referrer);
      }
      /* Login failed */
      else{
         $database->postToLog("Failed login for user " . $_POST['user']);
         $_SESSION['value_array'] = $_POST;
         $_SESSION['error_array'] = $form->getErrorArray();
         header("Location: ".$session->referrer);
      }
   }
   
   /**
    * procLogout - Simply attempts to log the user out of the system
    * given that there is no logout form to process.
    */
   function procLogout(){
      global $session, $database;
      $database->postToLog("User " . $session->username . " is logging out");
      $retval = $session->logout();
      header("Location: index");
   }
   
   /**
    * procRegister - Processes the user submitted registration form,
    * if errors are found, the user is redirected to correct the
    * information, if not, the user is effectively registered with
    * the system and an email is (optionally) sent to the newly
    * created user.
    */
   function procRegister(){
      global $session, $form, $database;
      
      /* Convert username to all lowercase (by option) */
      if(ALL_LOWERCASE){
         $_POST['user'] = strtolower($_POST['user']);
      }
      /* Registration attempt */
      $retval = $session->register($_POST['user'], $_POST['pass'], $_POST['passcheck'], $_POST['email']);
      
      /* Registration Successful */
      if($retval == 0){
         $database->postToLog("Registered user " . $_POST['user']);
         $_SESSION['reguname'] = $_POST['user'];
         $_SESSION['regsuccess'] = true;
         header("Location: ".$session->referrer);
      }
      /* Error found with form */
      else if($retval == 1){
         $_SESSION['value_array'] = $_POST;
         $_SESSION['error_array'] = $form->getErrorArray();
         header("Location: ".$session->referrer);
      }
      /* Registration attempt failed */
      else if($retval == 2){
         $database->postToLog("Failed registration attempt with username: " . $_POST['user']);
         $_SESSION['reguname'] = $_POST['user'];
         $_SESSION['regsuccess'] = false;
         header("Location: ".$session->referrer);
      }
   }
   
   function procEditNews(){
     global $session, $form, $database;
     $database->postToLog("User " . $session->username . " edited news item #" . $_POST['postid']);
     if($_POST['subject'] == ""){
        $form->setError("subject", "Subject can not be empty");
      }
      if($_POST['body'] == ""){
        $form->setError("body", "Body can not be empty");
      }
      $retval = $database->postEditItem($_POST['subject'], $_POST['body'], $_POST['postid'], $session->username);
      
      if($retval){
         header("Location: index");
      }
   }
   
   function procPostNews(){
      global $session, $form, $database;
      $database->postToLog("User " . $session->username . " posted news item with subject: " . $_POST['subject']);
      if($_POST['subject'] == ""){
        $form->setError("subject", "Subject can not be empty");
      }
      if($_POST['body'] == ""){
        $form->setError("body", "Body can not be empty");
      }
      
      $retval = $database->postNewsItem($_POST['subject'], $_POST['body'], $session->username);
      
      if($retval){
         header("Location: index");
      }
   }
   
   function register($subuser, $subpass, $subpasscheck, $subemail){
      global $database, $form, $mailer;  //The database, form and mailer object
      
      /* Username error checking */
      $field = "user";  //Use field name for username
      if(!$subuser || strlen($subuser = trim($subuser)) == 0){
         $form->setError($field, "* Username not entered");
      }
      else if($subpass !== $subpasscheck){
        $field = "pass2";
      	$form->setError($field, "Passwords to not match");
      }
      else{
         /* Spruce up username, check length */
         $subuser = stripslashes($subuser);
         if(strlen($subuser) < 5){
            $form->setError($field, "* Username below 5 characters");
         }
         else if(strlen($subuser) > 30){
            $form->setError($field, "* Username above 30 characters");
         }
         /* Check if username is not alphanumeric */
         else if(!eregi("^([0-9a-z])+$", $subuser)){
            $form->setError($field, "* Username not alphanumeric");
         }
         /* Check if username is reserved */
         else if(strcasecmp($subuser, GUEST_NAME) == 0){
            $form->setError($field, "* Username reserved word");
         }
         /* Check if username is already in use */
         else if($database->usernameTaken($subuser)){
            $form->setError($field, "* Username already in use");
         }
         /* Check if username is banned */
         else if($database->usernameBanned($subuser)){
            $form->setError($field, "* Username banned");
         }
      }

      /* Password error checking */
      $field = "pass";  //Use field name for password
      if(!$subpass){
         $form->setError($field, "* Password not entered");
      }
      else{
         /* Spruce up password and check length*/
         $subpass = stripslashes($subpass);
         if(strlen($subpass) < 4){
            $form->setError($field, "* Password too short");
         }
         /* Check if password is not alphanumeric */
         else if(!eregi("^([0-9a-z])+$", ($subpass = trim($subpass)))){
            $form->setError($field, "* Password not alphanumeric");
         }
         /**
          * Note: I trimmed the password only after I checked the length
          * because if you fill the password field up with spaces
          * it looks like a lot more characters than 4, so it looks
          * kind of stupid to report "password too short".
          */
      }
      
      /* Email error checking */
      $field = "email";  //Use field name for email
      if(!$subemail || strlen($subemail = trim($subemail)) == 0){
         $form->setError($field, "* Email not entered");
      }
      else{
         /* Check if valid email address */
         $regex = "^[_+a-z0-9-]+(\.[_+a-z0-9-]+)*"
                 ."@[a-z0-9-]+(\.[a-z0-9-]{1,})*"
                 ."\.([a-z]{2,}){1}$";
         if(!eregi($regex,$subemail)){
            $form->setError($field, "* Email invalid");
         }
         $subemail = stripslashes($subemail);
      }

      /* Errors exist, have user correct them */
      if($form->num_errors > 0){
         return 1;  //Errors with form
      }
      /* No errors, add the new account to the */
      else{
         if($database->addNewUser($subuser, md5($subpass), $subemail)){
            if(EMAIL_WELCOME){
               $mailer->sendWelcome($subuser,$subemail,$subpass);
            }
            return 0;  //New user added succesfully
         }else{
         	$form->setError("error", "Error with database. Try again later.");
            return 1;  //Registration attempt failed
         }
      }
   }
   
   /**
    * procForgotPass - Validates the given username then if
    * everything is fine, a new password is generated and
    * emailed to the address the user gave on sign up.
    */
   function procForgotPass(){
      global $database, $session, $mailer, $form;
      $database->postToLog("Helping user ". $_POST['user'] ." retrieve password.");
      /* Username error checking */
      $subuser = $_POST['user'];
      $field = "user";  //Use field name for username
      if(!$subuser || strlen($subuser = trim($subuser)) == 0){
         $form->setError($field, "* Username not entered<br>");
      }
      else{
         /* Make sure username is in database */
         $subuser = stripslashes($subuser);
         if(strlen($subuser) < 5 || strlen($subuser) > 30 ||
            !eregi("^([0-9a-z])+$", $subuser) ||
            (!$database->usernameTaken($subuser))){
            $form->setError($field, "* Username does not exist<br>");
         }
      }
      
      /* Errors exist, have user correct them */
      if($form->num_errors > 0){
         $_SESSION['value_array'] = $_POST;
         $_SESSION['error_array'] = $form->getErrorArray();
      }
      /* Generate new password and email it to user */
      else{
         /* Generate new password */
         $newpass = $session->generateRandStr(8);
         
         /* Get email of user */
         $usrinf = $database->getUserInfo($subuser);
         $email  = $usrinf['email'];
         
         /* Attempt to send the email with new password */
         if($mailer->sendNewPass($subuser,$email,$newpass)){
            /* Email sent, update database */
            $database->updateUserField($subuser, "password", md5($newpass));
            $_SESSION['forgotpass'] = true;
         }
         /* Email failure, do not change password */
         else{
            $_SESSION['forgotpass'] = false;
         }
      }
      
      header("Location: ".$session->referrer);
   }
   
   /**
    * procEditAccount - Attempts to edit the user's account
    * information, including the password, which must be verified
    * before a change is made.
    */
   function procEditAccount(){
      global $session, $form, $database;
      $database->postToLog("User " . $session->username . " has edited their account.");
      /* Account edit attempt */
      $retval = $session->editAccount($_POST['curpass'], $_POST['newpass'], $_POST['email']);

      /* Account edit successful */
      if($retval){
         $_SESSION['useredit'] = true;
         header("Location: ".$session->referrer);
      }
      /* Error found with form */
      else{
         $_SESSION['value_array'] = $_POST;
         $_SESSION['error_array'] = $form->getErrorArray();
         header("Location: ".$session->referrer);
      }
   }
};

/* Initialize process */
$process = new Process;

?>
