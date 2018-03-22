<?php

/**
 * Настройки приложения
 */

ini_set('log_errors', '1');

//sessions
ini_set('session.use_only_cookies', '1');
ini_set('session.name', 'PHPSESSID_mfw');
ini_set('session.cookie_lifetime', '0');
ini_set('session.use_cookies', '1');
ini_set('session.use_only_cookies', '1');
ini_set('session.use_strict_mode', '1');
ini_set('session.cookie_httponly', '1');
ini_set('session.use_trans_sid', '0');
ini_set('session.session.gc_maxlifetime', '300');
ini_set('session.referer_check', SESSION_REFERER_NAME);
ini_set('session.cache_limiter', "nocache");
ini_set('session.hash_function', "sha256");

