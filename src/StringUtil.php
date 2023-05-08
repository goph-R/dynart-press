<?php

namespace Dynart\Press;

class StringUtil {

    public static function replaceAccents(string $string) {
        return html_entity_decode(
            preg_replace('/&([a-zA-Z])(uml|acute|grave|circ|tilde|ring|slash);/', '$1',
                htmlentities($string, ENT_COMPAT, 'UTF-8')
            )
        );
    }

    public static function safeFilename($string) {
        return str_replace(' ', '-', preg_replace('/[^a-zA-Z0-9\s\-_\.]/', '', StringUtil::replaceAccents($string)));
    }
}