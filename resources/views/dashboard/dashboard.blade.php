@extends('layouts.app')

@section('content')
    <div class="container">
        @if(session()->has('error'))
            <div class="alert alert-danger">
                {{ session()->get('error') }}
            </div>
        @endif
        <div class="table-responsive">
            <table class="table table-bordered dataTable no-footer">
                <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Url сайта</th>
                    <th scope="col">Статус сайта</th>
                    <th scope="col">Действия</th>
                </tr>
                </thead>
                <tbody>
                @foreach($sites as $site)
                    <tr>
                        <td>{{ $site->id  }}</td>
                        <td>{{ $site->site_url }}</td>
                        @switch($site->status)
                            @case("START")
                            <td style="color: green">В накрутке</td>
                            @break

                            @case("STOP")
                            <td style="color: red">Не накручивается</td>
                            @break

                            @case("ERR")
                            <td style="color: purple">Ошибка</td>
                            @break

                            @default
                            <td style="color: coral">Статус не определен</td>
                        @endswitch
                        <td width="15%">
                            <div class="actions">

                                <div class="buttons-form mb-3">
                                    <button type="button" id="{{ $site->id  }}" class="btn btn-success wrapper" data-toggle="modal" data-target="#exampleModal" style="width: 100%">В накрутку</button>
                                </div>
                                <!-- Modal -->
                                <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="exampleModalLabel">Отправить в накрутку</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <form id="wrapper" method="post" action="{{ route('in_wrapper') }}">
                                                @csrf
                                                <div class="modal-body">
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text" id="">Кол-во</span>
                                                        </div>
                                                        <input type="text" class="form-control" id="count_wrapp" name="count_wrapp">
                                                        <input type="text" hidden class="form-control" id="SiteUrl" name="SiteUrl" value="">
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="submit" id="inWrapp" class="btn btn-primary">Накрутить</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <form action="{{ route('destroy_site', $site->id) }}" method="post">
                                    @csrf
                                    @method('DELETE')
                                    <button  class="btn btn-danger" style="width: 100%">Удалить</button>
                                </form>
                            </div>

                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
