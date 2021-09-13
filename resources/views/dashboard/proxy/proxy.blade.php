@extends('layouts.app')

@section('content')
    <div class="container">
        @if(session()->has('error'))
            <div class="alert alert-danger">
                {{ session()->get('error') }}
            </div>
        @endif
        <div class="buttons-form mb-3">
            <button type="button" class="btn btn-outline-secondary" data-toggle="modal" data-target="#exampleModal">Добавить прокси</button>
            <button type="button" class="btn btn-outline-secondary" data-toggle="modal" data-target="#ProxyList">Загрузить txt файл с прокси</button>
        </div>
            <!-- Modal -->
            <div class="modal fade" id="ProxyList" tabindex="-1" role="dialog" aria-labelledby="ProxyList" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="ProxyList">Добавление прокси сервера</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form method="post" action=" {{ route('save_proxy_list') }} " enctype="multipart/form-data">
                            @csrf
                            <div class="modal-body">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="">Proxy:</span>
                                    </div>
                                    <input class="form-control" type="file" id="formFile" name="proxy_list">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-outline-secondary" type="button" id="parse_comment">Добавить</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        <!-- Button trigger modal -->
            <!-- Modal -->
            <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Добавление прокси сервера</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form method="post" action="{{ route('save_proxy') }}">
                            @csrf
                            <div class="modal-body">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="">Proxy:</span>
                                    </div>
                                    <input name="proxy" type="text" class="form-control" placeholder="Прокси в формате: ip:port" aria-label="Recipient's username" aria-describedby="basic-addon2" value="">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-outline-secondary" type="button" id="parse_comment">Добавить</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>



        <div class="table-responsive">
            <table class="table table-bordered dataTable no-footer">
                <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Прокси сервер</th>
                    <th scope="col">Статус прокси</th>
                    <th scope="col">Действия</th>
                </tr>
                </thead>
                <tbody>
                @foreach($proxies as $proxy)
                    <tr>
                        <td>{{ $proxy->id  }}</td>
                        <td>{{ $proxy->proxy_address }}:{{$proxy->proxy_port}}</td>
                        @switch($proxy->status)
                            @case("ACTIVE")
                            <td style="color: green">Активный</td>
                            @break

                            @case("Not verified")
                            <td style="color: purple">Не проверен</td>
                            @break

                            @case("INACTIVE")
                            <td style="color: red">Неактивный</td>
                            @break

                            @default
                            <td style="color: coral">Статус не определен</td>
                        @endswitch
                        <td width="15%">
                            <form action="{{ route('destroy_proxy', $proxy->id) }}" method="post">
                                @csrf
                                @method('DELETE')
                                <button  class="btn btn-outline-danger" style="width: 100%">Удалить</button>
                            </form>
                        </td>
                    </tr>

                @endforeach
                </tbody>
            </table>
            {{$proxies->links('pagination::bootstrap-4')}}
        </div>
    </div>
@endsection
