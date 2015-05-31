<?php

require_once('shadow.php');

$DB_LINK = null;
$VAR_COUNTER = 0;

function ensureConnection()
{
    global $DB_LINK;

    if ($DB_LINK) return;

    $DB_LINK = mysqli_connect("127.0.0.1", __MYSQL_USER_NAME, __MYSQL_PASSWORD);
    if (!$DB_LINK) 
    {
        die('Failed to connect database server.');
    }
    if (!mysqli_select_db($DB_LINK, __MYSQL_DB_NAME))
    {
        die('Failed to select database.');    
    }

    mysqli_set_charset($DB_LINK, 'utf8') or die('Failed to set charset');
}

function logQuery($q, $time, $fn)
{
    $f = fopen($fn, "a");
    fwrite($f, date('Y-m-d H:i:s: ')."  $q\nLast $time seconds.\n\n");
    fclose($f);
}

function logLongQuery($q, $time)
{
    logQuery($q, $time, 'longqueries.txt');
}

function logFailedQuery($q, $time)
{
    logQuery($q, $time, 'failedqueries.txt');
}

function runQueryEx($query, $errp, $queryp = 'mysqli_real_query', $additonalInfo = '')
{
    global $DB_LINK;
    global $MYSQL_TRACE;
    $results = array();
    $index = 0;
    
    ensureConnection();
    
    if ($MYSQL_TRACE === true)
    {
        echo $query;
    }
    
    $start = microtime(TRUE);
    $r = $queryp($DB_LINK, $query);
    $last = microtime(TRUE) - $start;
    
    if ($last > 0.5)
    {
        logLongQuery($query, $last);
    }
    
    if ($r === FALSE)
    {
        logFailedQuery($query, $last);
        $errp(mysqli_error($DB_LINK) . "\n Your query was: " . $query . "Additional Info: " . $additonalInfo);
        return;
    }
    while ($result = mysqli_use_result($DB_LINK))
    {
        $results[$index] = array();
        while ($row = mysqli_fetch_assoc($result))
        {
            $results[$index][] = $row;
        }
        $index++;
        if (!mysqli_more_results($DB_LINK)) break;
        mysqli_next_result($DB_LINK);
    }
    
    return $results;
}

function doDie($str)
{
    die($str);
}

function runQuery($query, $errp = 'doDie', $additonalInfo = '')
{
    return runQueryEx($query, $errp, 'mysqli_real_query', $additonalInfo);
}

function getLastInsertId()
{
    global $DB_LINK;
    return mysqli_insert_id($DB_LINK);
}

function getAffectedRowCount()
{
    global $DB_LINK;
    return mysqli_affected_rows($DB_LINK);
}

function sqlvprintf($query, $args)
{
    global $DB_LINK;
    $ctr = 0;
    ensureConnection();
    $values = array();
    foreach ($args as $value)
    {
        if (is_string($value))
        {
            $value = "'" . mysqli_real_escape_string($DB_LINK, $value) . "'";
        }
        else if (is_null($value))
        {
            $value = 'NULL';
        }
        else if (!is_int($value) && !is_float($value))
        {
            die('Only numeric, string, array and NULL arguments allowed in a query. Argument '.($ctr+1).' is not a basic type, it\'s type is '. gettype($value). '.');
        }
        $values[] = $value;
        $ctr++;
    }
    $query = preg_replace_callback(
        '/{(\\d+)}/', 
        function($match) use ($values)
        {
            if (isset($values[$match[1]]))
            {
                return $values[$match[1]];
            }
            else
            {
                return $match[0];
            }
        },
        $query
    );
    return $query;
}

/*
function sqlprintf($query)
{
    return sqlvprintf($query, array_slice(func_get_args(), 1));
}
*/

function runEscapedQuery($preparedQuery /*, ...*/)
{
    $params = array_slice(func_get_args(), 1);
    $results = runQuery(sqlvprintf($preparedQuery, $params));    
    return $results;
}

function runPreparedQuery($preparedQuery /*, ...*/)
{
    global $DB_LINK;
    global $VAR_COUNTER;

    $params = array_slice(func_get_args(), 1);
    $preparedVars = array();
    runQuery("PREPARE pq FROM '$preparedQuery'");
    foreach ($params as $value)
    {
        if (is_string($value))
        {
            runQuery("SET @v$VAR_COUNTER = " . "'" . mysqli_real_escape_string($DB_LINK, $value) . "'");
        }
        else if (is_null($value))
        {
            runQuery("SET @v$VAR_COUNTER = NULL");
        }
        else
        {
            runQuery("SET @v$VAR_COUNTER = $value");
        }
        $preparedVars[] = "@v$VAR_COUNTER";
        $VAR_COUNTER++;
    }
    $execCmd = 'EXECUTE pq USING ' . implode(', ', $preparedVars);
    $results = runQuery($execCmd, $preparedQuery);
    runQuery("DEALLOCATE PREPARE pq");
    
    return $results;
}

function isEmptyResult($set)
{
    return !isset($set[0][0]);
}

?>
