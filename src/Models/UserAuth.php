<?php
namespace Taketwo\Models;

class UserAuth extends \Eloquent
{
    protected $table = 'user_auth';

    /**
     * 对应关系
     */
    public function user()
    {
        return $this->belongsTo('User');
    }
}
