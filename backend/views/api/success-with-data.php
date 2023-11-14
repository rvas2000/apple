<?php
// Сообщение об успешном выполнении с результатом

/** @var  $result mixed */

echo \yii\helpers\Json::encode([
    'status' => 'OK',
    'data' => $result
]);

