<?php

$language = empty($language) ? 'en_US' : $language;
putenv("LANG=".$language); 
setlocale(LC_ALL, $language);

$domain = "messages";
bindtextdomain($domain, BASE_DIR . "/locales");
bind_textdomain_codeset($domain, 'UTF-8');
textdomain($domain);
