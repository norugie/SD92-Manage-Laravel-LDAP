@if(session()->exists('message'))
    <div class="alert alert-{{ session('status') }} alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        {!! session('message') !!}
    </div>
@endif
