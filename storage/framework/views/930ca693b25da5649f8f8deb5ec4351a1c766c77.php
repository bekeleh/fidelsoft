<style type="text/css" media="screen">
    .notfound .notfound-404 h1 {

        font-family: 'Montserrat', sans-serif;
        font-size: 146px;
        font-weight: 700;
        margin: 0px;
        color: #232323;
    }

    .notfound h2 {
        font-family: 'Montserrat', sans-serif;
        font-size: 22px;
        font-weight: 700;
        margin: 0;
        text-transform: uppercase;
        color: #232323;
    }

    .notfound p {
        font-family: 'Montserrat', sans-serif;
        color: #787878;
        padding-bottom: 10px;
        line-height: 1.5;
        font-size: 16px;
        font-weight: 300;
    }

    notfound a {
        font-family: 'Montserrat', sans-serif;
        display: inline-block;
        padding: 12px 30px;
        font-weight: 700;
        background-color: #f99827;
        color: #fff;
        border-radius: 40px;
        text-decoration: none;
        -webkit-transition: 0.2s all;
        transition: 0.2s all;
    }

</style>

<div class="container">
    <h1>404</h1>
    <div class="error-content">
        <h3><i class="fa fa-warning text-yellow"></i><strong>Page not found :(</strong></h3>
        <p>
            We could not find the page you were looking for.
            You should maybe <a href="<?php echo e(url('/')); ?>">return to the dashboard</a>.
        </p>

    </div>
</div>
