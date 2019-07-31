@extends('layouts.app')

@section('content')

    <div class="grid">

        <div class="path">
                <h1>Is this the droid you were looking for?</h1>
                <div class="pathString">{{ $path }}</div>
                <div class="stats">
                    <div>
                        <h3>Map length</h3>
                        <span>{{ count($layout) }}</span>
                    </div>
                    <div>
                        <h3>Moves made</h3>
                        <span>{{ strlen($path) }}</span>
                    </div>
                </div>
        </div>

        <div class="map">
            @foreach ($layout as $row)

                <div class="mapRow">{{ $row }}</div>

            @endforeach
        </div>

    </div>

@endsection