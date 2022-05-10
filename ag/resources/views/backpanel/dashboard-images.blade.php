@extends('layouts.app')
@section('content')

<section>
    <div class="container">
        <div class="inner-title player-right justify-content-between py-2">
            <h2>Manage Dashboard Images</h2>
{{--            @if(\Request::get('admin') == 1)--}}
                <div class="btn-wrapadd">
                    <a href="{{route('dashboard.images.create')}}" class="add_player grey-gradient-bg text-color-black">Add New</a>
                </div>
{{--            @endif--}}
        </div>
        <div class="list-games-block">
            <table id="example22" class="display nowrap dataTable no-footer" style="width:100%">
                <thead>
                    <tr>
                        <th class="light-grey-bg">Title</th>
                        <th class="light-grey-bg">Image Size</th>
                        <th class="light-grey-bg">Image</th>
                        <th class="light-grey-bg">Link</th>
                        <th class="light-grey-bg" width="20%">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($images as $image)
                    <tr>
                        <td class="white-bg">{{$image->title}}</td>
                        <td class="white-bg">
                            @if($image->width_type == 'column12')
                                Full Width
                            @elseif($image->width_type == 'column6')
                                Half Width
                            @elseif($image->width_type == 'column3')
                                Square Width
                            @endif
                        </td>
                        <td class="white-bg"><img src="{{ URL::to('public/asset/upload') }}/{{$image->file_name}}" height="100px;"></td>
                        <td class="white-bg">{{$image->link}}</td>
                        <td class="white-bg">
                            <a href="{{ route('dashboard.images.edit',$image->id) }}" class="btn-list black-bg2 text-color-white">Edit</a>
                            <a href="{{ route('dashboard.images.delete',$image->id) }}" class="btn-list red-bg text-color-white">Delete</a>
                        </td>
                    </tr>
                   @endforeach
                </tbody>
            </table>
        </div>
    </div>
</section>
@endsection
