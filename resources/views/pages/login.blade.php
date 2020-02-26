@extends('layouts.auth')

@section('content')
    <main class="c-fullpage">
        <div class="c-fullpage__inner">
            <div class="c-login">
                <div class="c-login__heading">
                    <h1>{{ config('app.name') }}</h1>
                    {{-- <a href="#" target="_blank">
                        <svg width="130" height="45" viewBox="0 0 496 159" xmlns="http://www.w3.org/2000/svg">
                            <g fill="none" fill-rule="evenodd">
                                <path d="M317.031 125.6c-9.801 19.829-30.514 33.357-54.567 33.357-33.496 0-60.76-26.34-60.76-58.916 0-32.576 27.264-58.916 60.76-58.916 33.504 0 60.54 26.34 60.54 58.916 0 3.464-.696 7.16-1.62 10.396 0 0-27.724-3.004-27.724-3.236 1.384-3.696 2.076-3.004 2.076-7.16 0-18.252-15.016-33.268-33.272-33.268-18.252 0-33.264 15.016-33.264 33.268 0 18.256 15.012 33.272 33.264 33.272 8.035 0 15.54-2.904 21.32-7.713h33.247z"
                                      fill="#000" />
                                <path fill="#000"
                                      d="M220.416 110.438h100.968l-21.488-22.644h-79.48zM113.675 41.124c-32.808 0-59.608 26.34-59.608 58.92 0 32.576 26.8 58.912 59.608 58.912 32.808 0 59.148-26.336 59.148-58.912 0-32.58-26.34-58.92-59.148-58.92m1.156 92.188c-18.256 0-33.272-15.016-33.272-33.268 0-18.252 15.016-33.272 33.272-33.272 18.252 0 33.268 15.02 33.268 33.272s-15.016 33.268-33.268 33.268"
                                />
                                <path fill="#000"
                                      d="M148.101 157.57h27.264V1.61h-27.264zM408.713 157.57h27.268V1.61h-27.268zM2.541 157.57h27.264V42.51H2.541z"
                                />
                                <path fill="#E10600" d="M468.357 157.57h27.264v-28.163h-27.264z" />
                                <path d="M0 16.172c0 9.008 7.16 16.176 16.172 16.176 9.016 0 16.176-7.168 16.176-16.176C32.348 7.16 25.188 0 16.172 0 7.16 0 0 7.16 0 16.172M348.413 157.57h27.268V42.51h-27.268zM345.873 16.172c0 9.008 7.164 16.176 16.176 16.176 9.004 0 16.168-7.168 16.168-16.176C378.217 7.16 371.053 0 362.049 0c-9.012 0-16.176 7.16-16.176 16.172"
                                      fill="#000" />
                            </g>
                        </svg>
                    </a> --}}
                </div>

                <div class="c-login__main">
                    {{ Form::open(['route' => 'control.auth.login']) }}
                        <div class="el-form-item {{ $errors->has('email') ? 'is-error' : '' }}">
                            <div class="el-input el-input--prefix">
                                {{ Form::email('email', null, [
                                    'autocomplete' => 'off',
                                    'autofocus',
                                    'tabIndex' => 1,
                                    'class' => 'el-input__inner',
                                    'placeholder' => _('Enter E-mail')
                                ]) }}

                                <span class="el-input__prefix"><i class="el-input__icon fal fa-envelope"></i></span>
                            </div>

                            @if($errors->has('email'))
                                <div class="el-form-item__error">{{ implode('. ', $errors->get('email')) }}</div>
                            @endif
                        </div>

                        <div class="el-form-item {{ $errors->has('password') ? 'is-error' : '' }}">
                            <div class="el-input el-input--prefix el-input-group el-input-group--append">
                                {{ Form::password('password', [
                                    'tabIndex' => 2,
                                    'class' => 'el-input__inner',
                                    'placeholder' => _('Enter password')
                                ]) }}

                                <span class="el-input__prefix"><i class="el-input__icon fal fa-lock-alt"></i></span>

                                <div class="el-input-group__append el-input-group__append--sm">
                                    <a href="{{ route('control.auth.password.request') }}"
                                        class="forgotten-link">{{ _('Forgot?') }}</a>
                                </div>
                            </div>

                            @if($errors->has('password'))
                                <div class="el-form-item__error">{{ implode('. ', $errors->get('password')) }}</div>
                            @endif
                        </div>

                        <button type="submit" class="el-button el-button--primary" tabindex="4">
                            <span>{{ _('Sign in') }}</span>
                        </button>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </main>

@endsection
