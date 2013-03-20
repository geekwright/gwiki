#
# gwiki tables v1.0
#

CREATE TABLE gwiki_pages (
  gwiki_id int(10) NOT NULL AUTO_INCREMENT,
  keyword varchar(128) NOT NULL DEFAULT '',
  display_keyword varchar(255) NOT NULL DEFAULT '',
  title varchar(255) NOT NULL DEFAULT '',
  body text NOT NULL,
  parent_page varchar(128) NOT NULL DEFAULT '',
  page_set_home varchar(128) NOT NULL DEFAULT '',
  page_set_order int(4) NOT NULL DEFAULT '0',
  meta_description text NOT NULL,
  meta_keywords varchar(512) NOT NULL DEFAULT '',
  lastmodified int(10) NOT NULL DEFAULT '0',
  uid int(10) NOT NULL DEFAULT '0',
  admin_lock tinyint NOT NULL DEFAULT '0',
  active tinyint NOT NULL DEFAULT '0',
  search_body text NOT NULL,
  toc_cache text NOT NULL,
  show_in_index tinyint NOT NULL DEFAULT 1,
  gwiki_version int(4) NOT NULL DEFAULT '0',

  PRIMARY KEY (gwiki_id, active),
  KEY (active,keyword),
  KEY (keyword)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE gwiki_pageids (
  page_id int(10) NOT NULL AUTO_INCREMENT,
  keyword varchar(128) NOT NULL DEFAULT '',
  created int(10) NOT NULL DEFAULT '0',
  hit_count int(10) NOT NULL DEFAULT '0',

  PRIMARY KEY (page_id),
  UNIQUE KEY (keyword)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE gwiki_group_prefix(
  group_prefix_id int(10) NOT NULL auto_increment,
  group_id int(10) NOT NULL default '0',
  prefix_id int(10) NOT NULL default '0',

  PRIMARY KEY (group_prefix_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE gwiki_prefix(
  prefix_id int(10) NOT NULL auto_increment,
  prefix varchar(128) NOT NULL default '',
  prefix_home varchar(128) NOT NULL default '',
  prefix_auto_name tinyint(1) NOT NULL default '0',
  prefix_template_id int(10) NOT NULL default '0',
  prefix_is_external tinyint(1) NOT NULL default '0',
  prefix_external_url varchar(512) NOT NULL default '',

  PRIMARY KEY (prefix_id),
  UNIQUE KEY (prefix)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE gwiki_template(
  template_id int(10) NOT NULL auto_increment,
  template varchar(128) NOT NULL default '',
  template_body text NOT NULL,
  template_notes text NOT NULL,

  PRIMARY KEY (template_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE gwiki_page_images (
  image_id int(10) NOT NULL AUTO_INCREMENT,
  keyword varchar(128) NOT NULL DEFAULT '',
  image_name varchar(128) NOT NULL DEFAULT '',
  image_alt_text varchar(255) NOT NULL DEFAULT '',
  image_file varchar(255) NOT NULL DEFAULT '',
  use_to_represent int(1) NOT NULL DEFAULT '0',

  PRIMARY KEY (image_id),
  UNIQUE KEY (keyword, image_name)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE gwiki_page_files (
  file_id int(10) NOT NULL AUTO_INCREMENT,
  keyword varchar(128) NOT NULL DEFAULT '',
  file_name varchar(128) NOT NULL DEFAULT '',
  file_path varchar(255) NOT NULL DEFAULT '',
  file_type varchar(128) NOT NULL DEFAULT '',
  file_icon varchar(64) NOT NULL DEFAULT '',
  file_size int(10) NOT NULL DEFAULT '0',
  file_upload_date int(10) NOT NULL DEFAULT '0',
  file_description text,
  file_uid int(10) NOT NULL DEFAULT '0',

  PRIMARY KEY (file_id),
  UNIQUE KEY (keyword, file_name)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
