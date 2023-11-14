<?php

namespace backend\controllers;

use backend\models\Apple;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\Response;

class ApiController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => [
                            'actionGetApple',
                            'actionGetAllApples',
                            'actionCheckAll',
                            'actionEat',
                            'actionDelete',
                            'actionFall',
                            'actionGenerate',
                        ],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function __construct($id, $module, $config = [])
    {
        parent::__construct($id, $module, $config);
        \Yii::$app->layout = null;
        \Yii::$app->response->format = Response::FORMAT_JSON;
    }

    protected function apple2Array(Apple $apple): array
    {
        return [
            'id' => $apple->getId(),
            'setDate' => $apple->getSetDate()->format('d.m.Y H:i:s'),
            'color' => ['name' => $apple->getColor()->getColorName(), 'rgb' => $apple->getColor()->getRgb()],
            'status' => $apple->getStatus()->getStatusName(),
            'eatenPercent' => $apple->getEatenPercent()
        ];
    }
    public function actionGetApple(int $appleId): string
    {
        $apple = Apple::findOne($appleId);
        if (!$apple) {
            throw new \Exception('Яблоко с таким ID не найдено');
        }

        $result = $this->apple2Array($apple);

        return $this->render('success-with-data.php', ['result' => $result]);
    }

    public function actionGetAllApples(): string
    {
        $apples = Apple::find()->onCondition(['deleted' => 0])->orderBy(['set_date' => 'desc'])->all();
        $result = [];
        foreach ($apples as $apple) {
            $result[] = $this->apple2Array($apple);
        }

        return $this->render('success-with-data.php', ['result' => $result]);
    }

    public function actionCheckAll(): string
    {
        $apples = Apple::findAll(['status_id' =>Apple::STATUS_ON_GROUND]);
        foreach ($apples as $apple) {
            $apple->check();
        }

        return $this->render('success');
    }

    public function actionEat(int $appleId, int $percent): string
    {
        $apple = Apple::findOne($appleId);
        if (!$apple) {
            throw new \Exception('Яблоко с таким ID не найдено');
        }

        $apple->eat($percent);

        $result = [
            'eatenPercent' => $apple->getEatenPercent(),
        ];

        return $this->render('success-with-data.php', ['result' => $result]);
    }

    public function actionDelete(int $appleId): string
    {
        $apple = Apple::findOne($appleId);
        if (!$apple) {
            throw new \Exception('Яблоко с таким ID не найдено');
        }

        $apple->delete();

        return $this->render('success');
    }

    public function actionFall(int $appleId): string
    {
        $apple = Apple::findOne($appleId);
        if (!$apple) {
            throw new \Exception('Яблоко с таким ID не найдено');
        }

        $apple->fall();

        return $this->render('success');
    }

    public function actionGenerate(): string
    {
        $result = ['number' => Apple::generate()];

        return $this->render('success-with-data.php', ['result' => $result]);
    }

}