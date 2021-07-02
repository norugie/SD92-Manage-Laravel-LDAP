@extends ( 'cms.layout.notice' )

@section ( 'content' )

    {{-- <div class="error-code">500</div> --}}
    <div class="error-message">Something went wrong with processing your signin request. Please try again.</div>
    <div class="button-place">
        <a href="/signin" class="btn btn-default btn-lg waves-effect">SIGN IN HERE</a>
    </div>

@endsection