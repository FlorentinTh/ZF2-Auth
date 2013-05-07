CREATE DATABASE `zend-auth` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;

USE `zend-auth`;

CREATE TABLE `user` (
  `id` tinyint(4) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;

INSERT INTO `user` (`id`, `username`, `password`) VALUES
(null, 'user1', '5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8');
