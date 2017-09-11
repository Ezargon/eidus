


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


alter table `#__pbbooking_config` add column (
  `validation_link_expiry` tinyint(1) default 0
);

INSERT INTO `#__pbbooking_emails` (`id`, `email_tag`, `description`, `emailbody`, `emailsubject`, `subscriber`, `uses_twig`) VALUES
	(1,'client_validation_email','This is the main email sent to the client on client validation workflows.','<p>Hi |*firstname*| |*lastname*|</p>\r\n<p>Thank you for choosing us for your next treatment. Please click the below link to validate your appointment with us.</p>\r\n<p>|*URL*|</p>\r\n<p>Your booking details are</p>\r\n<p>|*booking_details*|</p>','Your Treatment Verification',0,0),
	(2,'auto_validated_appt_email','This email is sent to the client when using Auto Validation','<p>Hi |*firstname*| |*lastname*|</p>\r\n\r\n<p><strong>This is your auto validated appt confirmation</strong></p>\r\n\r\n\r\n<p>Thank you for choosing us for your next treatment.</p>\r\n\r\n\r\n\r\n<p>Your booking details are</p><p>|*booking_details*|</p>','Your Auto Validated Appt Confirmation',1,0),
	(3,'admin_validation_pending_email','This email is set to the client when an Admin Validated booking is in a PENDING state.	','<p>Hi |*firstname*| |*lastname*|</p>\r\n\r\n<p><strong>This is your admin validation pending appt confirmation</strong></p>\r\n\r\n\r\n<p>Thank you for choosing us for your next treatment.</p>\r\n\r\n\r\n\r\n<p>Your booking details are</p><p>|*booking_details*|</p>','Your Admin Validation Pending',1,0),
	(4,'admin_validation_confirmed','This email is sent to the client when an Admin Validated appointment has been CONFIRMED.	','<p>Hi |*firstname*| |*lastname*|</p>\r\n\r\n<p><strong>This is your admin validation confirmed appt confirmation</strong></p>\r\n\r\n\r\n<p>Thank you for choosing us for your next treatment.</p>\r\n\r\n\r\n\r\n<p>Youre booking details are</p><p>|*booking_details*|</p>','Your Admin Validation Confirmed',1,0),
	(5,'admin_payment_confirmed_email','This email is sent to the admin when a paid booking has been confirmed.  ie. paid.		','<p>Hi Admin</p>\r\n\r\n<p>There is a new paid booking in the calendar.</p>\r\n','Admin Paid Booking Confirmation',1,0),
	(6,'client_payment_confirmed_email','This email is sent to the client when a booking has been paid for.	','<p>Hi |*firstname*| |*lastname*|</p>\r\n\r\n<p><strong>This is your client paid booking confirmation.</strong></p>\r\n\r\n\r\n<p>Thank you for choosing us for your next treatment.</p>\r\n\r\n\r\n\r\n<p>Your booking details are</p><p>|*booking_details*|</p>','Your Paid Booking Confirmation',1,0),
	(7,'reminder_email','This email is sent to the client when a reminder is emailed.','Hi, This is a reminder.\r\n\r\nYour booking details are:\r\n\r\n|*booking_details*|','This is a reminder email',0,0),
	(8,'survey_email','This email is sent to request a survey.','<p>\r\n  Hi,\r\n</p>\r\n\r\n<p>We would love it if you would take a short survey about your recent experience.</p>\r\n\r\n<p>\r\n  |*URL*|\r\n</p>','Please take a short survey.',0,0),
	(9,'admin_notification','This email is sent to the admin when a new appointment is validated','<p>There is a new validated appointment in the diary. Details are below:</p> <p>|*booking_details*|</p>','New Validated Appointment',0,0),
	(10,'admin_created_appt','This email is sent to the client when an ADMIN creates an appointment for them','<p>Hi |*firstname*| |*lastname*|</p> <p>Thank you for choosing us for your next treatment</p><p>Your booking details are</p> <p>|*booking_details*|</p>','Your New Appointment',1,0),
	(11,'admin_deleted_appt','This email is sent to the client when an ADMIN deletes an appointment for them','<p>Hi |*firstname*| |*lastname*|</p> \r\n<p>An admin has deleted your appointment with us.</p> \r\n','Admin Deleted Appointment',1,0),
	(12,'admin_modified_appt','This email is sent to the client when an ADMIN modifies an appointment for them','<p>Hi |*firstname*| |*lastname*|</p> \r\n\r\n<p>An admin has modified your appointment time with us.</p> \r\n<p>Your booking details are</p> <p>|*booking_details*|</p>','Admin Modified Appointment',1,0);


