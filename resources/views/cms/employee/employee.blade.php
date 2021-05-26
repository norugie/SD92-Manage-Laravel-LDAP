@extends ( 'cms.layout.layout' )

@section ( 'content' )

    <table>
    <thead>
        <tr>
        <th>Username</th>
        <th>Full Name</th>
        </tr>
    </thead>
    <tbody>
        @foreach($users as $user)
        <tr>
        <td>{{ $user->getFirstAttribute('samaccountname') }}</td>
        <td>{{ $user->getFirstAttribute('cn') }}</td>
        </tr>
        @endforeach
    </tbody>
    </table>

@endsection