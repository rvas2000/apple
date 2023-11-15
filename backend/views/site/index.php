<?php

/** @var yii\web\View $this */

$this->title = 'Играем в яблоки';
?>
<div class="site-index">

    <div class="jumbotron text-center bg-transparent">
        <h1 class="display-4">Играем в яблоки</h1>

        <p class="lead">Тестовое задание</p>

    </div>

    <div class="body-content">
        <h2>Это яблоня</h2>
        <div class="apple-container" id="apples-tree"></div>
        <h2>Это корзина под яблоней</h2>
        <div class="apple-container" id="apples-basket"></div>

        <menu class="apple-group-operations">
            <ul>
                <li>
                    <a href="api/generate">Вырастить еще яблок</a>
                </li>
                <li>
                    <a href="api/check-all">Перебрать яблоки в корзине</a>
                </li>
            </ul>

        </menu>
    </div>
</div>
