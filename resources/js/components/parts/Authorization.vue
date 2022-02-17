<template>



    <div class="row">
        <error-alert v-if="validationErrors" :errors="validationErrors"></error-alert>

        <div id="LoginForm" v-if="!haveAuth" class="row d-inline-flex mt-2 mt-md-0 ms-md-auto">
            <div id="AuthForm" v-if="Authsee" class="col">
                <h6 class="d-flex justify-content-center m-1">Вход</h6>
                <div class="form-group">
                    <div class="row" >
                        <div class="col">
                            <input type="email" v-model="form.email" class="form-control"
                                   placeholder="name@example.com">
                        </div>
                        <div class="col">
                            <input type="password" v-model="form.password" class="form-control"
                                   placeholder="Пароль">
                        </div>
                        <div>

                        </div>
                    </div>
                    <div class="row m-1">
                        <div class="col d-flex justify-content-center">
                            <button  type="submit" @click="onLogin"
                                     class="btn btn-outline-primary btn-sm">Войти</button>
                        </div>
                        <div class="col d-flex justify-content-center">
                            <button  type="submit" class="btn btn-outline-primary btn-sm"
                                     v-on:click="Authsee = false, Regsee=true" >Регистрация</button>
                        </div>
                    </div>
                </div>
            </div>
            <div id="RegistrationForm" v-if="Regsee"  class="col">
                <h6 class="d-flex justify-content-center m-1">Регистрация</h6>



                <div class="form-group">
                    <div class="row" >
                        <div class="row m-1">
                            <div class="col">
                                <input type="email" v-model="form.email" class="form-control"
                                       placeholder="name@example.com">
                            </div>
                            <div class="col">
                                <input type="text" v-model="form.user_name" class="form-control" placeholder="Имя">
                            </div>
                        </div>
                        <div class="row m-1">
                            <div class="col">
                                <input type="password" v-model="form.password" class="form-control"
                                       placeholder="Пароль">
                            </div>
                            <div class="col">
                                <input type="password" v-model="form.password_confirmation" class="form-control"
                                       placeholder=" Повторите Пароль">
                            </div>
                        </div>
                    </div>
                    <div class="row m-1">
                        <div class="col d-flex justify-content-center">
                            <button  type="submit" @click="onRegister"
                                     class="btn btn-outline-primary btn-sm">Зрегистрироваьтся</button>
                        </div>
                        <div class="col d-flex justify-content-center">
                            <button  type="submit" class="btn btn-outline-primary btn-sm"
                                     v-on:click="Authsee = true, Regsee=false">Назад</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="btn-group" v-if="haveAuth">
            <button type="button" class="btn btn-info dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                {{ form.user_name }}
            </button>
            <ul class="dropdown-menu">
                <li><a v-if="form.status === 'admin'" class="dropdown-item" href="/posts/export">Экспортировать посты в Exel</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="#" @click="onLogout">Выход</a></li>
            </ul>
        </div>

    </div>

</template>

<script>
export default {
    data() {
        return{
            haveAuth: false,
            Authsee: true,
            Regsee: false,
            form:{
                user_name: null,
                email: null,
                password: null,
                password_confirmation: null,
                status: null
            },
            validationErrors: ''
        }
    },
    methods:{
        onRegister(){
                axios.post('api/register', {
                    name: this.form.user_name,
                    email: this.form.email,
                    password: this.form.password,
                    password_confirmation: this.form.password_confirmation
                })
                    .then(response => {
                        this.onLogin()
                    })
                    .catch(error => {

                        if (error.response.status === 422 ) {
                        this.validationErrors = error.response.data.errors;

                        }
                    })

        },
        onLogin(){
            this.errors = {};

                axios.post('api/login', {
                    email: this.form.email,
                    password: this.form.password
                })
                    .then(response => {
                        if (response.data.success === true) {
                            this.form.user_name = response.data.name;
                            this.form.status = response.data.status;
                            this.sendData(response.data.name, response.data.status);
                            this.haveAuth = true;
                            this.validationErrors = '';
                        } else if (response.data.status === 422) {
                            this.validationErrors = response.data.errors
                        }
                    })

                .catch(error=>{
                    if (error.response.status === 422 ) {
                        this.validationErrors = error.response.data.errors;
                    }
                })
        },
        sendData(name,status){
            this.$emit ('getData', name, status)
        },
        onLogout(){
            this.errors = {};
            axios.post('api/login', this.form)
                .then(response =>{
                    this.form.user_name = null;
                    this.haveAuth = false;
                    this.form.status = null;
                    this.form.email = null;
                    this.form.password = null;
                    this.form.password_confirmation = null;
                    this.sendData(this.form.user_name,this.form.status)
                })
                .catch(error=>{
                    this.errors = error.response.data.errors;
                })

        }

    }
}
</script>

<style scoped>

</style>
