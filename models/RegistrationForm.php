<?php
namespace app\models;

use Faker\Provider\zh_CN\DateTime;
use yii\base\Model;
use app\models\User;
/**
 * New User RegistrationForm model
 *
 * Class RegistrationForm
 * @property string $fname
 * @property string $email
 * @property string $password
 * @property string $birth
 * @property string $hobbies
 */
class RegistrationForm extends Model
{
    public $name;
    public $fname;
    public $email;
    public $password;
    public $birth;
    public $hobbies;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['email', 'password', 'fname',  'hobbies'], 'required'],
            [['fname'], 'string', 'max' => 500],
            ['birth', 'dateFormat'],
            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            ['email', 'unique', 'targetClass' => '\app\models\User', 'message' => 'This email address has already been taken.'],
            ['password', 'string', 'min' => 6],

        ];
    }

    public function dateFormat($attribute, $params)
    {
        if (!$this->hasErrors()) {
            try {
                $date= new \DateTime($this->birth);
                $this->birth = (string) $date->getTimestamp();
            }
            catch( \Exception $e ) {
                $this->addError($attribute, 'Incorrect date format.');
            }
        }

    }

    /**
     * Create new User with STATUS_NOTACTIVE
     *
     * @return \app\models\User
     */

    public function GenerateUser()
    {
            $user = new User;
            $user->username = $this->email;
            $user->email = $this->email;
            $user->status = User::STATUS_ACTIVE;
            $user->generateAuthKey();

        return $user;
    }

}