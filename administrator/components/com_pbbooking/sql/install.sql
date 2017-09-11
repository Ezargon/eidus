#
# Encoding: Unicode (UTF-8)
#


DROP TABLE IF EXISTS `#__pbbooking_block_days`;
DROP TABLE IF EXISTS `#__pbbooking_cals`;
DROP TABLE IF EXISTS `#__pbbooking_config`;
DROP TABLE IF EXISTS `#__pbbooking_customfields`;
DROP TABLE IF EXISTS `#__pbbooking_customfields_data`;
DROP TABLE IF EXISTS `#__pbbooking_events`;
DROP TABLE IF EXISTS `#__pbbooking_treatments`;
DROP TABLE IF EXISTS `#__pbbooking_logs`;
DROP TABLE IF EXISTS `#__pbbooking_surveys`;
DROP TABLE IF EXISTS `#__pbbooking_sync`;
DROP TABLE IF EXISTS `#__pbbooking_lang_override`;
DROP TABLE IF EXISTS `#__pbbooking_emails`;
DROP TABLE IF EXISTS `#__pbbooking_smss`;


CREATE TABLE if not exists `#__pbbooking_cals` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(80) NOT NULL,
  `hours` text,
  `email` varchar(128) DEFAULT NULL,
  `enable_google_cal` tinyint(1) DEFAULT 0,
  `gcal_id` VARCHAR(256) DEFAULT NULL,
  `color` VARCHAR(16) DEFAULT '#339933',
  `languages` text default null,
  `ordering` int(11) default 0,
  `groupbookings` tinyint(1) default 0,
  `groupclass_max` int(11) default null,
  `calendar_schedule` text default null,
  `article_id` int(11) default null,
  `image_url` varchar(256) default null,
  PRIMARY KEY (`id`)
) DEFAULT CHARACTER SET utf8;

CREATE TABLE if not exists `#__pbbooking_block_days` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `block_start_date` datetime,
  `block_end_date` datetime,
  `block_note` varchar(255) DEFAULT NULL,
  `calendars` varchar(255) DEFAULT NULL,
  `r_int` int(11) DEFAULT '0',
  `r_freq` varchar(128) DEFAULT NULL,
  `r_end` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) DEFAULT CHARACTER SET utf8;



CREATE TABLE `#__pbbooking_config` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `trading_hours` text,
  `show_link` int(1) DEFAULT NULL,
  `time_groupings` text,
  `time_increment` int(11) DEFAULT '30',
  `show_prices` tinyint(1) DEFAULT '1',
  `bcc_admin` tinyint(1) DEFAULT '1',
  `validation` varchar(128) DEFAULT 'client',
  `calendar_start_day` tinyint(1) NOT NULL DEFAULT '0',
  `show_busy_frontend` tinyint(1) NOT NULL DEFAULT '0',
  `enable_logging` tinyint(1) NOT NULL DEFAULT '0',
  `manage_fields` text,
  `enable_shifts` tinyint(1) DEFAULT '1',
  `currency_symbol_before` tinyint(1) DEFAULT '1',
  `paypal_currency` varchar(10) DEFAULT 'AUD',
  `paypal_test` tinyint(1) DEFAULT '0',
  `notification_email` varchar(256) DEFAULT NULL,
  `multi_page_checkout` tinyint(1) DEFAULT '0',
  `enable_cron` tinyint(1) DEFAULT '0',
  `enable_reminders` tinyint(1) DEFAULT '0',
  `reminder_settings` text,
  `single_page_block_days_master_trading_hours` tinyint(1) DEFAULT '0',
  `enable_testimonials` tinyint(1) DEFAULT '0',
  `testimonial_days_after` int(11) DEFAULT '0',
  `testimonial_questions` text,
  `disable_announcements` tinyint(1) DEFAULT '0',
  `self_service_change_notice` int(11) DEFAULT '48',
  `enable_selfservice` tinyint(1) DEFAULT '0',
  `enable_google_cal` tinyint(1) DEFAULT '0',
  `display_past_appointments` tinyint(1) DEFAULT '0',
  `prevent_bookings_within` int(11) DEFAULT '60',
  `disable_pending_bookings` tinyint(1) DEFAULT '0',
  `show_busy_front_end` tinyint(1) DEFAULT '0',
  `select_calendar_individual` tinyint(1) DEFAULT '0',
  `enable_multilanguage` tinyint(1) DEFAULT '0',
  `multilangmessages` text,
  `calendar_color` varchar(11) DEFAULT '#5F0044',
  `allow_booking_max_days_in_advance` int(11) DEFAULT '0',
  `color` varchar(11) DEFAULT '#5F0044',
  `user_offset` tinyint(1) DEFAULT '0',
  `booking_details_template` text,
  `enable_recaptcha` tinyint(1) DEFAULT '0',
  `sync_future_events` tinyint(1) DEFAULT '0',
  `sync_google_events_to_pbbooking` tinyint(1) DEFAULT '0',
  `paypal_api_username` varchar(256) DEFAULT NULL,
  `paypal_api_password` varchar(256) DEFAULT NULL,
  `paypal_api_signature` varchar(512) DEFAULT NULL,
  `google_cal_sync_secret` varchar(80) DEFAULT NULL,
  `google_max_results` int(11) DEFAULT '25',
  `authcode` varchar(256) DEFAULT NULL,
  `token` text,
  `validation_link_expiry` tinyint(1) default 0,
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8;


