<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * SMTP for sendemail() helper. Used when getenv('SMTP_HOST') is empty
 * (typical on shared hosting where .env is not loaded into PHP).
 *
 * Fill in smtp_* and mail_from_* on the live server (or set SMTP_* in the web server env).
 * For Gmail: App Password required (Google Account → Security → 2-Step → App passwords).
 */
$config['smtp_host'] = '';
$config['smtp_port'] = '587';
$config['smtp_user'] = '';
$config['smtp_pass'] = '';
$config['smtp_crypto'] = 'tls';
$config['smtp_timeout'] = '30';
$config['mail_from_email'] = '';
$config['mail_from_name'] = '';
/** Set to 'smtp' to force SMTP even if smtp_host were empty (not recommended). */
$config['mail_protocol'] = '';
