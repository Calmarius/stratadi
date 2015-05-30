<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

function apiTest($components, $post)
{
    var_dump($components);
    var_dump($post);

    return 0;
}

$rootApi = array(
    'test' => 'apiTest'
);

function invokeApiInner($apiSet, $components, $post)
{
    if (!isset($apiSet[$components[0]])) return FALSE;

    return $apiSet[$components[0]]($components, $post);

    return TRUE;
}

function invokeApi($uri, $post)
{
    global $rootApi;

    $components = array_values(array_filter(explode('/', $uri)));

    if ($components[0] != 'api') return FALSE;

    array_shift($components);

    invokeApiInner($rootApi, $components, $post);

    return TRUE;
}

invokeApi($_SERVER['REQUEST_URI'], $_POST);

?>


