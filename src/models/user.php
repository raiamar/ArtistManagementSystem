<?php
require_once __DIR__.'/../../config/db.php';
require_once __DIR__.'/../helper.php';

class UserHandler
{
    public static function list(int $page, int $perPage = 1): array{
         return paginate('users','isActive = TRUE',[],$perPage,$page);
        $a = paginate('users','isActive = TRUE',[],$perPage,$page);

        echo"<pre>";
        print_r($a);
        die;
    }
}