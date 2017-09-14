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
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default" style="margin-top:45px">
                <div class="panel-heading">
                    <h3 class="panel-title">Результаты парсинга excel</h3>
                </div>
                <div class="panel-body">
                    @if(Session::has('message'))
                        <span class="help-block">
                                        <strong>{{ Session::get('message') }}</strong>
                                    </span>
                    @endif

                    <h2>Количество полей в excel: {{ $excelRow }}</h2>
                    <h2>Количество найденых совпадений в БД: {{ $tableRow }}</h2>
                    <h2>Производитель: {{ $manufacturer }}</h2>
                    <h2>Данные по валюте: {{ $currency }}</h2>
                    <div class="btn-group btn-group-justified">
                        <a href="{{ route('cancel_update') }}" class="btn btn-danger">Отменить</a>
                        @if(!$error)
                        <a href="{{ route('update_excel') }}" class="btn btn-primary">Обновить БД</a>
                        @endif
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>
<script>

</script>
</body>
</html>
