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
                                                <form action="{{ route('in_wrapper', $site->site_url) }}" method="post" style="margin-bottom: 5px;">
                                                    @csrf
                                                    <input type="hidden" name="_url" value="{{$site->site_url}}">
                                                    <button  class="btn btn-success" style="width: 100%">В накрутку</button>
                                                </form>
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
