<!DOCTYPE html>
<html>
  <head>
    <title>Room Reservation</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="4c-reservation.css">
    <script src="4b-reservation.js"></script>
  </head>
  <body>
    <?php
    // (A) SOME DATE CALCULATIONS
    require "2-lib-hotel.php";
    $min = date("Y-m-d", strtotime("+".MIN_BOOK."days"));
    $max = date("Y-m-d", strtotime("+".MAX_BOOK."days"));
    ?>

    <!-- (B) SELECT DATE -->
    <form id="sDate" onsubmit="return rsv.get()">
      <div class="step">STEP 1 OF 3</div>
      <h1 class="head">SELECT DATE</h1>
      <label>Check In</label>
      <input type="date" id="sDateFrom"
             min="<?=$min?>" max="<?=$max?>" value="<?=$min?>">
      <label>Staying For (Days)</label>
      <input type="number" id="sDateTo"
             min="<?=MIN_STAY?>" max="<?=MAX_STAY?>" value="<?=MIN_STAY?>">
      <input type="submit" value="Next" class="button">
    </form>

    <!-- (C) SELECT ROOM -->
    <div id="sRoom" class="hide"></div>

    <!-- (D) ENTER CONTACT INFO -->
    <form id="sContact" class="hide" onsubmit="return rsv.reserve()">
      <div class="step">STEP 3 OF 3</div>
      <h1 class="head">CONTACT INFO</h1>
      <label>Name</label>
      <input type="text" name="name" required>
      <label>Email</label>
      <input type="email" name="email" required>
      <input type="button" value="Back" class="button" onclick="rsv.switch(1)">
      <input type="submit" value="Submit" class="button">
    </form>
  </body>
</html>