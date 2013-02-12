#
# Table structure for table `wikimod`
#

CREATE TABLE wikimod (
  id int(10) NOT NULL auto_increment,
  keyword varchar(255) NOT NULL default '',
  title varchar(255) NOT NULL default '',
  body text NOT NULL default '',
  lastmodified datetime NOT NULL default '0000-00-00 00:00:00',
  u_id int(10) NOT NULL default '0',
  PRIMARY KEY (id)
) ENGINE=MyISAM;