INSERT INTO `#__pbbooking_config` (`id`, `trading_hours`, `show_link`, `time_groupings`, `time_increment`, `show_prices`, `bcc_admin`, `validation`, `calendar_start_day`, `show_busy_frontend`, `enable_logging`, `manage_fields`, `enable_shifts`, `currency_symbol_before`, `paypal_currency`, `paypal_test`, `notification_email`, `multi_page_checkout`, `enable_cron`, `enable_reminders`, `reminder_settings`, `single_page_block_days_master_trading_hours`, `enable_testimonials`, `testimonial_days_after`, `testimonial_questions`, `disable_announcements`, `self_service_change_notice`, `enable_selfservice`, `enable_google_cal`, `display_past_appointments`, `prevent_bookings_within`, `disable_pending_bookings`, `show_busy_front_end`, `select_calendar_individual`, `enable_multilanguage`, `multilangmessages`, `calendar_color`, `allow_booking_max_days_in_advance`, `color`, `user_offset`, `booking_details_template`, `enable_recaptcha`, `sync_future_events`, `sync_google_events_to_pbbooking`, `paypal_api_username`, `paypal_api_password`, `paypal_api_signature`, `google_cal_sync_secret`, `google_max_results`, `authcode`, `token`) VALUES
	(1,'[{"status":"open","open_time":"1000","close_time":"2000"},{"status":"open","open_time":"1000","close_time":"2000"},{"status":"open","open_time":"1000","close_time":"2000"},{"status":"open","open_time":"1000","close_time":"2000"},{"status":"open","open_time":"1000","close_time":"2000"},{"status":"open","open_time":"0900","close_time":"1700"},{"status":"closed"}]',1,'{"morning":{"shift_start":"1000","shift_end":"1200","display_label":"morning"},"afternoon":{"shift_start":"1330","shift_end":"1700","display_label":"afternoon"},"evening":{"shift_start":"1700","shift_end":"1930","display_label":"evening"}}',30,1,0,'client',0,1,0,NULL,1,1,'AUD',0,'',0,0,1,'{"reminder_days_in_advance":"1"}',0,1,1,'[{"testimonial_field_values":"Yes=Yes|No=No","testimonial_field_type":"select","testimonial_field_varname":"like_it","testimonial_field_label":"Did you like it?"},{"testimonial_field_values":"Yes=Yes|No=No","testimonial_field_type":"radio","testimonial_field_varname":"come_again","testimonial_field_label":"Would you come again?"},{"testimonial_field_values":"","testimonial_field_type":"text","testimonial_field_varname":"email_address","testimonial_field_label":"What is your email address"}]',0,12,0,0,0,60,0,0,0,0,NULL,'#5f0044',0,'#5F0044',0,'<p><table><tr><th>{{COM_PBBOOKING_SUCCESS_DATE}}</th><td>{{dstart}}</td></tr><tr><th>{{COM_PBBOOKING_SUCCESS_TIME}}</th><td>{{dtstart}}</td></tr><tr><th>{{COM_PBBOOKING_BOOKINGTYPE}}</th><td>{{service.name}}</td></tr></table></p>',0,0,0,'','','','4e916d6c03a7',25,'',NULL);


