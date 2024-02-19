<?php

$language = isset($language) ? $language : 'en_US'; // Default to 'en_US' if $language is not set
// If the user has set a language preference, use that
if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
    // Extract the language and country part
    preg_match('/^([a-z]{2}_[A-Z]{2})/', str_replace('-', '_', $_SERVER['HTTP_ACCEPT_LANGUAGE']), $matches);
    if ($matches) {
        $language = $matches[1];
    }
}

putenv("LANG=".$language); 
setlocale(LC_ALL, $language);

$domain = "messages";
bindtextdomain($domain, BASE_DIR . "/locales");
bind_textdomain_codeset($domain, 'UTF-8');
textdomain($domain);
