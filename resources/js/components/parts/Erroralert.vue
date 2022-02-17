<template>
<div>
    <div  class="alert alert-danger">
        <ul>
            <li v-for="(value, index) in validationErrors" :key="index">
                {{value}}
            </li>
        </ul>
    </div>

</div>
</template>

<script>
export default {
    props: ['errors'],
    computed: {
        validationErrors(){
           let errors = Object.values(this.errors)
            errors = errors.flat()
            return errors
        }

    }
}
</script>

<style scoped>

</style>
