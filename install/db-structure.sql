CREATE TABLE IF NOT EXISTS `agency` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(60) NOT NULL,
  `city` varchar(60) NOT NULL,
  `telephone` char(10) NOT NULL,
  `current_batch_size` int(11) DEFAULT 0,
  `available_amount` int(11) DEFAULT 0,
  `issued_amount` int(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `agency_telephone_uindex` (`telephone`),
  UNIQUE KEY `agency_name_city_uindex` (`name`,`city`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `buyer` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `telephone` char(10) NOT NULL,
  `agency_id` int(11) NOT NULL,
  `otp` char(8) NOT NULL,
  `notified_on` datetime DEFAULT NULL,
  `status` enum('WAITING','PENDING_NOTIFICATION','NOTIFIED','FAILED_TO_NOTIFY','EXPIRED') DEFAULT 'WAITING',
  PRIMARY KEY (`id`),
  UNIQUE KEY `buyer_telephone_uindex` (`telephone`),
  KEY `buyer_agency_id_fk` (`agency_id`),
  CONSTRAINT `buyer_agency_id_fk` FOREIGN KEY (`agency_id`) REFERENCES `agency` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=latin1;

DELIMITER ;;
CREATE FUNCTION `queue_length`(agencyId INT) RETURNS INT
BEGIN
    RETURN (SELECT COUNT(*) FROM buyer WHERE agency_id = agencyId AND status != 'EXPIRED');
END ;;
DELIMITER ;
