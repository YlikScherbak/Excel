<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title id="Title">Excel</title>
    <link rel="stylesheet" href="{{ asset('/css/bootstrap.min.css') }}" media="screen"/>
    <link rel="stylesheet" href="{{ asset('/css/style.css') }}" media="screen"/>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <script src="{{ asset('/js/jquery-3.2.1.min.js') }}"></script>
    <script src="{{ asset('/js/bootstrap.js') }}" media="screen"></script>
</head>
<body>

@include('excel.navbar')

<div class="container">
    <div>
        <h3 align="center">Логика обработки екселя и обновления базы данных</h3>
        <h4 align="center"><strong>Форма ввода данных</strong></h4>
        <img src="{{ asset('/images/forma.jpg') }}" class="img-responsive"/>
        <p>Обязательные поля:</p>
        <ol>
            <li>Пользователь должен указать столбец в котором содержатся артикулы товаров.</li>
            <li>Пользователь должен указать столбец в котором содержатся цены товаров.</li>
            <li>Пользователь должен указать поставщика.</li>
        </ol>
        <p>Не обязательные поля:</p>
        <ol>
            <li>Пользователь может указать столбец в котором указана валюта товара. Валидные валюты: <strong>UAH, USD, EUR, PLN</strong>. Также была добавлена "грн"</li>
            <li>Пользователь может указать валюту. Эта валюта применяется ко всем найденым товарам в БД.</li>
            <li>При указании флага 'Учитывать скидку', страя цена ( если она меньше новой ) будет записана в поле compare_price.</li>
        </ol>
        <p>Поиск в базе данных осуществляеться по двум критериям: артикул и производитель. Количество найденных совпадений будет выведенно перед обновлением БД.</p>
        <h4 align="center"><strong>Логика установления валюты и цены</strong></h4>
        <ol>
            <li>Если пользователь не указывает столбец с валютой и валюту, то по умолчанию будет считаться что валюта UAH.</li>
            <li>Если пользователь указывает столбец в котором записан тип валюты то, для каждой строки файла excel валюта будет подбираться индивидульно. Список валидных валют приведён выше.</li>
            <li>Если пользователь указывает валюту то, этот тип валюты применится ко всем найденым товарам.</li>
            <li>Если пользователь указывает валюту и столбец  с валютой то, это будет считаться ошибкой пользователя и обновить базу данных вы не сможете.</li>
            <li>Надбавку нужно указывать как целое число. Если нужно 25% просто укажите целое число 25. Если же надбавка не указана, она будет считаться за 1.</li>
            <li><strong>После обновления базы данных в стоимость товара запишеться цена которая была указана в файле excel, а тип валюты будет установлен исходя из имеющихся данных.</strong></li>
        </ol>
    </div>
</div>

<script>

</script>
</body>
</html>
