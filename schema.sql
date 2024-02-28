CREATE TABLE `translate_event_actions` (
  `translate_event_actions_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `event_id` int(10) NOT NULL COMMENT 'Post_ID of the translation_event post in the wp_posts table',
  `original_id` int(10) NOT NULL COMMENT 'ID of the translation',
  `user_id` int(10) NOT NULL COMMENT 'ID of the user who made the action',
  `action` enum('approve','create','reject','request_changes') NOT NULL COMMENT 'The action that the user made (create, reject, etc)',
  `locale` varchar(10) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL COMMENT 'Locale of the translation',
  `happened_at` datetime NOT NULL COMMENT 'When the action happened, in UTC',
  PRIMARY KEY (`translate_event_actions_id`),
  UNIQUE KEY `event_per_original_per_user` (`event_id`,`locale`,`original_id`,`user_id`)
) COMMENT='Tracks translation actions that happened during a translation event';
