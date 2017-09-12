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
        @if (count($errors) > 0)
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form class="form-inline" method="post" action="{{ route('update_excel') }}">
            {{ csrf_field() }}
            <div class="form-group">
                <label for="articul">Артикул:</label>
                <input type="number" name="articul" class="form-control" id="articul" autofocus required min="0"
                       max="{{ session('columns') -1 }}">
            </div>
            <div class="form-group">
                <label for="price">Цена:</label>
                <input type="number" name="price" class="form-control" id="price" autofocus required min="0"
                       max="{{ session('columns') -1 }}">
            </div>
            <div class="form-control">
                <label for="manufacturer">Производитель:</label>
                <select name="manufacturer" id="manufacturer">
                    @foreach($manufacturer as $man)
                        <option value="{{ $man->manufacturer }}">{{ $man->manufacturer }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-control">
                <label for="currency">Валюта:</label>
                <select name="currency" id="currency">
                    <option value=""></option>
                    @foreach($currency as $cur)
                        <option value="{{ $cur->code }}">{{ $cur->code }}</option>
                    @endforeach
                </select>
            </div>
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="old" value="true"> Учитывать скидку
                </label>
            </div>
            <button type="submit" class="btn btn-default">Отправить</button>
        </form>
    </div>
</div>

<div class="container">
    <div class="row">
        <div>
            <table class="table table-striped table-hover table-bordered">
                <thead id="head">
                <tr>
                    @for($i = 0; $i < session('columns'); $i++)
                        <td>{{ $i}}</td>
                    @endfor
                </tr>
                </thead>
                <tbody id="excel_body">
                @foreach($data as $excel)
                    <tr>
                        @for($i = 0; $i < count($excel); $i++)
                            <td>{{ str_limit($excel[$i], 50) }}</td>
                        @endfor
                    </tr>
                @endforeach
                </tbody>
                <tbody>
            </table>

        </div>
    </div>
</div>


<script>

</script>
</body>
</html>
