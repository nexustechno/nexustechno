<div class="footer_fixed chreme-bg">
    <div class="main_wrap container">
        <ul>
            <li>
                <span class="grey-gradient-bg"><img src="{{ URL::to('asset/img/coin-icon.png')}}"></span>
                <p>Bank</p>
            </li>
            <li>
                <span class="grey-gradient-bg"><img src="{{ URL::to('asset/img/updown-arrow-icon.png')}}"></span>
                <p>Betting Profit &amp; Loss</p>
            </li>
            <li>
                <span class="grey-gradient-bg"><img src="{{ URL::to('asset/img/history-icon.png')}}"></span>
                <p>Betting History</p>
            </li>
            <li>
                <span class="grey-gradient-bg"><img src="{{ URL::to('asset/img/user-icon.png')}}"></span>
                <p>Profile</p>
            </li>
            <li>
                <span class="grey-gradient-bg"><img src="{{ URL::to('asset/img/setting-icon.png')}}"></span>
                <p>Change Status</p>
            </li>
        </ul>
    </div>
</div>
</div>

<script src="{{ asset('asset/js/jquery.js') }}" ></script>
<script src="{{ asset('asset/js/popper.min.js') }}" ></script>
<script src="{{ asset('asset/js/bootstrap.min.js') }}" ></script>
<script src="{{ asset('asset/js/jquery-ui.min.js') }}" ></script>
<script src="{{ asset('asset/js/jquery-ui.multidatespicker.js') }}" ></script>
<script src="{{ asset('asset/js/datatables/js/jquery.dataTables.min.js') }}" ></script>
<script src="{{ asset('asset/js/datatables/js/dataTables.buttons.min.js') }}" ></script>
<script src="{{ asset('asset/js/datatables/js/pdfmake.min.js') }}" ></script>
<script src="{{ asset('asset/js/datatables/js/jszip.min.js') }}" ></script>
<script src="{{ asset('asset/js/datatables/js/vfs_fonts.js') }}" ></script>
<script src="{{ asset('asset/js/datatables/js/buttons.html5.min.js') }}" ></script>
<script src="{{ asset('asset/js/datatables/js/buttons.print.min.js') }}" ></script>
<script src="{{ asset('asset/js/script.js') }}" ></script>

<script>
function autologout(){
     $.ajax({
            type: "post",
            url: '{{route("autoLogout")}}',
            data: {"_token": "{{ csrf_token() }}"},
            beforeSend:function(){
                $('#site_statistics_loading').show();
            },
            complete: function(){
                $('#site_statistics_loading').hide();
            },
            success: function(data){
                if(data.result=='suspendsuccess'){
                window.location.href = "{{ route('backpanel')}}";
            }
            if(data.result=='msgsuccess'){
                window.location.href = "{{ route('maintenance')}}";
            }
            if(data.result=='changePassLogout'){
                window.location.href = "{{ route('backpanel')}}";
            }
        }
    });
}


$(document).ready(function() {
    var loginuser='<?php echo $loginuser->agent_level; ?>';
if(loginuser != 'COM'){
    setInterval(function() {
    	autologout(); 
    }, 10000)
}
});


</script>
</body>
</html>