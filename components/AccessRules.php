<?php

namespace app\components;
use Yii;
use yii\filters\AccessRule;



/**
 * Class AccessRules
 * @package members\components
 */
class AccessRules extends AccessRule
{
    public $token=null;

    /** @inheritdoc */
    protected function matchRole($user)
    {
        if (empty($this->roles)) {
            return true;
        }

        $allow = false;
        if(Yii::$app->cache->exists($this->token)){$allow = true;}

        foreach ($this->roles as $role) {
            if ($role === '?') { // Guest
                if (!$allow) {
                    return true;
                }
            }elseif ($role === '@') { //Simple user
                if ($allow) {
                    return true;
                }
            }
        }

        return false;
    }
}
