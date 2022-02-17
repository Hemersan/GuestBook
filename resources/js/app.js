require('./bootstrap');

window.Vue = require('vue').default;


import App from './components/App'
import Navbar from './components/parts/Navbar'
import AuthForm from './components/parts/Authorization'
import Posts from './components/pages/Index'
import Erroralert from "./components/parts/Erroralert";

Vue.component('navbar', Navbar);
Vue.component('posts', Posts);
Vue.component('authform', AuthForm);
Vue.component('error-alert', Erroralert)

const app = new Vue({
    el: '#app',
    render: h => h(App)
});
