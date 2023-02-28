drop database if exists rs_dev;
create database rs_dev default character set utf8;

GRANT ALL PRIVILEGES ON `rs_dev`.* TO 'rs_dev'@'%' IDENTIFIED BY 'root';
