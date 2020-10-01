#
# Add field 'inactivemessage_tstamp' to table 'fe_users'
#
CREATE TABLE fe_users (
	inactivemessage_tstamp int(11) unsigned DEFAULT NULL,
	company_type int(11) unsigned DEFAULT '0' NOT NULL,
	old_account int(11) unsigned DEFAULT '0' NOT NULL,
    kitodo_feuser_access text,
    saved_searches int(11) unsigned DEFAULT '0' NOT NULL,
);

#
# Table structure for table 'tx_digasfemanagement_domain_model_search'
#
CREATE TABLE tx_digasfemanagement_domain_model_search (
    title varchar(255) DEFAULT '' NOT NULL,
    search_params text,
    fe_user int(11) unsigned DEFAULT '0' NOT NULL,
);

--
-- Table structure for table 'tx_digasfemanagement_domain_model_statistic'
--
CREATE TABLE tx_digasfemanagement_domain_model_statistic (
    uid int(11) NOT NULL auto_increment,
    pid int(11) DEFAULT '0' NOT NULL,
    tstamp int(11) DEFAULT '0' NOT NULL,
    crdate int(11) DEFAULT '0' NOT NULL,
    deleted smallint(6) DEFAULT '0' NOT NULL,
    hidden smallint(6) DEFAULT '0' NOT NULL,
    document int(11) unsigned NOT NULL,
    fe_user int(11) unsigned NOT NULL,

    PRIMARY KEY (uid),
    KEY document (document),
    KEY fe_user (fe_user)
);
