<?php

/**
 * Simple method for extracting a large array of arguments passed to a method.
 *
 * @param array|stdClass $args
 * @param array|stdClass $defaults
 * @return array
 */
function extract_args (&$args, $defaults)
{
    if (!is_array($args) && !is_object($args)) {
        return $args = $defaults;
    }

    // Overwrite the default key with the given one
    foreach ($defaults as $key => $val) {
        if (is_array($args)) {
            if (isset($args[$key]))
                $defaults[$key] = $args[$key];
        } elseif (is_object($args)) {
            if (isset($args->$key))
                $defaults[$key] = $args->$key;
        }
    }
    // Pass any additional arguments as well, though might not be used
    foreach ($args as $key => $val) {
        if (!array_key_exists($key, $defaults))
            $defaults[$key] = $val;
    }
    // Change the return value to an object if the input args were an object.
    if (is_object($args)) {
        $return = new stdClass();
        foreach ($defaults as $key => $val) {
            $return->$key = $val;
        }
        return $args = $return;
    }

    return $args = $defaults;
}