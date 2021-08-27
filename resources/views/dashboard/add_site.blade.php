@extends('layouts.app')
@section('content')
    <div class="container">

        <form method="post" action="{{ route('save_site') }}">
            {{ csrf_field() }}
            <div class="input-group col-md-5 offset-md-3 mb-3">
                <input name="url" type="text" class="form-control" placeholder="Вставьте ссылку на сайт" aria-label="Recipient's username" aria-describedby="basic-addon2" value="">
                <div class="input-group-append">
                    <button type="submit" class="btn btn-outline-secondary" type="button" id="parse_comment">Добавить</button>
                </div>
            </div>
        </form>
    </div>
@endsection
