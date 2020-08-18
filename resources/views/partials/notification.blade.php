<div class="btn-group">
    <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown">
        <div id="myAccountButton" class="ellipsis" style="width:85px">
            <?php echo $notif_cnt = count(Auth::user()->unreadnotifications) ?: '0' ?>
            Notifications <span class="badge"></span>
        </div>
    </button>
    <ul class="dropdown-menu">
        @if($notif_cnt)
            @foreach($links =Auth::user()->unreadnotifications as $link)
                <li>
                    <a href="{{$link->data['link'] }}">
                        <span class="fa fa-file-pdf-o"></span>
                        {{$link->data['title'] }} {{ \Carbon\Carbon::parse($link->posted_at)->diffForHumans()}}
                    </a>
                </li>
            @endforeach
        @endif
    </ul>
</div>