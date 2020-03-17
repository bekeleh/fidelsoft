<style>
    p {
        font-size: 16px;
    }

    h3 {
        padding-bottom: 10px;
    }
</style>
<div class="row">
    <div class="col-md-9 col-md-offset-1">
        <div class="col-md-10">
            <div class="callout callout-danger">
                <h3><i class="icon fa fa-warning"></i> System Unavailable</h3>
                <p>{!! json_decode(file_get_contents(storage_path('framework/down')), true)['message'] !!}</p>
            </div>
        </div>
    </div>
</div>