CREATE TABLE if not exists `#__pbbooking_customfields` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `fieldname` varchar(80) DEFAULT NULL,
  `fieldtype` varchar(80) DEFAULT NULL,
  `varname` varchar(80) DEFAULT NULL,
  `size` int(11) DEFAULT NULL,
  `is_email` tinyint(1) DEFAULT NULL,
  `is_required` tinyint(1) DEFAULT NULL,
  `is_first_name` tinyint(1) DEFAULT '0',
  `is_last_name` tinyint(1) DEFAULT '0',
  `values` varchar(255) DEFAULT NULL,
  `ordering` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) DEFAULT CHARACTER SET utf8;


CREATE TABLE if not exists `#__pbbooking_customfields_data` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `customfield_id` int(11) DEFAULT NULL,
  `pending_id` int(11) DEFAULT NULL,
  `data` varchar(256) DEFAULT NULL,
  `is_email` tinyint(1) DEFAULT NULL,
  `is_required` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) DEFAULT CHARACTER SET utf8;


CREATE TABLE if not exists `#__pbbooking_events` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cal_id` int(11) NOT NULL,
  `summary` text NOT NULL,
  `dtend` datetime DEFAULT NULL,
  `dtstart` datetime DEFAULT NULL,
  `description` text NOT NULL,
  `uid` varchar(80) DEFAULT NULL,
  `service_id` int(11) DEFAULT '0',
  `r_int` int(11) DEFAULT NULL,
  `r_freq` VARCHAR(255) DEFAULT NULL,
  `r_end` datetime DEFAULT NULL,
  `customfields_data` text,
  `email` varchar(128) DEFAULT NULL,
  `deposit_paid` tinyint(1) DEFAULT '0',
  `amount_paid` decimal(10,2) DEFAULT '0.00',
  `reminder_sent` tinyint(1) DEFAULT 0,
  `testimonial_request_sent` tinyint(1) DEFAULT 0,
  `gcal_id` varchar(256) DEFAULT NULL,
  `user_offset` int(11) DEFAULT 0,
  `verified` tinyint(1) DEFAULT 0,
  `validation_token` varchar(256) default null,
  `parent` int(11) DEFAULT 0,
  `remote_ip` varchar(45) default null,
  `date_created` datetime default null,
  `externalevent` tinyint(1) default 0,
  `deleted` tinyint(1) default 0,
  `payment_provider` varchar(16) default null,
  PRIMARY KEY (`id`)
) DEFAULT CHARACTER SET utf8;


CREATE TABLE if not exists `#__pbbooking_treatments` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `duration` int(11) NOT NULL,
  `price` decimal(19,4) NOT NULL DEFAULT '0.00',
  `calendar` varchar(128) DEFAULT '0',
  `require_payment` tinyint(1) DEFAULT 0,
  `ordering` int(11) default 0,
  `is_variable` tinyint(1) DEFAULT 0,
  `min_duration` int(11) DEFAULT NULL,
  `pointscost` int(11) default null,
  `article_id` int(11) default null,
  PRIMARY KEY (`id`)
) DEFAULT CHARACTER SET utf8;

CREATE TABLE if not exists `#__pbbooking_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `datetime` datetime DEFAULT NULL,
  `component` varchar(128) DEFAULT NULL,
  `message` text,
  PRIMARY KEY (`id`)
) DEFAULT CHARACTER SET utf8;

CREATE TABLE if not exists `#__pbbooking_surveys` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `event_id` int(11) DEFAULT '0',
  `date_submitted` datetime DEFAULT NULL,
  `submission_ip` varchar(128) DEFAULT NULL,
  `publish` tinyint(1) DEFAULT '0',
  `content` text,
  PRIMARY KEY (`id`)
) DEFAULT CHARACTER SET utf8;

CREATE TABLE if not exists `#__pbbooking_sync` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date_added` datetime DEFAULT NULL,
  `action` varchar(10) DEFAULT NULL,
  `data` text,
  `status` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
);

CREATE TABLE `#__pbbooking_lang_override` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `original_id` int(11) DEFAULT '0',
  `messagename` varchar(128) DEFAULT NULL,
  `type` varchar(20) DEFAULT NULL,
  `langtag` varchar(10) DEFAULT NULL,
  `data` text,
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8;

