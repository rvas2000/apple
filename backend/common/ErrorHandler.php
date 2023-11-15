<?php

namespace backend\common;

use yii\web\Response;

class ErrorHandler extends \yii\web\ErrorHandler
{


    /**
     * @param $exception
     * @return void
     */
    protected function renderException($exception)
    {
        if (
            \Yii::$app->has('response')
            && ($response = \Yii::$app->getResponse())->format === Response::FORMAT_JSON
        ) {
            $response->isSent = false;
            $response->stream = null;
            $response->data = null;
            $response->content = null;

            // Для нашего API всегда выставляем код ответа 200! Ошибка анализируется полем status в ответе
            $response->setStatusCode(200);

            $response->data = [
                'status' => 'ERR',
                'message' => $exception->getMessage(),
            ];

            $response->send();
            return;
        }

        parent::renderException($exception);
    }
}