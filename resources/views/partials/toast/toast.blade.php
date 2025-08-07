@if(session('success'))
    @include('partials.toast.toast-success', ['message' => session('success')])
@endif

@if(session('error'))
    @include('partials.toast.toast-error', ['message' => session('error')])
@endif

@if(session('warning'))
    @include('partials.toast.toast-warning', ['message' => session('warning')])
@endif
