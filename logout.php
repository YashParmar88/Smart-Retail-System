<?php
/* start the session to identify which user is logging out */
session_start();

/* destroy all session data (clear the identity card) */
session_destroy();

/* send the user back to the login page safely */
header("Location: index.php");
exit();
?>