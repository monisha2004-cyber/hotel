-- (A) ROOMS
CREATE TABLE `rooms` (
  `room_id` varchar(255) NOT NULL,
  `room_type` varchar(1) NOT NULL,
  `room_price` decimal(12,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `rooms`
  ADD PRIMARY KEY (`room_id`),
  ADD KEY `room_type` (`room_type`);

INSERT INTO `rooms` (`room_id`, `room_type`, `room_price`) VALUES
('#01-A', 'S', '10.00'),
('#01-B', 'S', '10.00'),
('#02-A', 'D', '20.00'),
('#02-B', 'T', '20.00'),
('#03-A', 'B', '30.00'),
('#04-A', 'P', '40.00');

-- (B) RESERVATIONS
CREATE TABLE `reservations` (
  `reservation_id` bigint(20) NOT NULL,
  `room_id` varchar(255) NOT NULL,
  `reservation_start` date NOT NULL,
  `reservation_end` date NOT NULL,
  `reservation_name` varchar(255) NOT NULL,
  `reservation_email` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `reservations`
  ADD PRIMARY KEY (`reservation_id`),
  ADD KEY `reservation_name` (`reservation_name`),
  ADD KEY `reservation_email` (`reservation_email`),
  ADD KEY `room_id` (`room_id`);

ALTER TABLE `reservations`
  MODIFY `reservation_id` bigint(20) NOT NULL AUTO_INCREMENT;