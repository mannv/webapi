CREATE DATABASE IF NOT EXISTS `webapi_db` COLLATE 'utf8mb4_unicode_ci';
GRANT ALL ON `webapi_db`.* TO 'webapi_user'@'%';

FLUSH PRIVILEGES;