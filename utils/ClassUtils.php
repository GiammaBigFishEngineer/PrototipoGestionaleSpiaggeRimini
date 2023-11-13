<?php

function class_to_array($class, $properties)
{
    $values = [];
    foreach ($properties as $prop) {
        $values[$prop] = $class->$prop;
    }

    return $values;
}

function non_null_properties($class, $properties): array
{
    $props = [];
    foreach ($properties as $prop) {
        if ($class->$prop !== NULL) $props[] = $prop;
    }

    return $props;
}
