@extends('app')
@section('content')
    <main class="login-form">
        <div class="cotainer">
            <div class="row justify-content-center">
                <div class="col-md-4">
                    <div class="card">
                        <h3 class="card-header text-center">Password</h3>
                        <div class="card-body">
                            <form method="POST" action="{{ route('customResetPassword') }}">
                                @csrf
                                <input name="token" style="display: none" value="{{$token}}">
                                <div class="form-group mb-3">
                                    <input type="text" placeholder="Email" id="email" class="form-control" name="email" required
                                           autofocus>
                                </div>
                                <div class="form-group mb-3">
                                    <input type="password" placeholder="Password" id="password" class="form-control"
                                           name="password" required>
                                </div>
                                <div class="form-group mb-3">
                                    <input type="password" placeholder="Password" id="password_confirmation" class="form-control"
                                           name="password_confirmation" required>
                                </div>
                                <div class="d-grid mx-auto">
                                    <button type="submit" class="btn btn-dark btn-block">Send</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection
