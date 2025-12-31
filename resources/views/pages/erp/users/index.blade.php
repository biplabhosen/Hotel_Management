@extends('layout.erp.app')
@section('content')


<table class="table p-5">
    <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">Name</th>
            <th scope="col">Email</th>
            {{-- <th scope="col">Photo</th> --}}
            <th scope="col">Action</th>
        </tr>
    </thead>


    <tbody>
        @foreach ($users as $user)
            <tr>
                <th scope="row">{{ $user->id }}</th>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                {{-- <td><img src="{{ asset('storage/photo/user') }}/{{ $user->photo }}" width="100" alt=""
                        srcset=""></td> --}}
                <td class="btn btn-group">
                    <a class="btn btn-primary" href="{{ url('user/edit', $user->id) }}">Edit</a>
                    <form action="{{ URL('user/delete', $user->id) }}" method="post">
                        @csrf
                        @method('delete')
                        <button onclick="return confirm('Are you sure?')" type="submit"
                            class="btn btn-danger">Delete</button>
                    </form>
                </td>
            </tr>
        @endforeach

    </tbody>
</table>
{{$users->links()}}

@endsection
