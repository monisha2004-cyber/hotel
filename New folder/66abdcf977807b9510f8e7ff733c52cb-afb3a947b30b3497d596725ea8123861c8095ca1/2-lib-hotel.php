<?php
class Hotel {
  // (A) CONSTRUCTOR - CONNECT TO THE DATABASE
  private $pdo = null;
  private $stmt = null;
  public $error;
  function __construct () {
    $this->pdo = new PDO(
      "mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=".DB_CHARSET,
      DB_USER, DB_PASSWORD, [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
  }

  // (B) DESTRUCTOR - CLOSE DATABASE CONNECTION
  function __destruct () {
    if ($this->stmt !== null) { $this->stmt = null; }
    if ($this->pdo !== null) { $this->pdo = null; }
  }

  // (C) HELPER - RUN SQL QUERY
  function query ($sql, $data=null) : void {
    $this->stmt = $this->pdo->prepare($sql);
    $this->stmt->execute($data);
  }

  // (D) SAVE ROOM
  function save ($id, $type, $price, $oid=null) {
    // (D1) SQL & DATA
    $sql = $oid==null
      ? "INSERT INTO `rooms` (`room_id`, `room_type`, `room_price`) VALUES (?,?,?)"
      : "UPDATE `rooms` SET `room_id`=?, `room_type`=?, `room_price`=? WHERE `room_id`=?" ;
    $data = [$id, $type, $price];
    if ($oid!=null) { $data[] = $oid; }

    // (D2) RUN SQL
    $this->query($sql, $data);
    return true;
  }

  // (E) GET ROOMS FOR SELECTED DATE RANGE
  function get ($from=null, $to=null) {
    // (E1) GET ALL ROOMS
    $this->query("SELECT * FROM `rooms`");
    $rooms = [];
    while ($r = $this->stmt->fetch()) {
      $rooms[$r["room_id"]] = [
        "t" => ROOM_TYPE[$r["room_type"]],
        "p" => $r["room_price"]
      ];
    }

    // (E2) INCLUDE RESERVATIONS
    if ($from && $to) {
      $this->query(
        "SELECT * FROM `reservations` 
         WHERE (`reservation_start` BETWEEN ? AND ?)
         OR (`reservation_end` BETWEEN ? AND ?)", 
        [$from, $to, $from, $to]
      );
      while ($r = $this->stmt->fetch()) { if (isset($rooms[$r["room_id"]])) {
        // (E2-1) ASSUMPTION - MORNING CHECKOUT + AFTERNOON CHECKIN
        // ALLOW "SAME DAY RESERVATION" IF END DATE IS SAME
        if ($r["reservation_end"] == $from) { continue; }

        // (E2-2) MARK AS "BOOKED"
        if (!isset($rooms[$r["room_id"]]["b"])) { $rooms[$r["room_id"]]["b"] = []; }
        $rooms[$r["room_id"]]["b"][] = [
          "s" => $r["reservation_start"],
          "e" => $r["reservation_end"]
        ];
      }}
    }

    // (E3) RETURN RESULTS
    return $rooms;
  }

  // (F) SAVE RESERVATION
  function reserve ($id, $start, $end, $name, $email) {
    $this->query(
      "INSERT INTO `reservations` (`room_id`, `reservation_start`, `reservation_end`, `reservation_name`, `reservation_email`) 
       VALUES (?,?,?,?,?)", [$id, $start, $end, $name, $email]
    );
    return true;
  }
}

// (G) DATABASE SETTINGS - CHANGE TO YOUR OWN!
define("DB_HOST", "localhost");
define("DB_NAME", "test");
define("DB_CHARSET", "utf8mb4");
define("DB_USER", "root");
define("DB_PASSWORD", "");

// (H) ROOM TYPES & RULES
define("ROOM_TYPE", [
  "S" => "Single", "D" => "Double", "T" => "Twin",
  "B" => "Business", "P" => "Presidential"
]);
define("MIN_BOOK", 1); // min next day
define("MAX_BOOK", 30); // max next month
define("MIN_STAY", 1); // min 1 day stay
define("MAX_STAY", 7); // max 7 days stay

// (I) START
$_HOTEL = new hotel();