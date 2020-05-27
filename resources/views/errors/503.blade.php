<style type="text/css" media="screen">
    .container {
        margin: 10px auto;
        max-width: 600px;
        text-align: center;
    }

    h1 {
        margin: 30px 0;
        font-size: 4em;
        line-height: 1;
        letter-spacing: -1px;
    }
</style>

<div class="container">
    <h1>503</h1>
    <p><strong>System Unavailable :(</strong></p>
    <p>
        {!! json_decode(file_get_contents(storage_path('framework/down')), true)['message'] !!}
    </p>
    You maybe <a href="{{ url('/') }}">return to the home</a>.
</div>