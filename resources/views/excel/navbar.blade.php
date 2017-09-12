<div class="container" id="navbar">
    <div class="row" style="margin-top: 0">
        <div class="navbar navbar-inverse navbar-static-top">
            <div class="container">
                <div class="navbar-header">
                    <a class="navbar-brand pull-left" href="#" target="_blank">
                        <img src="{{ asset('/images/photo.jpg') }}"
                             style="max-width:100px; max-height: 40px; margin-top: -7px;"> </img>
                    </a>
                </div>
                <div class="collapse navbar-collapse" id="responsive-menu">
                    <ul class="nav navbar-nav">
                        <li><a id="allTables" href="{{ route('index_excel') }}">Експорт excel</a></li>
                    </ul>
                    <div class="navbar-form navbar-right">
                        <a href="" class="btn btn-primary" onclick="event.preventDefault();">
                            <i class="glyphicon glyphicon-log-out"></i> Выйти
                        </a>
                    </div>
                    <form id="logout-form" action="" method="POST" style="display: none;">
                        {{ csrf_field() }}
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>