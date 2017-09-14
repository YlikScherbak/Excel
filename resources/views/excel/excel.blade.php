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
        <div class="col-md-6 col-md-offset-3">
            @if(Session::has('message'))
                <span class="help-block">
                                        <strong>{{ Session::get('message') }}</strong>
                                    </span>
            @endif
            <div class="panel panel-default" style="margin-top:45px">
                <div class="panel-heading">
                    <h3 class="panel-title">Выберите excel файл</h3>
                </div>
                <div class="panel-body">

                    <form method="post" action="{{ route('post_excel') }}" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <label class="custom-file">
                                <input type="file" name="file" class="custom-file-input" required>
                                <span class="custom-file-control"></span>
                            </label>
                        </div>
                        <button type="submit" class="btn btn-success">Отправить</button>
                    </form>

                </div>
            </div>
                <div class="error-actions">
                    <a href="{{ route('info') }}" class="btn btn-primary btn-lg"><span
                                class="glyphicon glyphicon-info-sign"></span>
                        Инструкция :) </a>
                </div>
        </div>
    </div>
</div>

<script>

</script>
</body>
</html>
