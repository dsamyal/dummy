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
                                    <th>IP</th>
                                    <th>Country</th>
                                    <th>User ID</th>
                                    <th>Profile URL</th>
                                    <th>Created At</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php //echo '<pre>'; print_r($visitor_data); ?>
                                @foreach($visitor_data as $data)
                                <tr>
                                    <td>{{ $data->ip }}</td>
                                    <td>{{ $data->country }}</td>
                                    <td>{{ $data->user_id }}</td>
                                    <td><a href="{{ $data->user_url }}" target="_blank">{{ $data->user_url }}</a></td>
                                    <td>{{ $data->created_at }}</td>
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
