<?php

namespace backend\controllers;

use backend\models\Apple;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Json;
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

    protected function success(): array
    {
        return ['status' => 'OK'];
    }

    protected function successWithData($result): array
    {
        return ['status' => 'OK', 'data' => $result];
    }

    public function actionGetApple(int $appleId): array
    {
        $apple = Apple::findOne($appleId);
        if (!$apple) {
            throw new \Exception('Яблоко с таким ID не найдено');
        }

        $result = $this->apple2Array($apple);

        return $this->successWithData($result);
    }

    public function actionGetAllApples(): array
    {
        $apples = Apple::find()->onCondition(['deleted' => 0])->orderBy(['set_date' => 'desc'])->all();
        $result = [];
        foreach ($apples as $apple) {
            $result[] = $this->apple2Array($apple);
        }

        return $this->successWithData($result);
    }

    public function actionCheckAll(): array
    {
        $apples = Apple::findAll(['status_id' =>Apple::STATUS_ON_GROUND]);
        foreach ($apples as $apple) {
            $apple->check();
        }

        return $this->success();
    }

    public function actionEat(int $appleId, int $percent): array
    {
        $apple = Apple::findOne($appleId);
        if (!$apple) {
            throw new \Exception('Яблоко с таким ID не найдено');
        }

        $apple->eat($percent);

        $result = [
            'eatenPercent' => $apple->getEatenPercent(),
        ];

        return $this->successWithData($result);
    }

    public function actionDelete(int $appleId): array
    {
        $apple = Apple::findOne($appleId);
        if (!$apple) {
            throw new \Exception('Яблоко с таким ID не найдено');
        }

        $apple->delete();

        return $this->success();
    }

    public function actionFall(int $appleId): array
    {
        $apple = Apple::findOne($appleId);
        if (!$apple) {
            throw new \Exception('Яблоко с таким ID не найдено');
        }

        $apple->fall();

        return $this->success();
    }

    public function actionGenerate(): array
    {
        $result = ['number' => Apple::generate()];

        return $this->successWithData($result);
    }

}