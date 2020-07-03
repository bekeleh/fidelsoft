<!DOCTYPE html>
<html lang="en">
<head>
    <title><?php echo e(trans('texts.team_source')); ?></title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" type="image/png" href="/images/favicon.png">
    <link rel="stylesheet" href="/css/bootstrap.min.css">
    <link rel="stylesheet" href="/css/app.css">
    <link rel="stylesheet" href="/css/font-awesome.css">
    <link rel="stylesheet" href="/plugins/jcrop/Jcrop.min.css">
    <link rel="stylesheet" href="/css/toastr.min.css">
    <link href="/plugins/ckeditor/plugins/codesnippet/lib/highlight/styles/github.css" rel="stylesheet">
    <link href="/plugins/vakata-jstree/dist/themes/default/style.css" rel="stylesheet">
    <link href="/plugins/atjs/jquery.atwho.min.css" rel="stylesheet">
    <link href="/plugins/select2/select2.min.css" rel="stylesheet">
</head>
<body <?php if(isset($editWiki) && $editWiki === true): ?> style="overflow: hidden;" <?php endif; ?>>
    <div class="modal fade" id="team-logo-modal" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">Crop Image</h4>
                </div>
                <div class="modal-body" style="display: table; margin: 0 auto;">
                    <img id="team-logo-crop" class="crop" src="#" alt="Crop Image"/>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" 
                    onclick="$('#avatar-upload-form').submit();">Save changes
                </button>
            </div>
        </div>
    </div>
</div>
<div id="app">
    <?php if(Auth::user()): ?>
    <?php if(!isset($entityType)): ?>
    <?php echo $__env->make('partials.menu', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
    <?php endif; ?>
    <div <?php if(!isset($entityType)): ?> style="position: absolute; top: 50px; width: 100%; height: calc(100% - 62px);" <?php endif; ?>>
        <?php echo $__env->yieldContent('content'); ?>
    </div>
    <?php else: ?>
    <?php echo $__env->yieldContent('content'); ?>
    <?php endif; ?>

</div>

<script type="text/javascript" src="/js/jquery.js"></script>
<script type="text/javascript" src="/js/list.min.js"></script>
<script type="text/javascript" src="/js/laroute.js"></script>
<script type="text/javascript" src="/js/bootstrap.min.js"></script>
<script type="text/javascript" src="/plugins/jcrop/Jcrop.min.js"></script>
<script type="text/javascript" src="/plugins/jquery-infinitescroll/jquery.infinitescroll.min.js"></script>
<script type="text/javascript" src="/js/app.js"></script>
<script type="text/javascript" src="/js/toastr.min.js"></script>
<script type="text/javascript" src="/plugins/ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="/js/moment.min.js"></script>
<script type="text/javascript" src="/js/color-hash.js"></script>
<script type="text/javascript" src="/js/laravel-delete-req.js"></script>
<script src="/plugins/ckeditor/plugins/codesnippet/lib/highlight/highlight.pack.js"></script>
<script src="/plugins/vakata-jstree/dist/jstree.min.js"></script>
<script src="/plugins/atjs/jquery.caret.min.js"></script>
<script src="/plugins/atjs/jquery.atwho.min.js"></script>
<script src="/plugins/select2/select2.full.min.js"></script>
<script>
    (function () {
        hljs.initHighlightingOnLoad();
    })();
</script>



    
    
    

    
    



</body>
</html>