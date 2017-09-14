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
        <p>Обязатальные поля:</p>
        <ol>
            <li>Артикул. Пользователь должен указать столбец в котором содержится артикулы товара.</li>
            <li>Цена. Пользователь должен указать столбец в котором содержится цена товара.</li>
            <li>Производитель. Пользователь должен указать производителя.</li>
        </ol>
        <p>Не обязатальные поля:</p>
        <ol>
            <li>Столбец. Пользователь может указать столбец в котором указана валюта каждого товара. Валидные валюты: <strong>UAH, USD, EUR, PLN</strong>. Также была добавлена "грн"</li>
            <li>Валюта. Пользователь может указать валюту. Эта валюта примениться ко всем найденым товарам в БД.</li>
            <li>Учитывать скидку. При указании этого флага, страя цена ( если она меньше новой) будет записана в поле compare_price.</li>
        </ol>
        <p>Поиск в базе данных осуществляеться по двум критериям: артикул и производитель. Количество найденных совпадений будет выведенно перед обновлением БД.</p>
        <h4 align="center"><strong>Логика установления валюты и цены</strong></h4>
        <ol>
            <li>Если пользователь не указывает столбец с валютой и валюту, то по умолчанию будет считаться что валюта UAH.</li>
            <li>Если пользователь указывает столбец в котором записан тип валюты то, для каждой строки екселя валюта будет подбираться индивидульно. Список валидных валют приведён выше.</li>
            <li>Если пользователь указывает валюту то, этот тип валюты применится ко всем найденым товарам.</li>
            <li>Если пользователь указывает валюту и столбец  с валютой то, это будет считаться ошибкой пользователя и обновить базу данных вы не сможете.</li>
            <li><strong>После обновления базы данных в стоимость товара запишеться цена которая была указана в экселе, а тип валюты будет установлен исходя из имеющихся данных.</strong></li>
        </ol>
    </div>
</div>

<script>

</script>
</body>
</html>