INSERT INTO `#__pbbooking_cals` (`id`, `name`, `hours`) VALUES (1, 'Massage Therapist 1', '[{"status":"open","open_time":"1000","close_time":"2000"},{"status":"open","open_time":"1000","close_time":"2000"},{"status":"open","open_time":"1000","close_time":"2000"},{"status":"open","open_time":"1000","close_time":"2000"},{"status":"open","open_time":"1000","close_time":"2000"},{"status":"open","open_time":"0900","close_time":"1700"},{"status":"closed"}]'), (2, 'Beauty Therapist 1', '[{"status":"closed"},{"status":"closed"},{"status":"closed"},{"status":"open","open_time":"1000","close_time":"2000"},{"status":"open","open_time":"1000","close_time":"2000"},{"status":"open","open_time":"0900","close_time":"1700"},{"status":"closed"}]'), (3, 'Naturopath 1', '[{"status":"open","open_time":"1000","close_time":"2000"},{"status":"open","open_time":"1000","close_time":"2000"},{"status":"open","open_time":"1000","close_time":"2000"},{"status":"open","open_time":"1000","close_time":"2000"},{"status":"open","open_time":"1000","close_time":"2000"},{"status":"open","open_time":"0900","close_time":"1700"},{"status":"closed"}]'), (4, 'Acupuncturist 1', '[{"status":"open","open_time":"1000","close_time":"2000"},{"status":"open","open_time":"1000","close_time":"2000"},{"status":"open","open_time":"1000","close_time":"2000"},{"status":"open","open_time":"1000","close_time":"2000"},{"status":"open","open_time":"1000","close_time":"2000"},{"status":"open","open_time":"0900","close_time":"1700"},{"status":"closed"}]');



INSERT INTO `#__pbbooking_customfields` (`id`, `fieldname`, `fieldtype`, `varname`, `size`, `is_email`, `is_required`, `is_first_name`, `is_last_name`, `values`) VALUES (1, 'First Name', 'text', 'firstname', 60, NULL, 1, 1, 0, NULL), (2, 'Last Name', 'text', 'lastname', 60, NULL, 1, 0, 1, NULL), (3, 'Email', 'text', 'email', 60, 1, 1, 0, 0, NULL), (4, 'Mobile', 'text', 'mobile', 60, NULL, 1, 0, 0, NULL), (5, 'Gender', 'radio', 'gender', 60, 0, 1, 0, 0, 'Male|Female');


INSERT INTO `#__pbbooking_treatments` (`id`, `name`, `duration`, `price`, `calendar`) VALUES (1, '30 minute relaxaton massage take 2', 30, 40, '1,2'), (2, '60 minute relexation massage', 60, 60, '1,2'), (3, '30 minute remedial massage', 30, 40, '1'), (4, '60 minute remedial massage', 60, 70, '1'), (5, 'Express Facial', 30, 60, '2');






CREATE TABLE `#__pbbooking_emails` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email_tag` varchar(64) DEFAULT NULL,
  `description` text,
  `emailbody` text,
  `emailsubject` varchar(512) DEFAULT NULL,
  `subscriber` tinyint(1) DEFAULT '0',
  `uses_twig` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8;


CREATE TABLE `#__pbbooking_smss` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sms_tag` varchar(128) DEFAULT NULL,
  `sms` text,
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8;



