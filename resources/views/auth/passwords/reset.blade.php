@extends("layouts.registration")
@section('content')
    <div id="registration" class="box shadow border-top-0 p-4">
        <div class="row">
            <div class="col-6 d-flex align-items-center justify-content-start">
                <i class="fa fa-arrow-right fa-1-6x green-color hover-blue hover-scale" data-bs-toggle="tooltip" title="بازگشت" v-on:click="WindowRelocate('{{route("login")}}')"></i>
            </div>
            <div class="col-6 d-flex align-items-center justify-content-end">
                <i class="fa fa-question-circle fa-1-6x green-color hover-blue hover-scale" data-bs-toggle="modal" title="راهنما" data-bs-target="#help_modal"></i>
            </div>
        </div>
        <h4 class="iranyekan text-left pb-2 pt-2 green-color">
            بازنشانی گذرواژه
        </h4>
        <div class="col-12 align-self-center text-center">
            <img class="introduction-image" alt="password reset" src="{{ asset("/images/authentication/password-forget.svg") }}"/>
        </div>
        <div class="col-12 align-self-center">
            <form class="mt-3" method="POST" action="{{ route('password.update') }}">
                @csrf

                <input type="hidden" name="token" value="{{ $token }}">

                <div class="row">
                    <div class="mb-3 col-12">
                    <label for="email" class="col-md-4 col-form-label text-md-start">آدرس ایمیل</label>

                        <input id="email" type="email" class="form-control b-form registration-input-text text-center iranyekan @error('email') is-invalid @enderror" name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus>

                        @error('email')
                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                        @enderror
                    </div>
                    <div class="mb-3 col-12">
                        <label for="password" class="col-md-4 col-form-label text-md-start">گذرواژه جدید</label>
                        <input id="password" type="password" class="form-control b-form registration-input-text text-center iranyekan @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">

                        @error('password')
                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                        @enderror
                    </div>

                    <div class="mb-3 col-12">
                        <label for="password-confirm" class="col-md-4 col-form-label text-md-start">تکرار گذرواژه جدید</label>

                        <input id="password-confirm" type="password" class="form-control b-form registration-input-text text-center iranyekan" name="password_confirmation" required autocomplete="new-password">
                    </div>

                    <div class="mb-3 col-12 text-center">
                            <button type="submit" class="btn btn-primary">
                                بازنشانی گذرواژه جدید
                            </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
