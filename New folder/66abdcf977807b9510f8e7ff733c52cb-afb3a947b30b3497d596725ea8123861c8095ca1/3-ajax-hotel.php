<?php
if (isset($_POST["req"])) {
  require "2-lib-hotel.php";
  switch ($_POST["req"]) {
    // (A) GET AVAILABLE ROOMS
    case "get":
      echo json_encode($_HOTEL->get($_POST["from"], $_POST["to"]));
      break;

    // (B) RESERVE ROOM
    case "reserve":
      echo $_HOTEL->reserve($_POST["id"], $_POST["start"], $_POST["end"], $_POST["name"], $_POST["email"])
        ? "OK" : $_HOTEL->error ;
      break;
  }
}