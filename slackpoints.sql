-- MySQL dump 10.13  Distrib 5.6.24, for osx10.8 (x86_64)
--
-- Host: localhost    Database: slackpoints
-- ------------------------------------------------------
-- Server version	5.5.46

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `debug`
--

DROP TABLE IF EXISTS `debug`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `debug` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `data` text,
  `date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=736 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `points`
--

DROP TABLE IF EXISTS `points`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `points` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `target_user_id` int(11) NOT NULL,
  `team_id` varchar(100) NOT NULL,
  `creating_user_id` int(11) NOT NULL,
  `status_id` int(11) NOT NULL,
  `date` varchar(45) NOT NULL,
  `channel_id` varchar(100) NOT NULL,
  `channel_name` varchar(255) NOT NULL,
  `private` bit(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=439 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `teams`
--

DROP TABLE IF EXISTS `teams`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `teams` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `team_id` varchar(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `token` varchar(100) NOT NULL,
  `slackbot_token` varchar(100) NOT NULL,
  `short_name` varchar(100) NOT NULL,
  `timezone` varchar(50) NOT NULL,
  `minutes_per_point` int(11) NOT NULL,
  `minutes` int(11) DEFAULT NULL,
  `points` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `team_id_UNIQUE` (`team_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` varchar(100) NOT NULL,
  `team_id` varchar(100) NOT NULL,
  `user_name` varchar(200) NOT NULL,
  `date_created` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping events for database 'slackpoints'
--

--
-- Dumping routines for database 'slackpoints'
--
/*!50003 DROP PROCEDURE IF EXISTS `Debug_Save` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
CREATE DEFINER=`slackpoints`@`%` PROCEDURE `Debug_Save`(

	IN data text

)
BEGIN

	INSERT INTO debug (data, date) VALUES (data, NOW());

END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `Teams_GetLeaderboard` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
CREATE DEFINER=`slackpoints`@`%` PROCEDURE `Teams_GetLeaderboard`(

	IN team_id varchar(100),
	IN start_date datetime

)
BEGIN

	-- Team info
	SELECT 		teams.slackbot_token,
				teams.short_name
	FROM		teams
	WHERE		teams.team_id = team_id;

	-- Leaderboard
	SELECT 		COUNT(*) AS total,
				users.user_name
	FROM		points 
	INNER JOIN 	users ON users.id = points.target_user_id
	WHERE		points.status_id = 1
	AND			points.team_id = team_id
	AND			(points.date >= start_date OR start_date IS NULL)
	GROUP BY	points.target_user_id
	ORDER BY 	total DESC;

END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `Teams_GetTokenByTeamID` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
CREATE DEFINER=`slackpoints`@`%` PROCEDURE `Teams_GetTokenByTeamID`(

	IN team_id varchar(100)

)
BEGIN

	SELECT teams.token FROM teams WHERE teams.team_id = team_id LIMIT 1;

END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `Users_SavePoint` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
CREATE DEFINER=`slackpoints`@`%` PROCEDURE `Users_SavePoint`(

	IN slack_team_id varchar(100),
	IN slack_channel_id varchar(100),
	IN slack_channel_name varchar(255),
	IN slack_creating_user_id varchar(100),
	IN slack_creating_user_name varchar(200),
	IN slack_target_user_name varchar(200),
	IN slack_command_text varchar(500),
	IN private bit

)
BEGIN

-- Vars
DECLARE creating_user_id INT;
DECLARE target_user_id INT;
DECLARE status_id INT DEFAULT 1;
DECLARE point_id INT;
#DECLARE minutes_per_point INT;
#DECLARE last_point_date datetime;
DECLARE total_minutes INT;
DECLARE total_points INT;
DECLARE xth_last_point_date datetime;

-- Minutes per point
#SET minutes_per_point = (SELECT teams.minutes_per_point FROM teams WHERE teams.team_id = slack_team_id LIMIT 1);
SET total_minutes = (SELECT teams.minutes FROM teams WHERE teams.team_id = slack_team_id LIMIT 1);
SET total_points = (SELECT teams.points FROM teams WHERE teams.team_id = slack_team_id LIMIT 1);

-- Create/update creating user
SET creating_user_id = (
	SELECT 	users.id 
	FROM 	users 
	WHERE	users.team_id = slack_team_id
	AND 	(users.user_id = slack_creating_user_id OR users.user_name = slack_creating_user_name)
);
IF creating_user_id IS NULL THEN
	INSERT INTO users (user_id, team_id, user_name, date_created) 
	VALUES (slack_creating_user_id, slack_team_id, slack_creating_user_name, utc_timestamp());
	SET creating_user_id = LAST_INSERT_ID();
ELSE
	UPDATE 	users 
	SET 	user_id = slack_creating_user_id, 
			user_name = slack_creating_user_name 
	WHERE 	users.id = creating_user_id
	AND		users.team_id = slack_team_id;
END IF;

-- Create target user
SET target_user_id = (SELECT users.id FROM users WHERE users.user_name = slack_target_user_name AND users.team_id = slack_team_id);
IF target_user_id IS NULL THEN
	INSERT INTO users (team_id, user_name, date_created) 
	VALUES (slack_team_id, slack_target_user_name, utc_timestamp());
	SET target_user_id = LAST_INSERT_ID();
END IF;

-- User trying to self-assign point
IF slack_creating_user_name = slack_target_user_name THEN
	SET status_id = 2;
END IF;

-- User hasn't waited required time
IF status_id = 1 AND (
	SELECT 	COUNT(points.id)
	FROM 	points 
	WHERE 	points.creating_user_id = creating_user_id
	AND		points.team_id = slack_team_id
	AND		points.status_id = 1
	AND		DATE_ADD(points.date, INTERVAL total_minutes MINUTE) >= UTC_TIMESTAMP()
) >= total_points THEN
	SET status_id = 3;
END IF;

-- Save point
INSERT INTO points (target_user_id, team_id, channel_id, channel_name, creating_user_id, status_id, date, private)
VALUES (target_user_id, slack_team_id, slack_channel_id, slack_channel_name, creating_user_id, status_id, UTC_TIMESTAMP(), private);
SET point_id = LAST_INSERT_ID();

-- Last point date
#SET last_point_date = (SELECT points.date FROM points WHERE points.creating_user_id = creating_user_id AND points.team_id = slack_team_id AND points.status_id = 1 ORDER BY points.id DESC LIMIT 1);

-- xth last point
SET xth_last_point_date = (
	SELECT 	date 
	FROM (
		SELECT 		points.date 
		FROM 		points 
		WHERE 		points.creating_user_id = creating_user_id 
		AND 		points.team_id = slack_team_id 
		AND 		points.status_id = 1 
		ORDER BY 	date DESC 
		LIMIT 		total_points
	) AS recent_points
	ORDER BY date LIMIT 1
);

-- Select point data
SELECT 	status_id, 
		CASE

			WHEN (
				SELECT 	COUNT(points.id)
				FROM 	points 
				WHERE 	points.creating_user_id = creating_user_id
				AND		points.team_id = slack_team_id
				AND		points.status_id = 1
				AND		DATE_ADD(points.date, INTERVAL total_minutes MINUTE) >= UTC_TIMESTAMP()
			) >= total_points THEN (total_minutes - TIMESTAMPDIFF(MINUTE, xth_last_point_date, UTC_TIMESTAMP()))

			ELSE 0

		END AS time_until_next_point,
		(SELECT COUNT(*) FROM points WHERE points.target_user_id = target_user_id AND points.status_id = 1 AND points.team_id = slack_team_id) AS total_points_for_user;

-- Select team data
SELECT 	teams.token, 
		teams.slackbot_token, 
		teams.short_name 
FROM 	teams 
WHERE 	teams.team_id = slack_team_id;

END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2016-09-28  9:43:51
