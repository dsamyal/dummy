@extends('layouts.appdash')

@section('content')

<div class="breadcrumbs">
    <div class="breadcrumbs-inner">
        <div class="row m-0">
            <div class="col-sm-4">
                <div class="page-header float-left">
                    <div class="page-title">
                        <h1>Category Management</h1>
                    </div>
                </div>
            </div>
            <div class="col-sm-8">
                <div class="page-header float-right">
                    <div class="page-title">
                        <ol class="breadcrumb text-right">
                            <li><a href="#">Category Management</a></li>
                            <li class="active">List</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="animated fadeIn">
        <div class="row">
            <div class="col-md-12">
                @if ($message = Session::get('success'))
                <div class="alert alert-success">
                  <p>{{ $message }}</p>
                </div>
                @endif
            </div>    
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <strong class="card-title">Category List</strong>
                        <div class="pull-right">
                            <a class="btn btn-success" href="{{ route('categories.create') }}"> Create New Category</a>
                        </div>
                    </div>
                    
                    <div class="card-body">
                    <table id="bootstrap-data-table" class="table table-striped table-bordered">
                      <tr>
                         <th>No</th>
                         <th>Category Name</th>
                         <th>Parent Category Name</th>
                         <th width="280px">Action</th>
                      </tr>
                        @foreach ($categories as $key => $category)
                        <tr>
                            <td>{{ ++$i }}</td>
                            <td>{{ $category->title }}</td>
                            <td>{{ isset($category->parent->title)?$category->parent->title:'N/A' }}</td>
                            <td>
                                <a class="btn btn-primary" href="{{ route('categories.edit',$category->id) }}">Edit</a>
                                <form action="{{ route('categories.destroy',$category->id) }}" method="POST" accept-charset="UTF-8" style='display:inline;'>
									@csrf
									<input name="_method" type="hidden" value="DELETE">
									<input type="submit" class="btn btn-danger" value="Delete">
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </table>
                    
                    {!! $categories->render() !!}
                    </div>
                </div>
            </div>
        </div>
    </div><!-- .animated -->
</div><!-- .content -->

@endsection