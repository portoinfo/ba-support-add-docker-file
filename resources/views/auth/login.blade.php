@extends('layouts.login')

@section('content')
<div id="login" class="container">
    <div class="row justify-content-center" style="margin-left:100px;margin-right:100px;">
        <div class="col-md-8">
            <div class="card"  style="box-shadow: 1px 1px 5px #0195ff;border-radius: 30px;">
                <div class="card-header" style="background-color: white;border-radius: 30px;"><center><img src="images/chat.png" width="10%"><b> SUPUBER</b></center></div>

                <div class="card-body" style="border-radius: 30px;">
                    <form method="POST" action="login">
                        @csrf
                        <div class="form-group">
                            <label for="entrar">LOGAR</label>
                            <select class="form-control" v-model="typeLog" v-on:change="typeLogf">
                                <option></option>
                                <option value="1">ADMIN-MASTER</option>
                                <option value="3">FUNCINARIO</option>
                            </select>
                        </div>
                        <div class="form-group row">

                            <div class="col-md-12">
                                <input id="email" type="email" class="form-control inputBa @error('email') is-invalid @enderror" v-model="email" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="EMAIL">

                                @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">

                            <div class="col-md-12">
                                <input id="password" type="password" class="form-control inputBa @error('password') is-invalid @enderror" v-model="password" name="password" required autocomplete="current-password" placeholder="PASSWORD">

                                @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-6" style="margin-top: 7px;">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                                    <label class="form-check-label" for="remember">
                                        {{ __('Remember Me') }}
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                @if (Route::has('password.request'))
                                <a class="btn btn-link" href="{{ route('password.request') }}">
                                    {{ __('Forgot Your Password?') }}
                                </a>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-12" style="text-align: center;">
                                <button type="submit" class="btn btn-primary" style="width: 150px;background-color: #0395ff;border-radius: 30px;font-size: 20px;">
                                    {{ __('Entrar') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                @include('layouts.errors')
            </div>
        </div>
    </div>
</div>

<script>
    var log = new Vue({
    el: '#login',
    data: {
        typeLog: "",
        email: "",
        password: "",
    },
        methods:{
            typeLogf(){
                vm = this;
                if(vm.typeLog == 1){
                    vm.email = "adminMaster@live.com";
                    vm.password = "a";
                }else if(vm.typeLog == 3){
                    vm.email = "funcionario@live.com";
                    vm.password = "a";
                }
            }
        },
    });
</script>
@endsection

