<?php
$input = ['a', 'b', 'c'];

$joined = ejoin('|', $input);
assert($joined === 'a|b|c');

$splitted = esplit('|', $joined, false);
assert($splitted === $input);

////////////////

$input = ['a', 'b|c', 'd\\'];
$joined = ejoin('|', $input, '|\\');
assert($joined === 'a|b\|c|d\\\\');

$splitted = esplit('|', $joined, false);
assert($splitted === $input);

////////////////

$input = ['|', '||', '|||'];
$joined = ejoin('|', $input, '|');
assert($joined === '\||\|\||\|\|\|');

$splitted = esplit('|', $joined, false);
assert($splitted === $input);

////////////////

$input = ['\\', '\\\\', '\\\\\\'];
$joined = ejoin('|', $input);
assert($joined === '\\\\|\\\\\\\\|\\\\\\\\\\\\');

$splitted = esplit('|', $joined);
assert($splitted === $input);

////////////////

$input = ['\\|', 'aaa'];
$joined = ejoin('|', $input, '\\|');
assert($joined === '\\\\\\||aaa');

$splitted = esplit('|', $joined, false);
assert($splitted === $input);

////////////////

$joined = 'a|b\b|c';
$splitted = esplit('|', $joined);

//PHP Warning:  Unexpected escape sequence: \b
assert($splitted === false);

$joined = 'a|b|c\\';
$splitted = esplit('|', $joined);
//PHP Warning:  Unexpected end of escape sequence
assert($splitted === false);



function ejoin($separator, $array, $escape = '\\')
{
    $array = array_map(
        function (string $item) use ($escape) {
            return addcslashes($item, $escape);
        },
        $array
    );

    $result = implode($separator, $array);

    return $result;
}


function esplit($separator, $string, $escape = '\\')
{
    if (preg_match('/\\\b/', $string)) {
        trigger_error('Unexpected escape sequence: \b', E_USER_WARNING);
        return false;
    }

    if (preg_match('/[A-Za-z]\\\\$/', $string)) {
        trigger_error('Unexpected end of escape sequence', E_USER_WARNING);
        return false;
    }

    if ($escape) {
        $array = explode($separator, $string);
    } else {
        $string = preg_replace(['/\|/', '/\\\_/'], ['_', $separator], $string);
        $array = explode('_', $string);
    }

    $array = array_map('stripcslashes', $array);

    return $array;
}
