<template>
    <div>
        <authform v-on:getData="getUserName" />
        <br>
        <div class="row">
            <div class="col">
                <h1>Все отзывы</h1>
            </div>
            <div class="col d-flex justify-content-end">
                <button v-if="seeBtnAdd" class="btn btn-warning"
                        @click="seeAddPanel=true, seeBtnAdd=false">Добавить отзыв</button>
            </div>
        </div>
        <div v-if="errored" class="alert alert-warning" role="alert">
            Отзывы не загрузились
        </div>
        <table v-else class="table">
            <thead>
            <tr>
                <th scope="col" hidden>Id</th>
                <th scope="col">Имя</th>
                <th scope="col">Заголовок</th>
                <th scope="col">Отзыв</th>
                <th scope="col">
                    <button @click="doSort()">
                        Дата/Время
                    </button>
                </th>
                <th scope="col"></th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="post in posts" :key="post.id">
                <td>{{post.name}}</td>
                <td>{{post.title}}</td>
                <td>{{post.body}}</td>
                <td>
                    <button type="button" class="btn dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                        {{DataConvert(post.updated_at)}} {{TimeConvert(post.updated_at)}}
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#">Дата обновления: {{DataConvert(post.updated_at)}}
                            {{TimeConvert(post.updated_at)}}</a></li>
                        <li><a class="dropdown-item" href="#">Дата создания: {{DataConvert(post.created_at)}}
                            {{TimeConvert(post.created_at)}} </a></li>
                    </ul>
                </td>


                <td>
                    <div v-if="editTimeCheck(post)">
                    <button  @click="editPost(post)" class="btn btn-success">Редактировать</button>
                    <button @click="delPost(post)" class="btn btn-danger">Удалить</button>
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
                <nav>
                    <ul class="pagination">
                        <li
                            :class="{disabled: !pagination.prev_page_url}"
                            @click.prevent="getPost(pagination.prev_page_url)"
                            class="page-item"><button class="page-link">Назад</button></li>
                        <li class="page-item"><button disabled class="page-link" href="#">
                            Страница {{pagination.current_page}} из {{pagination.last_page}}
                        </button>
                        </li>
                        <li
                            :class="{disabled: !pagination.next_page_url}"
                            @click.prevent="getPost(pagination.next_page_url)"
                            class="page-item"><button class="page-link">Вперед</button>
                        </li>
                    </ul>
                </nav>

        <div>
            <error-alert v-if="validationErrors" :errors="validationErrors"></error-alert>
            <form v-if="seeAddPanel">

                <div class="mb-3">
                    <label for="title" class="form-label">Заголовок</label>
                    <input v-model="post.title" type="text" class="form-control" id="title">
                </div>
                <div class="mb-3">
                    <label for="body" class="form-label">Отзыв</label>
                    <textarea v-model="post.body" class="form-control" id="body" style="height: 100px"></textarea>
                </div>
                        <button type="submit" class="btn btn-primary" @click="addPost">Отправить</button>
            </form>
            <br>
        </div>
    </div>
</template>

<script>
export default {
    data(){
        return{
            user_status: null,
            user_name1: null,
            status_unknown: false,
            status_user: false,
            status_admin: false,
            posts:[],
            post: {
                id: '',
                name: '',
                title: '',
                body: '',
            },
            pagination: {},
            edit: false,
            errored: false,
            seeAddPanel: false,
            seeBtnAdd: true,
            validationErrors: '',
            sortByTime: 'new'
        }
    },
    mounted() {
        this.getPost();
    },
    methods: {
        getPost(page_url){
            this.userStatusCheck();
            page_url = page_url || 'api/posts/take-and-sort'

            axios.post(page_url, {
                sort: this.sortByTime
            })
                .then(response => {
                    this.posts = response.data.data
                    this.makePagination(response.data)
                })
                .catch(error => {
                    console.log(error)
                    this.errored = true;
                })
                .finally(() => this.errored = false)

        },
        makePagination(response){
            let pagination = {
                current_page: response.current_page,
                last_page: response.last_page,
                prev_page_url: response.prev_page_url,
                next_page_url: response.next_page_url
            }
            this.pagination = pagination
        },
        delPost(post){
                axios.delete('api/posts/' + post.id)
                    .then(response => {
                        this.getPost()
                   })
                    .catch(error => console.log(error))
        },
        addPost(){
          if(this.edit === false) {
                axios.post('api/posts', {
                    title: this.post.title,
                    body: this.post.body,
                    name: this.user_name1
                })
                    .then(response => {
                        this.post.title = ''
                        this.post.body = ''
                        this.seeAddPanel=false
                        this.seeBtnAdd=true
                        this.getPost()
                        this.validationErrors = '';
                    })
                    .catch(error => {
                        if (error.response.status === 422 ) {
                            this.validationErrors = error.response.data.errors;
                        }
                    })
          } else {

              axios.put('api/posts/' +this.post.id, {
                  title: this.post.title,
                  body: this.post.body,
                  name: this.user_name1
              })
                  .then(response => {
                      this.post.title = ''
                      this.post.body = ''
                      this.post.name = ''
                      this.seeAddPanel=false
                      this.seeBtnAdd=true
                      this.edit = false

                      this.getPost()
                      this.validationErrors = '';
                  })
                  .catch(error => {

                      if (error.response.status === 422 ) {
                          this.validationErrors = error.response.data.errors;

                      }
                  })
              this.getPost()
          }
        },
        editPost(post){
                this.edit = true
                this.seeAddPanel=true
                this.seeBtnAdd=false
                this.post.id = post.id
                this.post.title = post.title
                this.post.body = post.body
        },
        userStatusCheck(){
            if(this.user_status === null){
                this.status_unknown = true;
                this.status_user = false;
                this.status_admin = false;
            } else if(this.user_status === 'user'){
                this.status_unknown = false;
                this.status_user = true;
                this.status_admin = false;
            } else {
                this.status_unknown = false;
                this.status_user = false;
                this.status_admin = true;
            }
        },
        DataConvert(date){
            return (new Date(date)).toLocaleDateString()
        },
        TimeConvert(date){
            return (new Date(date)).toLocaleTimeString()
        },
        editTimeCheck(post){
            let add_post_date = new Date(post.created_at)
            let today = new Date()
            this.userStatusCheck()
            if (this.status_user === true && post.name === this.user_name1) {
                return (
                        (
                            (today.getHours() * 60 + today.getMinutes()) -
                            (add_post_date.getHours() * 60 + add_post_date.getMinutes()) < 120
                        ) || (
                            (today.getDate() - add_post_date.getDate() === 1) && (
                                (today.getHours() * 60 + 1440 + today.getMinutes()) -
                                (add_post_date.getHours() * 60 + add_post_date.getMinutes()) < 120)
                        )
                    ) && (
                        (today.getDate() === add_post_date.getDate()) || (
                            (today.getDate() - add_post_date.getDate() === 1) && (
                                (today.getHours() * 60 + 1440 + today.getMinutes()) -
                                (add_post_date.getHours() * 60 + add_post_date.getMinutes()) < 120)
                        )
                    ) &&
                    (today.getMonth() === add_post_date.getMonth()) &&
                    (today.getFullYear() === add_post_date.getFullYear());
            }
            else return this.status_admin === true;
        },
        getUserName(username,status){
            this.user_name1 = username
            this.user_status = status
            this.getPost()
        },
        doSort(){
            if (this.sortByTime === 'new'){
                this.sortByTime = 'old'
                this.getPost()
            } else {
                this.sortByTime = 'new'
                this.getPost()
            }
        }
    }
}
</script>

<style scoped>

</style>
