@extends ( 'cms.layout.notice' )

@section ( 'content' )

    {{-- <div class="error-code">500</div> --}}
    <div class="error-message">Your signin token for the User Manager App has been cleared.</div>
    <div class="button-place">
        <a href="/signin" class="btn btn-default btn-lg waves-effect">REINSTATE TOKEN OR SIGN IN HERE</a>
    </div>

@endsection