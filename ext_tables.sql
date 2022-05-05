#
# Add field 'inactivemessage_tstamp' to table 'fe_users'
#
CREATE TABLE fe_users (
	inactivemessage_tstamp int(11) unsigned DEFAULT NULL,
	company_type varchar(255) DEFAULT '' NOT NULL,
	old_account int(11) unsigned DEFAULT '0' NOT NULL,
    saved_searches int(11) unsigned DEFAULT '0' NOT NULL,
    kitodo_document_access int(11) unsigned DEFAULT '0' NOT NULL,
    locale int(10) DEFAULT '0' NOT NULL,
    pw_changed_on_confirmation smallint(6) DEFAULT '0' NOT NULL,
    temp_user_ordering_party varchar(255) DEFAULT '' NOT NULL,
    temp_user_area_location text,
    temp_user_purpose text,
    district varchar(255) DEFAULT '' NOT NULL,
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
    download_pages int(11) unsigned DEFAULT '0' NOT NULL,
    download_work int(11) unsigned DEFAULT '0' NOT NULL,
    work_views int(11) unsigned DEFAULT '0' NOT NULL,

    PRIMARY KEY (uid),
    KEY document (document),
    KEY fe_user (fe_user)
);

CREATE TABLE tx_digasfemanagement_domain_model_access (
    dlf_document int(11) DEFAULT '0' NOT NULL,
    record_id varchar(255) DEFAULT '' NOT NULL,
    fe_user int(11) unsigned DEFAULT '0' NOT NULL,
    access_granted_notification int(11) DEFAULT '0' NOT NULL,
    expire_notification int(11) DEFAULT '0' NOT NULL,
    rejected smallint(6) DEFAULT '0' NOT NULL,
    rejected_reason text,
    inform_user smallint(6) DEFAULT '0' NOT NULL,
);
