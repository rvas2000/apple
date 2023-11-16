$(document).ready(function () {

    const AJAX_ERROR_UNSUPPORTED_FORMAT = 'Неверный формат данных';

    /**
     * Показ сообщения об ошибке
     * @param message
     */
    let showError = function (message) {
        // В реальной ситуации сделать что-нибудь более наглядное
        alert(message);
    };

    /**
     * Парсинг результата AJAX-запроса
     * @param response - ответ AJAX
     * @param withData - признак "Ответ должен содержать данные"
     * @param callback - функция выполняемая в случае усешного получения данных
     */
    let parseResponse = function (response, withData, callback) {
        if ('status' in response) {
            if (response['status'] === 'OK') {
                if (withData) {
                    if ('data' in response && callback !== undefined) {
                        callback(response['data']);
                    } else {
                        showError(AJAX_ERROR_UNSUPPORTED_FORMAT);
                    }
                }
            } else {
                if ('message' in response) {
                    showError(response['message']);
                } else {
                    showError(AJAX_ERROR_UNSUPPORTED_FORMAT);
                }
            }
        } else {
            showError(AJAX_ERROR_UNSUPPORTED_FORMAT);
        }
    };

    /**
     * Контейнер для размещения яблок
     * @param containerId
     * @constructor
     */
    let AppleContainer = function(containerId) {
        this.containerId = containerId;
    };

    /**
     * Метод контейнера "Очистить"
     */
    AppleContainer.prototype.clear = function () {
        $('#' + this.containerId).empty();
    }

    /**
     * Метод контейнера "Положить яблоко"
     * @param apple
     */
    AppleContainer.prototype.placeApple = function (apple) {
        // верстка изображения яблока (в зависимости от статуса)
       let html;
       let color;
        switch (apple.status.id) {
            case 3:
                // Гнилое
                color = '#9B390FFF';
                html = '<div class="apple">'
                    + '	<div class="apple-body">'
                    + '		<div class="apple-caption" style="color: white">Гнилое!</div>'
                    + '	</div>'
                    + '	<a href="api/delete?appleId=' + apple.id + '">Удалить</a>'
                    + '</div>';
                break;
            case 2:
                // В корзине
                color = '#' + apple.color.rgb;
                html = '<div class="apple">'
                    + '	<div class="apple-body">'
                    + '		<div class="apple-caption">Съедено на ' + apple.eatenPercent + '%</div>'
                    + '		<form class="apple-form" action="api/eat">'
                    + '			<input type="hidden" name="appleId" value="' + apple.id + '">'
                    + '			Съесть еще <input name="percent" type="number" value="100" style="width: 50px;">%?'
                    + '			<button>Да</button>'
                    + '		</form>'
                    + '	</div>'
                    + '	<a href="api/delete?appleId=' + apple.id + '">Удалить</a>'
                    + '</div>';
                break;
            default:
                // На дереве
                color = '#' + apple.color.rgb;
                html = '<div class="apple">'
                    + '	<div class="apple-body">'
                    + '	</div>'
                    + '	<a href="api/delete?appleId=' + apple.id + '">Удалить</a>'
                    + '	<a href="api/fall?appleId=' + apple.id + '">Сбосить</a>'
                    + '</div>';
        }

        let d = $(html);

        // Устанавливаем стиль в зависимости от параметров ябока
        d.find('.apple-body').css('background-color', color);

        // Кладем яблоко в контейнер
        $('#' + this.containerId).append(d);

    };


    // Создаем контейнеры для размещения яблок
    let applesTree = new AppleContainer('apples-tree'); // яблоня
    let applesBasket = new AppleContainer('apples-basket'); // корзина

    /**
     * callback для запроса get-all-apples
     * @param data
     */
    let showApples = function(data) {
        data.forEach(function (apple) {
            if (apple.status.id === 1 ) {
                // если статус "На дереве", то вешаем яблоко на дерево
                applesTree.placeApple(apple);
            } else {
                // если статус другой, то кладем яблоко в корзину
                applesBasket.placeApple(apple);
            }
        });
    };

    /**
     * Инициализация игрового поля
     */
    let init = function() {
        // Получаем массив всех неудаленных ябок с сервера и размещаем их на дереве и в корзине
        $('div.apple-wait-loading').show();
        $.ajax('api/get-all-apples', {
            method: 'get',
            success: function (data) {
                // Очищаем дерео и корзину
                applesTree.clear();
                applesBasket.clear();
                // раскладываем полученные яблоки
                parseResponse(data, true, showApples);
                $('div.apple-wait-loading').hide();
            }
        });
    };

    // Навешиваем обработчики
    $(document).delegate('div.apple-body', 'mouseenter', function (evnt) {
        $(evnt.target).find('form.apple-form').show();
    });

    $(document).delegate('div.apple-body', 'mouseleave', function (evnt) {
        if (evnt.target === 'div.apple') {
            $(evnt.target).find('form.apple-form').hide();
        } else {
            $(evnt.target).closest('div.apple-body').find('form.apple-form').hide();
        }
    });

    // Обработчики ссылок - выполняется ajax
    $(document).delegate('div.body-content a', 'click', function(evnt) {
        let url = $(evnt.target).closest('a').attr('href');
        $.ajax(url, {
            method: 'get',
            success: function (data) {
                parseResponse(data, false);
                init();
            }
        });
        return false;
    });

    // Обработчики отправки форм по ajax
    $(document).delegate('div.apple form', 'submit', function(evnt) {
        let form = $(evnt.target);
        let parameters = form.serializeArray();
        let url = form.attr('action');
        $.ajax(url, {
            method: 'get',
            data: parameters,
            success: function (data) {
                parseResponse(data, false);
                init();
            }
        });
        return false;
    });

    init();



});
