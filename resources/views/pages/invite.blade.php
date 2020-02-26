@extends('control::layouts.auth')

@section('content')
    <main class="c-fullpage">
        <div class="c-fullpage__inner">
            <div class="c-login">
                <div class="c-login__heading">
                    <h1>{{ config('app.name') }}</h1>
                </div>

                <div class="c-login__main">
                    @if($invite)
                        {{ Form::open(['route' => ['control.auth.invite', $invite->token]]) }}
                        <div class="el-form-item {{ $errors->has('first_name') ? 'is-error' : '' }}">
                            <div class="el-input el-input--prefix">
                                {{ Form::text('first_name', null, ['class' => 'el-input__inner', 'placeholder' => _('Enter first name')]) }}

                                <span class="el-input__prefix"><i class="el-input__icon fal fa-user"></i></span>
                            </div>

                            @if($errors->has('first_name'))
                                <div class="el-form-item__error">{{ implode('. ', $errors->get('first_name')) }}</div>
                            @endif
                        </div>

                        <div class="el-form-item {{ $errors->has('last_name') ? 'is-error' : '' }}">
                            <div class="el-input el-input--prefix">
                                {{ Form::text('last_name', null, ['class' => 'el-input__inner', 'placeholder' => _('Enter last name')]) }}

                                <span class="el-input__prefix"><i class="el-input__icon fal fa-user"></i></span>
                            </div>

                            @if($errors->has('last_name'))
                                <div class="el-form-item__error">{{ implode('. ', $errors->get('last_name')) }}</div>
                            @endif
                        </div>

                        <div class="el-form-item {{ $errors->has('password') ? 'is-error' : '' }}">
                            <div class="el-input el-input--prefix el-input-group el-input-group--append">
                                {{ Form::password('password', ['class' => 'el-input__inner', 'placeholder' => _('Enter password')]) }}

                                <span class="el-input__prefix"><i class="el-input__icon fal fa-lock-alt"></i></span>
                            </div>

                            @if($errors->has('password'))
                                <div class="el-form-item__error">{{ implode('. ', $errors->get('password')) }}</div>
                            @endif
                        </div>

                        <div class="el-form-item {{ $errors->has('password') ? 'is-error' : '' }}">
                            <div class="el-input el-input--prefix el-input-group el-input-group--append">
                                {{ Form::password('password_confirmation', ['class' => 'el-input__inner', 'placeholder' => _('Repeat your password')]) }}

                                <span class="el-input__prefix"><i class="el-input__icon fal fa-lock-alt"></i></span>
                            </div>

                            @if($errors->has('password'))
                                <div class="el-form-item__error">{{ implode('. ', $errors->get('password')) }}</div>
                            @endif
                        </div>

                        <button type="submit" class="el-button el-button--primary">
                            <span>{{ _('Увійти') }}</span>
                        </button>
                        {{ Form::close() }}
                    @else
                        <h4>{{ _('Посилання застаріло') }}</h4>
                    @endif
                </div>
            </div>
        </div>
    </main>

    <script>
        document.querySelector('.el-checkbox').onchange = function (event) {
            var target = event.target;
            var parent = target.parentElement.parentElement;
            var child = parent.querySelector('.el-checkbox__input');
            if (target.checked) {
                parent.classList.remove('is-checked');
                child.classList.remove('is-checked');
            } else {
                parent.classList.add('is-checked');
                child.classList.add('is-checked');
            }
        }
    </script>
@endsection