INSERT INTO `#__pbbooking_emails` (`id`, `email_tag`, `description`, `emailbody`, `emailsubject`, `subscriber`, `uses_twig`) VALUES
	(1,'client_validation_email','This is the main email sent to the client on client validation workflows.','<p>Hi |*firstname*| |*lastname*|</p>\r\n<p>Thank you for choosing us for your next treatment. Please click the below link to validate your appointment with us.</p>\r\n<p>|*URL*|</p>\r\n<p>You\\\'re booking details are</p>\r\n<p>|*booking_details*|</p>','Your Treatment Verification',0,0),
	(2,'auto_validated_appt_email','This email is sent to the client when using Auto Validation','<p>Hi |*firstname*| |*lastname*|</p>\r\n\r\n<p><strong>This is your auto validated appt confirmation</strong></p>\r\n\r\n\r\n<p>Thank you for choosing us for your next treatment.</p>\r\n\r\n\r\n\r\n<p>You\\\'re booking details are</p><p>|*booking_details*|</p>','Your Auto Validated Appt Confirmation',1,0),
	(3,'admin_validation_pending_email','This email is set to the client when an Admin Validated booking is in a PENDING state.	','<p>Hi |*firstname*| |*lastname*|</p>\r\n\r\n<p><strong>This is your admin validation pending appt confirmation</strong></p>\r\n\r\n\r\n<p>Thank you for choosing us for your next treatment.</p>\r\n\r\n\r\n\r\n<p>You\\\'re booking details are</p><p>|*booking_details*|</p>','Your Admin Validation Pending',1,0),
	(4,'admin_validation_confirmed','This email is sent to the client when an Admin Validated appointment has been CONFIRMED.	','<p>Hi |*firstname*| |*lastname*|</p>\r\n\r\n<p><strong>This is your admin validation confirmed appt confirmation</strong></p>\r\n\r\n\r\n<p>Thank you for choosing us for your next treatment.</p>\r\n\r\n\r\n\r\n<p>You\\\'re booking details are</p><p>|*booking_details*|</p>','Your Admin Validation Confirmed',1,0),
	(5,'admin_payment_confirmed_email','This email is sent to the admin when a paid booking has been confirmed.  ie. paid.		','<p>Hi Admin</p>\r\n\r\n<p>There is a new paid booking in the calendar.</p>\r\n','Admin Paid Booking Confirmation',1,0),
	(6,'client_payment_confirmed_email','This email is sent to the client when a booking has been paid for.	','<p>Hi |*firstname*| |*lastname*|</p>\r\n\r\n<p><strong>This is your client paid booking confirmation.</strong></p>\r\n\r\n\r\n<p>Thank you for choosing us for your next treatment.</p>\r\n\r\n\r\n\r\n<p>You\'re booking details are</p><p>|*booking_details*|</p>','Your Paid Booking Confirmation',1,0),
	(7,'reminder_email','This email is sent to the client when a reminder is emailed.','Hi, This is a reminder.\r\n\r\nYour booking details are:\r\n\r\n|*booking_details*|','This is a reminder email',0,0),
	(8,'survey_email','This email is sent to request a survey.','<p>\r\n  Hi,\r\n</p>\r\n\r\n<p>We\'d love it if you\'d take a short survey about your recent experience.</p>\r\n\r\n<p>\r\n  |*URL*|\r\n</p>','Please take a short survey.',0,0),
	(9,'admin_notification','This email is sent to the admin when a new appointment is validated','<p>There is a new validated appointment in the diary. Details are below:</p> <p>|*booking_details*|</p>','New Validated Appointment',0,0),
	(10,'admin_created_appt','This email is sent to the client when an ADMIN creates an appointment for them','<p>Hi |*firstname*| |*lastname*|</p> <p>Thank you for choosing us for your next treatment</p><p>You\\\'re booking details are</p> <p>|*booking_details*|</p>','Your New Appointment',1,0),
	(11,'admin_deleted_appt','This email is sent to the client when an ADMIN deletes an appointment for them','<p>Hi |*firstname*| |*lastname*|</p> \r\n<p>An admin has deleted your appointment with us.</p> \r\n','Admin Deleted Appointment',1,0),
	(12,'admin_modified_appt','This email is sent to the client when an ADMIN modifies an appointment for them','<p>Hi |*firstname*| |*lastname*|</p> \r\n\r\n<p>An admin has modified your appointment time with us.</p> \r\n<p>You\\\'re booking details are</p> <p>|*booking_details*|</p>','Admin Modified Appointment',1,0);



INSERT INTO `#__pbbooking_smss` (`id`, `sms_tag`, `sms`) VALUES
	(1,'on_event_created','Hi |*firstname*|, thank you for booking your next appointment at our clinic.  Your appointment requires validation and a link has been sent to your email account.  The clinic'),
	(2,'on_event_validation','Hi |*firstname*|, thank you for booking your appointment at our clinic.  Your appointment is now validated.  We look forward to seeing you on |*dtstart*| for your |*service*|.  The clinic'),
	(3,'on_event_reminder','Hi |*firstname*|, thank you for booking your appointment at our clinic.  We look forward to seeing you on |*dtstart*| for your |*service*|.  The clinic');