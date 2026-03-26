<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
| Firebase Cloud Messaging (FCM)
| -------------------------------------------------------------------------
| Supports:
| 1) HTTP v1 (recommended): service-account based OAuth token flow
| 2) Legacy HTTP: server-key based
|
| For HTTP v1 you need a service account JSON file.
| Get it from Firebase Console → Project Settings → Service Accounts →
| "Generate new private key" → JSON download.
|
| Put that JSON file at the path below (or set env vars).
*/
$config['fcm_use_http_v1'] = getenv('FCM_USE_HTTP_V1') !== false
    ? filter_var(getenv('FCM_USE_HTTP_V1'), FILTER_VALIDATE_BOOLEAN)
    : true; // default ON

// Legacy fallback off by default because server key might be unavailable.
$config['fcm_enable_legacy_fallback'] = getenv('FCM_ENABLE_LEGACY_FALLBACK') !== false
    ? filter_var(getenv('FCM_ENABLE_LEGACY_FALLBACK'), FILTER_VALIDATE_BOOLEAN)
    : false;

$config['fcm_server_key'] = getenv('FCM_SERVER_KEY') ? (string)getenv('FCM_SERVER_KEY') : '';

// HTTP v1 settings
$config['fcm_project_id'] = getenv('FCM_PROJECT_ID') ? (string)getenv('FCM_PROJECT_ID') : '';
$config['fcm_service_account_path'] = getenv('FCM_SERVICE_ACCOUNT_PATH')
    ? (string)getenv('FCM_SERVICE_ACCOUNT_PATH')
    : APPPATH . 'config/fcm-service-account.json';

$config['fcm_android_channel_id'] = getenv('FCM_ANDROID_CHANNEL_ID')
    ? (string)getenv('FCM_ANDROID_CHANNEL_ID')
    : 'default';

$config['fcm_scope'] = 'https://www.googleapis.com/auth/firebase.messaging';
