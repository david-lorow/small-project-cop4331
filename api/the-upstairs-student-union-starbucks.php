<?php
//Error code 418 manually implemented because the server doesn't like it I guess
header ($_SERVER["SERVER_PROTOCOL" ] . " 418 I'm a teapot");
header("Content-Type: application/json");
echo json_encode([
  "error" => "We don't brew coffee past 11am"
]);
exit;