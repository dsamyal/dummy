@extends('layouts.appdash')

@section('content')
<div class="content">
    <!-- Animated -->
    <div class="animated fadeIn">
        <!-- Widgets  -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <table id="example" class="table table-striped table-bordered" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Post ID</th>
                                    <th>Clicks</th>
                                    <th>Post URL</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php //echo '<pre>'; print_r($visitor_data); ?>
                                @foreach($visitor_data as $data)
                                <tr>
                                    <td>{{ $data->post_id }}</td>
                                    <td>{{ $data->count_click }}</td>
                                    <td><a href="{{ $data->post_url }}" target="_blank">{{ $data->post_url }}</a></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
       
        <div class="clearfix"></div>
  
        <!-- Modal - Calendar - Add Category -->
        <div class="modal fade none-border" id="add-category">
            <div class="modal-dialog"> 
                <div class="modal-content">
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-danger waves-effect waves-light save-category" data-dismiss="modal">Save</button>
                    </div>
                </div>
            </div>
        </div>
    <!-- /#add-category -->
    </div>
    <!-- .animated -->
</div>
@endsection
