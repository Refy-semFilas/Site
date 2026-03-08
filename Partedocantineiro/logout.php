<?php
session_start();
session_destroy();
header("Location: ../Partedocliente/loginForm.html");
exit;
