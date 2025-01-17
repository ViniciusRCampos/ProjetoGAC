@extends('layouts.master')

@section('title', 'GAC System - Login')

@section('page-style')
<style>
    body {
        display: flex;
    }
</style>
@endsection

@section('content')
    <div class="row w-100 justify-content-center align-items-center m-0">
        <div class="col-auto mx-2">
            <div class="card bg-info">
                <div class="card-header">
                    <h1 class="text-center font-weight-bold">Bem vindo ao sistema GAC!</h1>
                </div>
                <div class="card-body">
                    <form id="login_form" class="needs-validation" novalidate>
                        @csrf
                        <div class="form-group">
                            <label for="user_input">Usuario</label>
                            <input type="input" name="user_input" id="user_input" class="form-control" required>
                            <div class="invalid-feedback">
                                O campo usuário é obrigatório.
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="password">Senha</label>
                            <input type="password" name="password" id="password" class="form-control" required>
                            <div class="invalid-feedback">
                                O campo senha é obrigatório.
                            </div>
                        </div>
                        <div class="text-danger d-none" id="login_error">
                            Usuario ou senha incorretos, tente novamente.
                        </div>
                        <div class="form-group justify-content-end d-flex">
                            <button type="button" id="btn_login" class="btn btn-dark mr-2">
                                <span id="btn_login_text" class="font-weight-light">Entrar</span>
                                <div class="spinner-border d-none" role="status" id="spinnerLogin" style="width: 1.5rem; height: 1.5rem;">
                                    <span class="sr-only">Loading...</span>
                                </div>
                            </button>
                            <button type="button" id="btn_register" class="btn btn-secondary font-weight-light">Cadastrar</button>
                        </div>
                    </form>

                    <form id="register_form" class="d-none needs-validation" novalidate>
                        @csrf
                        <div class="form-group">
                            <label for="name_register_input">Nome Completo</label>
                            <input type="input" name="name_register_input" id="name_register_input" class="form-control" required minlength="3" pattern="[a-zA-Z ]+" title="Apenas letras são permitidas.">
                            <div class="invalid-feedback">
                                O campo precisa de pelo menos 3 caracteres.
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="email_register_input">E-mail</label>
                            <input type="email" name="email_register_input" id="email_register_input" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="register_user_input">Usario</label>
                            <input type="input" name="register_user_input" id="register_user_input" class="form-control" required minlength="3" pattern="[a-zA-Z0-9]+" title="Apenas letras e números são permitidos.">
                            <div class="invalid-feedback">
                                O campo precisa de pelo menos 3 caracteres.
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="register_password_input">Senha</label>
                            <input type="password" name="register_password_input" id="register_password_input" class="form-control" required minlength="8" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" title="Deve conter no mínimo 8 caracteres, uma letra maiúscula, uma minúscula e um número.">
                            <small class="text-gray font-weight-light">Deve conter no mínimo 8 caracteres, uma letra maiúscula, uma minúscula e um número.</small>
                        </div>
                        <div class="form-group justify-content-end d-flex">
                            <button type="button" id="btn_register_form" class="btn btn-dark mr-2 font-weight-light">Cadastrar</button>
                            <button type="button" id="btn_return" class="btn btn-secondary font-weight-light">Voltar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page-script')
<script>
    const btnLogin = document.getElementById('btn_login');
    const btnRegister = document.getElementById('btn_register');
    const btnReturn = document.getElementById('btn_return');
    const btnRegisterForm = document.getElementById('btn_register_form');
    const btnLoginText = document.getElementById('btn_login_text');
    const formLogin = document.getElementById('login_form');
    const formRegister = document.getElementById('register_form');
    const loginSpinner = document.getElementById('spinnerLogin');
    const loginError = document.getElementById('login_error');

    formLogin.addEventListener('keyup', (e) => {
        if (e.key === 'Enter') {
            btnLogin.click();
        }
    });

    formRegister.addEventListener('keyup', (e) => {
        if (e.key === 'Enter') {
            btnRegisterForm.click();
        }
    });

    btnRegister.addEventListener('click', () => {
        formLogin.classList.add('d-none');
        formRegister.classList.remove('d-none');
    });

    btnReturn.addEventListener('click', () => {
        formLogin.classList.remove('d-none');
        formRegister.classList.add('d-none');
        formRegister.reset();
    });

    document.getElementById('user_input').addEventListener('input', () => {
        loginError.classList.add('d-none');
    });

    document.getElementById('password').addEventListener('input', () => {
        loginError.classList.add('d-none');
    });

    btnLogin.addEventListener('click', (e) => {
        const username = document.getElementById('user_input').value.trim();
        const password = document.getElementById('password').value.trim();

        formLogin.classList.add('was-validated');
        loginError.classList.add('d-none');

        if (formLogin.checkValidity() === false) {
            event.preventDefault();
            event.stopPropagation();
        } else {
            btnLogin.setAttribute('disabled', 'disabled');
            btnLoginText.classList.add('d-none');
            spinnerLogin.classList.remove('d-none');

            fetch('/login', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                    },
                    body: JSON.stringify({
                        username,
                        password
                    })
                }).then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        window.location.href = '/';
                    } else {
                        loginError.classList.remove('d-none');
                        formLogin.reset();
                        btnLoginText.classList.remove('d-none');
                        btnLogin.removeAttribute('disabled');
                        spinnerLogin.classList.add('d-none');
                        formLogin.classList.remove('was-validated');
                        console.error(data.errors);
                    }
                });
        }
    });

    btnRegisterForm.addEventListener('click', () => {
        const name = document.getElementById('name_register_input').value;
        const email = document.getElementById('email_register_input').value;
        const username = document.getElementById('register_user_input').value;
        const password = document.getElementById('register_password_input').value;

        formRegister.classList.add('was-validated');

        if (formRegister.checkValidity() === false) {
            event.preventDefault();
            event.stopPropagation();
        } else {
            fetch('/register', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                    },
                    body: JSON.stringify({
                        name,
                        email,
                        username,
                        password
                    })
                }).then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        formRegister.reset();
                        formRegister.classList.remove('was-validated');
                        formRegister.classList.add('d-none');
                        formLogin.classList.remove('d-none');
                        toastr.success('Usuário cadastrado com sucesso!', 'Sucesso', {
                            closeButton: true,
                            progressBar: true,
                            timeOut: 3000
                        });
                    } else {
                        console.log(data);
                        toastr.error(Object.values(data.errors)[0][0], 'Erro', {
                            closeButton: true,
                            progressBar: true
                        });
                    }
                }).catch(error => {
                    toastr.error('Erro na comunicação com o servidor.', 'Erro', {
                        closeButton: true,
                        progressBar: true
                    });
                    console.log(error);
                });
        }
    });
</script>
@endsection