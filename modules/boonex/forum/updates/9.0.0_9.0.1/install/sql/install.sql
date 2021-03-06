SET @sName = 'bx_forum';


-- TABLES
CREATE TABLE IF NOT EXISTS `bx_forum_subscribers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `initiator` int(11) NOT NULL,
  `content` int(11) NOT NULL,
  `added` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `initiator` (`initiator`,`content`),
  KEY `content` (`content`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `bx_forum_votes` (
  `object_id` int(11) NOT NULL default '0',
  `count` int(11) NOT NULL default '0',
  `sum` int(11) NOT NULL default '0',
  UNIQUE KEY `object_id` (`object_id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `bx_forum_votes_track` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `object_id` int(11) NOT NULL default '0',
  `author_id` int(11) NOT NULL default '0',
  `author_nip` int(11) unsigned NOT NULL default '0',
  `value` tinyint(4) NOT NULL default '0',
  `date` int(11) NOT NULL default '0',
  PRIMARY KEY (`id`),
  KEY `vote` (`object_id`, `author_nip`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `bx_forum_favorites_track` (
  `object_id` int(11) NOT NULL default '0',
  `author_id` int(11) NOT NULL default '0',
  `date` int(11) NOT NULL default '0',
  KEY `id` (`object_id`,`author_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


-- STUDIO PAGE & WIDGET
UPDATE `sys_std_pages` SET `icon`='bx_forum@modules/boonex/forum/|std-icon.svg' WHERE `name`=@sName;
UPDATE `sys_std_widgets` SET `icon`='bx_forum@modules/boonex/forum/|std-icon.svg' WHERE `module`=@sName AND `caption`='_bx_forum';