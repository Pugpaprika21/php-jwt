<?php

namespace App\Foundation\Database;

trait QueryConnectionTrait
{
    /**
     * @param array $db
     * @param string $driver
     * @return string
     */
    private function getDriverDNS(array $db, string $driver = 'mysql'): string
    {
        return [
            'mysql' => "mysql:host={$GLOBALS['APP_ENV']['DB_HOST']};dbname={$GLOBALS['APP_ENV']['DB_NAME']};charset={$db['char_set']};port={$GLOBALS['APP_ENV']['DB_PORT']}",
            'sqlsrv' => "sqlsrv:Server={$GLOBALS['APP_ENV']['DB_HOST']},{$GLOBALS['APP_ENV']['DB_PORT']};Database={$GLOBALS['APP_ENV']['DB_NAME']}",
            'pgsql' => "pgsql:host={$GLOBALS['APP_ENV']['DB_HOST']};dbname={$GLOBALS['APP_ENV']['DB_NAME']};charset={$db['char_set']};port={$GLOBALS['APP_ENV']['DB_PORT']}",
            'oci' => "oci:dbname=(DESCRIPTION=(ADDRESS=(PROTOCOL=TCP)(HOST={$GLOBALS['APP_ENV']['DB_HOST']})(PORT={$GLOBALS['APP_ENV']['DB_PORT']}))(CONNECT_DATA=(SERVICE_NAME={$GLOBALS['APP_ENV']['DB_NAME']})));charset={$db['char_set']}"
        ][$driver];
    }
}
