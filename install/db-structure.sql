CREATE TABLE IF NOT EXISTS `agency`
(
    `id`        int(11)     NOT NULL AUTO_INCREMENT,
    `name`      varchar(60) NOT NULL,
    `telephone` char(10)    NOT NULL,
    `city`      varchar(60) NOT NULL,
    `queue`     longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `agency_telephone_uindex` (`telephone`),
    UNIQUE KEY `agency_name_city_uindex` (`name`, `city`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

CREATE TABLE IF NOT EXISTS `active_buyer_list`
(
    `telephone` char(10) NOT NULL,
    PRIMARY KEY (`telephone`)
) ENGINE = InnoDB
  DEFAULT CHARSET = latin1;
