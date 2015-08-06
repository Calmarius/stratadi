<?php

require_once('setupmysqli.php');

// Check if a table already exists.
$r = runEscapedQuery("SELECT 1 FROM wtfb2_villages LIMIT 1");
if (!isEmptyResult($r))
{
	die('The server is already installed!');
}

// Creating tables

runEscapedQuery("
CREATE TABLE IF NOT EXISTS `linkexchange` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `url` text CHARACTER SET utf8 NOT NULL,
  `title` text CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
");

runEscapedQuery("
CREATE TABLE IF NOT EXISTS `wtfb2_accesses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `accountId` int(11) NOT NULL,
  `userName` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `passwordHash` varchar(255) COLLATE utf8_bin NOT NULL,
  `eMail` varchar(255) COLLATE utf8_bin NOT NULL,
  `permission` enum('banned','inactive','user','moderator','admin') COLLATE utf8_bin NOT NULL DEFAULT 'user',
  `activationToken` varchar(255) COLLATE utf8_bin NOT NULL,
  `city` varchar(100) COLLATE utf8_bin NOT NULL,
  `birth` datetime NOT NULL,
  `gender` enum('male','female') COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `userName` (`userName`),
  KEY `accountId` (`accountId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;
");

runEscapedQuery("
CREATE TABLE IF NOT EXISTS `wtfb2_deputies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sponsorId` int(11) NOT NULL,
  `deputyId` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sponsorId` (`sponsorId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;
");

runEscapedQuery("
CREATE TABLE IF NOT EXISTS `wtfb2_diplomacy` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `attitude` enum('ally','peace','war') COLLATE utf8_bin NOT NULL,
  `guildId` int(11) NOT NULL,
  `toGuildId` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `guildId` (`guildId`,`toGuildId`),
  KEY `toGuildId` (`toGuildId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;
");

runEscapedQuery("
CREATE TABLE IF NOT EXISTS `wtfb2_events` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `eventType` enum('attack','raid','recon','move','settle','return','heromove') COLLATE utf8_bin NOT NULL,
  `happensAt` datetime NOT NULL,
  `estimatedTime` datetime NOT NULL,
  `launchedAt` datetime NOT NULL,
  `launcherVillage` int(11) NOT NULL,
  `destinationVillage` int(11) NOT NULL DEFAULT '0',
  `targetX` int(11) NOT NULL DEFAULT '0',
  `targetY` int(11) NOT NULL DEFAULT '0',
  `spearmen` int(11) NOT NULL DEFAULT '0',
  `archers` int(11) NOT NULL DEFAULT '0',
  `knights` int(11) NOT NULL DEFAULT '0',
  `catapults` int(11) NOT NULL DEFAULT '0',
  `diplomats` int(11) NOT NULL DEFAULT '0',
  `catapultTarget` varchar(50) COLLATE utf8_bin NOT NULL DEFAULT 'none',
  `heroId` int(11) NOT NULL DEFAULT '0',
  `gold` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `happensAt` (`happensAt`),
  KEY `launcherVillage` (`launcherVillage`),
  KEY `destinationVillage` (`destinationVillage`),
  KEY `estimatedTime` (`estimatedTime`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;
");

runEscapedQuery("
CREATE TABLE IF NOT EXISTS `wtfb2_guildinvitations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `recipientId` int(11) NOT NULL,
  `guildId` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `recipientId` (`recipientId`,`guildId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;
");

runEscapedQuery("
CREATE TABLE IF NOT EXISTS `wtfb2_guildpermissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `permission` varchar(20) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `userId` (`userId`,`permission`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;
");

runEscapedQuery("
CREATE TABLE IF NOT EXISTS `wtfb2_guilds` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `guildName` varchar(100) COLLATE utf8_bin NOT NULL,
  `profile` text COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`),
  KEY `guildName` (`guildName`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;
");

runEscapedQuery("
CREATE TABLE IF NOT EXISTS `wtfb2_heroes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ownerId` int(11) NOT NULL,
  `name` varchar(30) COLLATE utf8_bin NOT NULL,
  `defense` int(11) NOT NULL DEFAULT '0',
  `offense` int(11) NOT NULL DEFAULT '0',
  `inVillage` int(11) NOT NULL DEFAULT '0',
  `avatarLink` varchar(255) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`),
  KEY `ownerId` (`ownerId`),
  KEY `inVillage` (`inVillage`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;
");

runEscapedQuery("
CREATE TABLE IF NOT EXISTS `wtfb2_hitlog` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `accessDate` date NOT NULL,
  `page` varchar(60) COLLATE utf8_bin NOT NULL,
  `clientIP` varchar(200) COLLATE utf8_bin NOT NULL,
  `accessCount` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `accessDate` (`accessDate`,`page`,`clientIP`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=2 ;
");

runEscapedQuery("
CREATE TABLE IF NOT EXISTS `wtfb2_iplog` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `ip` varchar(60) COLLATE utf8_bin NOT NULL,
  `useCount` int(11) NOT NULL,
  `lastUsed` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `ip` (`ip`),
  KEY `userId` (`userId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;
");

runEscapedQuery("
CREATE TABLE IF NOT EXISTS `wtfb2_languages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `language` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=80 ;
");

runEscapedQuery("
INSERT INTO `wtfb2_languages` (`id`, `language`) VALUES
(1, 'аҧсуа бызшәа'),
(2, 'Afrikaans'),
(3, 'shqip'),
(4, 'አማርኛ'),
(5, 'العربية'),
(6, 'Հայերեն'),
(7, 'azərbaycan'),
(8, 'Bamanankan'),
(9, 'euskera'),
(10, 'Беларуская'),
(11, 'Български'),
(12, 'Català'),
(13, '中文'),
(14, '简体中文'),
(15, '繁體中文'),
(16, 'Hrvatski'),
(17, 'čeština'),
(18, 'Dansk'),
(19, 'Nederlands'),
(20, 'Eesti'),
(21, 'Ɛʋɛ'),
(22, 'suomi'),
(23, 'français'),
(24, 'français canadien'),
(25, 'Fulfulde, Pulaar, Pular'),
(26, 'Galego'),
(27, 'Deutsch'),
(28, 'Ελληνικά'),
(29, 'Hausa'),
(30, 'עברית'),
(31, 'हिंदी'),
(32, 'Magyar'),
(33, 'Íslenska'),
(34, 'Bahasa indonesia'),
(35, 'Gaeilge'),
(36, 'italiano'),
(37, '日本語'),
(38, 'ಕನ್ನಡ'),
(39, 'Қазақ'),
(40, 'Kinyarwanda'),
(41, 'Кыргыз'),
(42, 'Kirundi'),
(43, '한국어'),
(44, 'Latviešu'),
(45, 'Lietuviškai'),
(46, 'Dholuo'),
(47, 'Македонски'),
(48, 'Bahasa melayu'),
(49, 'Malti'),
(50, 'Norsk'),
(51, 'پښتو'),
(52, 'فارسی'),
(53, 'polski'),
(54, 'português'),
(55, 'português brasileiro'),
(56, 'Română'),
(57, 'Rumantsch'),
(58, 'Pyccĸий'),
(59, 'Srpski'),
(60, 'Српски'),
(61, 'Somali'),
(62, 'Español'),
(63, 'Slovenčina'),
(64, 'Slovenščina'),
(65, 'Kiswahili'),
(66, 'svenska'),
(67, 'తెలుగు'),
(68, 'ภาษาไทย'),
(69, 'Tϋrkçe'),
(70, 'Українська'),
(71, 'اردو'),
(72, 'o''zbek'),
(73, 'Tiếng Việt'),
(74, 'Cymraeg'),
(75, 'Wolof'),
(76, 'isiXhosa'),
(77, 'Yorùbá'),
(78, 'isiZulu'),
(79, 'English');");

runEscapedQuery("
CREATE TABLE IF NOT EXISTS `wtfb2_reports` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `recipientId` int(11) NOT NULL,
  `isPublic` tinyint(1) NOT NULL DEFAULT '0',
  `isHidden` tinyint(1) NOT NULL DEFAULT '0',
  `isRead` tinyint(1) NOT NULL DEFAULT '0',
  `title` varchar(100) COLLATE utf8_bin NOT NULL,
  `text` text CHARACTER SET utf8 NOT NULL,
  `reportTime` datetime NOT NULL,
  `reportType` enum('unknown','defensenoloss','defensewithloss','defensefail','attacknoloss','attackwithloss','attackfail','gotvillagebyconquer','lostvillagebyconquer','destroyedvillage','lostvillagebydestruct','adminmessage','incomingmove','outgoingmove') COLLATE utf8_bin NOT NULL,
  `token` varchar(40) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`),
  KEY `recipientId` (`recipientId`),
  KEY `isPublic` (`isPublic`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;
");

runEscapedQuery("
CREATE TABLE IF NOT EXISTS `wtfb2_requestlog` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `requestTime` datetime NOT NULL,
  `clientIP` varchar(20) COLLATE utf8_bin NOT NULL,
  `userId` int(11) NOT NULL,
  `requestedPage` varchar(100) COLLATE utf8_bin NOT NULL,
  `requestType` enum('normal','deputy','admin') COLLATE utf8_bin NOT NULL,
  `queryGet` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `queryPost` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `requestTime` (`requestTime`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;
");

runEscapedQuery("
CREATE TABLE IF NOT EXISTS `wtfb2_spokenlanguages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `playerId` int(11) NOT NULL,
  `languageId` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `playerId` (`playerId`,`languageId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;
");

runEscapedQuery("
CREATE TABLE IF NOT EXISTS `wtfb2_threadentries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `threadId` int(11) NOT NULL,
  `posterId` int(11) NOT NULL,
  `text` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `when` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `threadId` (`threadId`,`posterId`),
  KEY `posterId` (`posterId`),
  KEY `when` (`when`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;
");

runEscapedQuery("
CREATE TABLE IF NOT EXISTS `wtfb2_threadlinks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `threadId` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `read` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `threadId` (`threadId`,`userId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;
");

runEscapedQuery("
CREATE TABLE IF NOT EXISTS `wtfb2_threads` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `updated` datetime NOT NULL,
  `subject` varchar(100) COLLATE utf8_bin NOT NULL,
  `guildId` int(11) DEFAULT NULL,
  `lastPosterId` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `updated` (`updated`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;
");

runEscapedQuery("
CREATE TABLE IF NOT EXISTS `wtfb2_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `guildId` int(11) DEFAULT NULL,
  `userName` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `profile` text COLLATE utf8_bin NOT NULL,
  `regDate` datetime NOT NULL,
  `gold` double NOT NULL DEFAULT '500',
  `avatarLink` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `lastUpdate` datetime NOT NULL,
  `lastMassVillageUpdate` datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
  `expansionPoints` double NOT NULL DEFAULT '0',
  `refererId` int(11) NOT NULL,
  `goldProduction` double NOT NULL,
  `villageCount` int(11) NOT NULL,
  `totalScore` double NOT NULL,
  `needsTutorial` tinyint(1) NOT NULL DEFAULT '1',
  `notes` text COLLATE utf8_bin NOT NULL,
  `lastLoaded` datetime NOT NULL,
  `attackKills` double NOT NULL DEFAULT '0',
  `defenseKills` double NOT NULL DEFAULT '0',
  `willDeleteAt` datetime DEFAULT NULL,
  `masterAccess` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `userName_2` (`userName`),
  KEY `guildId` (`guildId`),
  KEY `refererId` (`refererId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;");

runEscapedQuery("
CREATE TABLE IF NOT EXISTS `wtfb2_villages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ownerId` int(11) DEFAULT NULL,
  `villageName` varchar(100) COLLATE utf8_bin NOT NULL,
  `x` int(11) NOT NULL,
  `y` int(11) NOT NULL,
  `buildPoints` double NOT NULL DEFAULT '5',
  `barracksLevel` int(11) NOT NULL DEFAULT '0',
  `archeryRangeLevel` int(11) NOT NULL DEFAULT '0',
  `stablesLevel` int(11) NOT NULL DEFAULT '0',
  `workshopLevel` int(11) NOT NULL DEFAULT '0',
  `townHallLevel` int(11) NOT NULL DEFAULT '0',
  `blacksmithLevel` int(11) NOT NULL DEFAULT '0',
  `goldmineLevel` int(11) NOT NULL DEFAULT '1',
  `wallLevel` int(11) NOT NULL DEFAULT '0',
  `spearmen` double NOT NULL DEFAULT '0',
  `archers` double NOT NULL DEFAULT '0',
  `knights` double NOT NULL DEFAULT '0',
  `catapults` double NOT NULL DEFAULT '0',
  `diplomats` double NOT NULL DEFAULT '0',
  `spearmanLevel` int(11) NOT NULL DEFAULT '0',
  `archerLevel` int(11) NOT NULL DEFAULT '0',
  `knightLevel` int(11) NOT NULL DEFAULT '0',
  `catapultLevel` int(11) NOT NULL DEFAULT '0',
  `spearmenTraining` double NOT NULL DEFAULT '0',
  `archersTraining` double NOT NULL DEFAULT '0',
  `knightsTraining` double NOT NULL DEFAULT '0',
  `catapultsTraining` double NOT NULL DEFAULT '0',
  `diplomatsTraining` double NOT NULL DEFAULT '0',
  `lastUpdate` datetime NOT NULL,
  `spareBuildPoints` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `x` (`x`,`y`),
  KEY `ownerId` (`ownerId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;");

runEscapedQuery("
CREATE TABLE IF NOT EXISTS `wtfb2_worldevents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `x` int(11) NOT NULL,
  `y` int(11) NOT NULL,
  `eventTime` datetime NOT NULL,
  `playerId` int(11) DEFAULT NULL,
  `guildId` int(11) DEFAULT NULL,
  `type` enum('settle','destroy','conquer','guildchange','rename','eventhappened','diplomacychanged','scorechanged','abandon','forcelogout') COLLATE utf8_bin NOT NULL,
  `recipientId` int(11) DEFAULT NULL,
  `recipientGuildId` int(11) DEFAULT NULL,
  `needFullRefresh` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `when` (`eventTime`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;
");

runEscapedQuery("
CREATE TABLE IF NOT EXISTS `wtfb2_worldupdate` (
  `lastHeroMove` datetime NOT NULL,
  `lastOracleTime` datetime NOT NULL,
  `lastStatGenerated` datetime NOT NULL,
  KEY `lastHeroMove` (`lastHeroMove`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
");

runEscapedQuery("INSERT INTO `wtfb2_worldupdate`(`lastHeroMove`, `lastOracleTime`, `lastStatGenerated`) VALUES ('1970-01-01 00:00:00', '1970-01-01 00:00:00', '1970-01-01 00:00:00')");

require_once('dbmigrate.php');

die('Okay, tables created, please review the settings in configuration.php, and please register yourself, then change your permissions to admin in the database if you wish.');


?>
