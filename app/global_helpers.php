<?php

function dq($query)
{
    $sql = $query->toSql();
    foreach ($query->getBindings() as $key => $binding) {
        $sql = preg_replace('/\?/', "'$binding'", $sql, 1);
    }
    dd($sql);
}

function array_pop_n(array &$arr, $n)
{
    return array_splice($arr, 0, $n);
}

function string_from_camel($string): string
{
    return preg_replace('/([a-z0-9])([A-Z])/', '$1 $2', $string);
}

function audio_file_length_seconds($file)
{
    $dur = shell_exec(sprintf('ffmpeg -i %s 2>&1', $file));

    if (preg_match('/: Invalid /', $dur)) {
        return false;
    }

    preg_match('/Duration: (.{2}):(.{2}):(.{2})/', $dur, $duration);

    if ( ! isset($duration[1])) {
        return false;
    }

    $hours = $duration[1];

    $minutes = $duration[2];

    $seconds = $duration[3];

    return $seconds + ($minutes * 60) + ($hours * 60 * 60);
}

function percent(int $dividend, int $divisor, bool $maxAt100 = true, int $decimalPlaces = 0)
{
    $val = $divisor ? (float) round(($dividend / $divisor) * 100, $decimalPlaces) : null;

    if ($maxAt100) {
        return $val > 100 ? 100 : $val;
    }

    return $val;
}

function lq($query)
{
    $sql = $query->toSql();
    foreach ($query->getBindings() as $key => $binding) {
        $sql = preg_replace('/\?/', "'$binding'", $sql, 1);
    }
    logger($sql);
}

function mq($queryWithinClosure)
{
    DB::connection('mongodb')->enableQueryLog();

    $queryWithinClosure();

    return DB::connection('mongodb')->getQueryLog();
}

function dmq($queryWithinClosure)
{
    dd(mq($queryWithinClosure));
}

function lmq($queryWithinClosure)
{
    logger(mq($queryWithinClosure));
}

function is_odd($n)
{
    return (bool) ($n % 2);
}

function is_even($n)
{
    if (is_odd($n)) {
        return false;
    }

    return true;
}

function is_between(int $low, int $high, int $num): bool
{
    return $num >= $low && $num <= $high;
}

function arrays_are_identical(array $array1, array $array2): bool
{
    $remaining1 = array_diff($array1, $array2);
    $remaining2 = array_diff($array2, $array1);

    return 0 === count($remaining1) && 0 === count($remaining2);
}

function loggerLocal($data, array $extraEnvs = [])
{
    if (in_array(env('APP_ENV'), ['local', ...$extraEnvs])) {
        logger($data);
    }
}

function string_to_snake_case(string $string): string
{
    return strtolower(preg_replace('/(?<!_)(?<!^)(?<![A-Z])[A-Z]/', '_$0', preg_replace('/[\s.-]+/', '_', $string)));
}

function remove_new_lines(string $input): string
{
    return trim(preg_replace('/\s\s+/', '', $input));
}

function string_to_title_case(string $string): string
{
    return ucwords(preg_replace('/(?<! )(?<!^)(?<![A-Z])[A-Z]/', ' $0', preg_replace('/[._-]+/', ' ', $string)));
}
