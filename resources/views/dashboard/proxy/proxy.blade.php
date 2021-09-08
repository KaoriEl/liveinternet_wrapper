@extends('layouts.app')

@section('content')
    <div class="container">
        @if(session()->has('error'))
            <div class="alert alert-danger">
                {{ session()->get('error') }}
            </div>
        @endif
        <div class="buttons-form mb-3">
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal">Добавить прокси</button>
            {{--            <button type="button" class="btn btn-primary">Загрузить аккаунты файлом</button>--}}
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
                                    <span class="input-group-text" id="">Прокси</span>
                                </div>
                                <input type="text" class="form-control" name="proxy">
{{--                                <input type="hidden" value="1" class="form-control" name="status">--}}
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Добавить</button>
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
                                <button  class="btn btn-danger" style="width: 100%">Удалить</button>
                            </form>
                        </td>
                    </tr>

                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
