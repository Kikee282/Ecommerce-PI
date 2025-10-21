<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $name = trim($_POST["name"]);
  $email = trim($_POST["email"]);
  $message = trim($_POST["message"]);

  $errors = [];

  if (empty($name)) $errors[] = "El nom és obligatori.";
  if (empty($email)) $errors[] = "El correu electrònic és obligatori.";
  elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Introdueix un correu electrònic vàlid.";
  if (strlen($message) < 5) $errors[] = "El missatge ha de tindre almenys 5 caràcters.";

  if (!empty($errors)) {
    echo "<h3 style='color:red;'>S'han trobat errors:</h3><ul>";
    foreach ($errors as $e) echo "<li>$e</li>";
    echo "</ul><a href='index.html'>Tornar</a>";
  } else {
    echo "<h3 style='color:green;'>Missatge enviat correctament!</h3>";
    echo "<a href='index.html'>Tornar</a>";
  }
}
?>
