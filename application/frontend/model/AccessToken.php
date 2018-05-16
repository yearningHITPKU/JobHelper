<?php

namespace app\frontend\model;

use think\Model;

class AccessToken extends Model
{
    protected $pk = 'id';
    protected $table = 'access_token';
}
