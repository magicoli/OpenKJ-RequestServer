<?php

$language = empty($language) ? 'en_US' : $language;
putenv("LANG=".$language); 
setlocale(LC_ALL, $language);

$domain = "openkj";
bindtextdomain($domain, BASE_DIR . "/locale");
bind_textdomain_codeset($domain, 'UTF-8');
textdomain($domain);
