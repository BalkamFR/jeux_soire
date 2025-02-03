jeux_soiree_user
    .id
    .unique_key_user
    .score_user
    .name_user
    .email_user
    .avatar_user 
    .password_user
    .created_at
    .updated_at

jeux_soiree_name_game
    .id
    .name_game

jeux_soiree_player
    .id
    .unique_key_user
    .name_player
    .score_player

jeux_soiree_score
    .id
    .unique_key_user
    .name_player
    .score_player
    .score_game

jeux_soiree_alcool_drink
    .id
    .unique_key_alcool_drink
    .name_alcool_drink
    .alcohol_percentage  
    .created_at
    .updated_at

jeux_soiree_game_session
    .id
    .game_id
    .name_game
    .unique_key_user
    .place_game_max
    .place_game_current
    .status              // en cours, terminé, etc.
    .start_time
    .end_time
    .created_at


jeux_soiree_undercover
    .id
    .user_create_id
    .word_undercover
    .word_dif
    .word_theme
    .created_at
    .updated_at


jeux_soiree_undercover_game
    .id
    .unique_key_user
    .word_undercover
    .name_player
    .status
    .created_at
    .updated_at
