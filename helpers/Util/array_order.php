<?php

function real_array_order($order, $array)
{
    foreach($order as $key) yield $array[$key];
}

function array_order($order, $array)
{
    return array_values(iterator_to_array(real_array_order($order, $array)));
}