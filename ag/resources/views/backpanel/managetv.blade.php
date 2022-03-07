@extends('layouts.app')
@section('content')

<section class="manage_wrapper white-bg">
  <div class="container">
    <div class="inner-title player-right py-2">
      <h2>Manage TV</h2>
    </div>

    <div class="manage_tv_block">
      <form action="{{route('addManageTv')}}" method="post">
        @csrf

        @if(!empty($tv))
          <?php
          $checked = '';
          if(!empty($tv->cs1 != 'off')){
            $checked = 'checked';
          }
          ?>
          <div class="form-group col-lg-6 col-md-10 col-sm-12 col-12 p-0">
            <label for="">TV URL 1:</label>
            <input type="text" name="channel1" id="channel1" class="form-control"@if(!empty($tv['channel1'])) value= "{{$tv['channel1']}}" @else placeholder="http://lordsexch.com/mediaplayergame/30424203/27.63.150.123&output=embed" @endif>
            <input type="checkbox" name="cs1" id="cs1" {{$checked}}>
          </div>
          <div class="form-group col-lg-6 col-md-10 col-sm-12 col-12 p-0">
            <label for="">TV URL 2:</label>
            <input type="text" name="channel2" id="channel2" class="form-control" @if(!empty($tv['channel2'])) value= "{{$tv['channel2']}}" @else placeholder="http://lordsexch.com/mediaplayergame/30424203/27.63.150.123&output=embed" @endif>
            <input type="checkbox" name="cs2" id="cs2" @if(!empty($tv['cs2'] == 'on')) checked @endif>
          </div>
          <div class="form-group col-lg-6 col-md-10 col-sm-12 col-12 p-0">
            <label for="">TV URL 3:</label>
            <input type="text" name="channel3" id="channel3" class="form-control" @if(!empty($tv['channel3'])) value= "{{$tv['channel3']}}" @else placeholder="http://lordsexch.com/mediaplayergame/30424203/27.63.150.123&output=embed" @endif>
            <input type="checkbox" name="cs3" id="cs3" @if(!empty($tv['cs3'] == 'on')) checked @endif>
          </div>
          <div class="form-group col-lg-6 col-md-10 col-sm-12 col-12 p-0">
            <label for="">TV URL 4:</label>
            <input type="text" name="channel4" id="channel4" class="form-control" @if(!empty($tv['channel4'])) value= "{{$tv['channel4']}}" @else placeholder="http://lordsexch.com/mediaplayergame/30424203/27.63.150.123&output=embed" @endif>
            <input type="checkbox" name="cs4" id="cs4" @if(!empty($tv['cs4'] == 'on')) checked @endif>
          </div>
          <div class="form-group col-lg-6 col-md-10 col-sm-12 col-12 p-0">
            <label for="">TV URL 5:</label>
            <input type="text" name="channel5" id="channel5" class="form-control" @if(!empty($tv['channel5'])) value= "{{$tv['channel5']}}" @else placeholder="http://lordsexch.com/mediaplayergame/30424203/27.63.150.123&output=embed" @endif>
            <input type="checkbox" name="cs5" id="cs5" @if(!empty($tv['cs5'] == 'on')) checked @endif>
          </div>
        @else
            <div class="form-group col-lg-6 col-md-10 col-sm-12 col-12 p-0">
              <label for="">TV URL 1:</label>
              <input type="text" name="channel1" id="channel1" class="form-control"  placeholder="http://lordsexch.com/mediaplayergame/30424203/27.63.150.123&output=embed" >
              <input type="checkbox" name="cs1" id="cs1">
            </div>
            <div class="form-group col-lg-6 col-md-10 col-sm-12 col-12 p-0">
              <label for="">TV URL 2:</label>
              <input type="text" name="channel2" id="channel2" class="form-control" placeholder="http://lordsexch.com/mediaplayergame/30424203/27.63.150.123&output=embed">
              <input type="checkbox" name="cs2" id="cs2" >
            </div>
            <div class="form-group col-lg-6 col-md-10 col-sm-12 col-12 p-0">
              <label for="">TV URL 3:</label>
              <input type="text" name="channel3" id="channel3" class="form-control" placeholder="http://lordsexch.com/mediaplayergame/30424203/27.63.150.123&output=embed" >
              <input type="checkbox" name="cs3" id="cs3">
            </div>
            <div class="form-group col-lg-6 col-md-10 col-sm-12 col-12 p-0">
              <label for="">TV URL 4:</label>
              <input type="text" name="channel4" id="channel4" class="form-control" placeholder="http://lordsexch.com/mediaplayergame/30424203/27.63.150.123&output=embed" >
              <input type="checkbox" name="cs4" id="cs4">
            </div>
            <div class="form-group col-lg-6 col-md-10 col-sm-12 col-12 p-0">
              <label for="">TV URL 5:</label>
              <input type="text" name="channel5" id="channel5" class="form-control"placeholder="http://lordsexch.com/mediaplayergame/30424203/27.63.150.123&output=embed">
              <input type="checkbox" name="cs5" id="cs5" >
            </div>
        @endif
        <div class="form-group">
          <button class="submit-btn text-color-yellow mr-0">Submit</button>
        </div>
      </form>
    </div>
  </div>
</section>

<script>
  $(document).ready(function() {
    $('#example').DataTable({
      dom: 'Bfrtip',
      buttons: [
        'csv',
      ]
    });
  });
</script>
@endsection
