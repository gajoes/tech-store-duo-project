<?php

$email = $_POST["email"];

$token = bin2hex(random_bytes(16));

$token_hash = hash("sha256", $token);

$expiry = date("Y-m-d H:i:s", time() + 60 * 15);

$mysqli = require __DIR__ . "/database.php";

if (!$mysqli instanceof mysqli) {
  die("Failed to connect to the database.");
}

$sql = "UPDATE uzytkownicy
        SET reset_token_hash = ?,
        reset_token_expires_at = ?
        WHERE email = ?";

$stmt = $mysqli->prepare($sql);

$stmt->bind_param("sss", $token_hash, $expiry, $email);

$stmt->execute();

if ($mysqli->affected_rows) {
  $mail = require __DIR__ . "/mailer.php";

  $mail->setFrom("noreply@example.com");
  $mail->addAddress($email);
  $mail->Subject = "Password Reset";
  $mail->Body = <<<END

  Click <a href="http://localhost/reset-password.php?token=$token">here</a> to reset your password. 

  END;
  #zmienic nazwe na prawidlowa strone!
  try {
    $mail->send();
  } catch (Exception $e) {
    echo "Wiadomość nie mogła zostać wysłana. {$mail->ErrorInfo}";
  }
}

echo "Wiadomość została wysłana.";