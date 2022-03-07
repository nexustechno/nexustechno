<script>
    $(document).ready(function(){
        $("body").on('click','.make-fav-match',function () {
            var id = $(this).attr('data-id');
            var action = $(this).attr('data-action');
            if(getUser==undefined || getUser==null || getUser=='') {
                $("#myLoginModal").modal('show');
            }else{
                var _token = $("input[name='_token']").val();
                $.ajax({
                    type: "POST",
                    url: '{{route("user.fav.match")}}',
                    data: {_token:_token, id:id},
                    beforeSend:function(){},
                    complete: function(){},
                    success: function(data){
                        if(data.result == 'login'){
                            $("#myLoginModal").modal('show');
                        }else if(data.result == 'added'){
                            $(".unpin-img",this).hide();
                            $(".pin-img",this).show();
                        }else if(data.result == 'remove'){
                            $(".pin-img",this).hide();
                            $(".unpin-img",this).show();
                        }else{
                            if(action == 'multimarket'){
                                window.location.reload();
                            }
                        }
                    }
                });
            }
        });
    });
</script>
