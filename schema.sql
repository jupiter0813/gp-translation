# This file is only used in development environments, and to serve as documentation.
# It is not used in production in any way.

create or replace table wp_wporg_gp_translation_events_actions
(
    event_id       int(10)     not null comment 'ID of the event',
    translation_id int(10)     not null comment 'ID of the translation',
    user_id        int(10)     not null comment 'ID of the user who made the action',
    action         varchar(16) not null comment 'The action that the user made (create, reject, etc)',
    locale         varchar(10) not null comment 'Locale of the translation',
    # Make sure that for a given event and translation, the user cannot do multiple actions.
    primary key (event_id, translation_id, user_id)
)
    comment 'Tracks translation actions that happened during a translation event';
