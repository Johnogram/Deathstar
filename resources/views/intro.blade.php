@extends('layouts.app')

@section('content')

    <div class="splashContainer">
        <a href="{{ route('navigate', ["Han"]) }}" class="startLink hanSolo"></a>
        <a href="{{ route('navigate', ["Greedo"]) }}" class="startLink greedo"></a>

        <div class="title">
            <h1>Who shot first?</h1>
        </div>
    </div>

@endsection