alter table `#__pbbooking_events` add column (
	`payment_provider` varchar(16) default null
);

alter table `#__pbbooking_treatments` add column (`pointscost` int(11) default null);

alter table `#__pbbooking_treatments` add column (`article_id` int(11) default null);
alter table `#__pbbooking_cals` add column(
	`article_id` int(11) default null,
	`image_url` varchar(256) default null
);

CREATE TABLE `#__pbbooking_smss` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sms_tag` varchar(128) DEFAULT NULL,
  `sms` text,
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8;


INSERT INTO `#__pbbooking_smss` (`id`, `sms_tag`, `sms`) VALUES
	(1,'on_event_created','Hi |*firstname*|, thank you for booking your next appointment at our clinic.  Your appointment requires validation and a link has been sent to your email account.  The clinic'),
	(2,'on_event_validation','Hi |*firstname*|, thank you for booking your appointment at our clinic.  Your appointment is now validated.  We look forward to seeing you on |*dtstart*| for your |*service*|.  The clinic'),
	(3,'on_event_reminder','Hi |*firstname*|, thank you for booking your appointment at our clinic.  We look forward to seeing you on |*dtstart*| for your |*service*|.  The clinic');
	
	
	
ALTER TABLE `#__pbbooking_config` DROP COLUMN `email_body`;

ALTER TABLE `#__pbbooking_config` DROP COLUMN `email_subject`;

ALTER TABLE `#__pbbooking_config` DROP COLUMN `auto_validated_appt_body`;

ALTER TABLE `#__pbbooking_config` DROP COLUMN `auto_validated_appt_email_subject`;

ALTER TABLE `#__pbbooking_config` DROP COLUMN `admin_validation_pending_email_subject`;

ALTER TABLE `#__pbbooking_config` DROP COLUMN `admin_validation_pending_email_body`;

ALTER TABLE `#__pbbooking_config` DROP COLUMN `admin_validation_confirmed_email_subject`;

ALTER TABLE `#__pbbooking_config` DROP COLUMN `admin_validation_confirmed_email_body`;

ALTER TABLE `#__pbbooking_config` DROP COLUMN `admin_paypal_confirm`;

ALTER TABLE `#__pbbooking_config` DROP COLUMN `admin_paypal_confirm_subject`;

ALTER TABLE `#__pbbooking_config` DROP COLUMN `client_paypal_confirm_subject`;

ALTER TABLE `#__pbbooking_config` DROP COLUMN `client_paypal_confirm`;

ALTER TABLE `#__pbbooking_config` DROP COLUMN `reminder_email_body` ;

ALTER TABLE `#__pbbooking_config` DROP COLUMN `reminder_email_subject`;

ALTER TABLE `#__pbbooking_config` DROP COLUMN `testimonial_email_subject`;

ALTER TABLE `#__pbbooking_config` DROP COLUMN `testimonial_email_body`;

ALTER TABLE `#__pbbooking_config` DROP COLUMN `enable_firephp`;

ALTER TABLE `#__pbbooking_config` DROP COLUMN `admin_pending_cancel_subject`;

ALTER TABLE `#__pbbooking_config` DROP COLUMN `admin_pending_cancel_body`;

