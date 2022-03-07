@extends('layouts.app')

@section('content')
  
<section>

    <div class="container">

            <div class="block_css alert alert-success alert-dismissible fade show" role="alert" style="display:none">
              Privilage  Change Successfully.
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>

        <form method="post" action="">
                @csrf
        <div class="inner-title player-right justify-content-between py-2">
            <h2>Privilege List</h2>            
        </div>

        <div class="list-games-block">

            <table id="example" class="display nowrap" style="width:100%">
                <thead>
                    <tr>
                        <th class="light-grey-bg">Sr.No.</th>
                        <th class="light-grey-bg">User Name</th>
                        <th class="light-grey-bg">List Client</th>
                        <th class="light-grey-bg">Main Market</th>
                        <th class="light-grey-bg">Manage Fancy</th>
                        <th class="light-grey-bg">Fancy History</th>
                        <th class="light-grey-bg">Match History</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $count=1; ?>
                    @foreach($users as $user)
                    <tr>

                        <td class="white-bg">{{$count}}</td>
                        <td class="white-bg">{{$user->user_name}}</td>
                        <td class="white-bg"><input type="checkbox" name="list_client" id="list_client{{$user->id}}" onclick="changestatus('{{$user->id}}','list_client');" {{ $user->list_client == '1' ? 'checked' : '' }}></td>
                        <td class="white-bg"><input type="checkbox" name="main_market" id="main_market{{$user->id}}" onclick="changestatus('{{$user->id}}','main_market');" {{ $user->main_market == '1' ? 'checked' : '' }}></td>
                        <td class="white-bg"><input type="checkbox" name="manage_fancy" id="manage_fancy{{$user->id}}" onclick="changestatus('{{$user->id}}','manage_fancy');" {{ $user->manage_fancy == '1' ? 'checked' : '' }}></td>
                        <td class="white-bg"><input type="checkbox" name="fancy_history" id="fancy_history{{$user->id}}" onclick="changestatus('{{$user->id}}','fancy_history');" {{ $user->fancy_history == '1' ? 'checked' : '' }}></td>
                        <td class="white-bg"><input type="checkbox" name="match_history" id="match_history{{$user->id}}" onclick="changestatus('{{$user->id}}','match_history');" {{ $user->match_history == '1' ? 'checked' : '' }}></td>
                        
                    </tr>
                   <?php $count++; ?>   
                    @endforeach
          
                </tbody>
            </table>
        </div>
    </form>

    </div>

</section>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>

<script type="text/javascript">
     function changestatus(id,nameatt){
        var gstatus='';
         if($("#"+nameatt+id).is(':checked')){
            gstatus = 1;
        }else{
            gstatus = 0;
        }
        var _token = $("input[name='_token']").val();

        $.ajax({
               type:'POST',
               url:"{{url('changestatusListClient')}}",
               data:{
                  "_token": "{{ csrf_token() }}",
                   uid: id,
                   gstatus:gstatus,
                   nameatt:nameatt,
               },
               success:function(data) {

               if(data.result = 'success'){
                        $(".block_css").css("display","block");
                        $(document).scrollTop(100);
                   }

                }
            });
    }
</script>
@endsection