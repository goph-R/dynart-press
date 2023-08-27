<?php


$input = "select `#Person`.last_name as last_name, `#Person`.first_name as first_name, `#Person_Text`.translated as translated from `#Person` join `#Person_Text` on #Person_Text.text_id = #Person.id and #Person_Text.locale = '#Locale'";

function replaceClassNamesWithTableNames(string $query) {
    return preg_replace_callback(
        '/(\'[^\'#]*\')|(#[A-Za-z0-9_]+(?=[\s\n\r\.`]|$))/',
        function ($matches) {
            if ($matches[1]) {
                return $matches[1]; // Keep content within single quotes unchanged
            } else {
                return "dp_".strtolower(substr($matches[0], 1));
            }
        },
        $query
    );
}

echo replaceClassNamesWithTableNames($input);