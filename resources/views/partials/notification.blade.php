<div class="btn-group">
    <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown">
        <div id="myAccountButton" class="ellipsis" style="width:85px">
            <?php echo $notif_cnt = count(Auth::user()->unreadnotifications) ?: '0' ?>
            Notifications <span class="badge"></span>
        </div>
    </button>
    <ul class="dropdown-menu">
        @if($notif_cnt)
            @foreach($unreadMessages = auth()->user()->unreadnotifications as $unreadMessage)
                <li>
                    <a href="{{$unreadMessage->data['link'] }}?mark=unread">
                        {{$unreadMessage->data['title'] }}
                        by <span class="fa fa-user"></span>&nbsp;&nbsp;
                        <span class="fa fa-clock-o "></span>
                        {{ \Carbon\Carbon::parse($unreadMessage->data['created_at']['date'])->diffForHumans()}}
                    </a>
                </li>
            @endforeach
        @endif
    </ul>
</div>