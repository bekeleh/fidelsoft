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
    <div id="notfound">
        <div class="notfound">
            <div class="notfound-404">
                <h1>404</h1>
            </div>
            <h2>Oops! Page Not Be Found</h2>
            <p>Sorry but the page you are looking for does not exist, have been removed. name changed or is temporarily
                unavailable</p>
            <a href="<?php echo e(url('/')); ?>">Back to homepage</a>
        </div>
    </div>
</div>