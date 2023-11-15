<?php

namespace console\controllers;

use backend\models\Apple;
use common\models\User;
use yii\console\Controller;
use yii\console\ExitCode;

class AppleController extends Controller
{
    public function actionIndex(): int
    {
        $apples = Apple::find()->all();
        foreach ($apples as $apple) {
            $this->stdout(sprintf("id = %s: %s; %s\r\n", $apple->getId(), $apple->getStatus()->getStatusName(), $apple->getColor()->getColorName()));
        }
        return ExitCode::OK;
    }

    public function actionCheckAll(): int
    {
        $apples = Apple::findAll(['status_id' =>Apple::STATUS_ON_GROUND]);
        foreach ($apples as $apple) {
            $apple->check();
        }
        return ExitCode::OK;
    }

    public function actionEat(int $appleId, int $percent): int
    {
        $apple = Apple::findOne($appleId);
        $apple->eat($percent);
        return ExitCode::OK;
    }

    public function actionDelete(int $appleId): int
    {
        $apple = Apple::findOne($appleId);
        $apple->delete();
        return ExitCode::OK;
    }

    public function actionFall(int $appleId): int
    {
        $apple = Apple::findOne($appleId);
        $apple->fall();
        return ExitCode::OK;
    }


    public function actionGenerate(): void
    {
        $n = Apple::generate();
        echo sprintf("На дереве созрело %s новых яблок\r\n", $n);
    }

    public function actionManageUser(): int
    {
        $f = fopen('php://stdin', 'r');
        $this->stdout('Логин: '); $login = trim(fgets($f));
        $this->stdout('Пароль: '); $password = trim(fgets($f));
        $this->stdout('e-mail: '); $email = trim(fgets($f));
        fclose($f);

        if (empty($login) || empty($password)) {
            $this->stderr('Не заданы имя пользователя или пароль');
            return ExitCode::DATAERR;
        }

        $msg = sprintf("Изменены данные пользователя %s", $login);
        $user = User::findOne(['username' => $login]);
        if (!$user) {
            $user = new User();
            $user->username = $login;
            $user->auth_key = uniqid();
            $msg = sprintf("Создан новый пользователь %s", $login);
        }

        $user->email = $email;
        $user->status = User::STATUS_ACTIVE;
        $user->setPassword($password);
        $user->save();

        $this->stdout($msg);
        return ExitCode::OK;
    }
}