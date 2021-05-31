<template>
<div>
    <button type="button" class="btn btn-outline-secondary" @click='handleCreateClick'>Adauga</button>
    <table class="table">
        <thead>
            <tr>
                <th></th>
                <th scope="col">#</th>
                <th scope="col">Name</th>
                <th scope="col">Email</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="user in users" v-bind:key="user.id">
                <td>
                    <button class="btn btn-outline-secondary" @click='handleEditClick(user.id)'>Edit</button>
                </td>
                <td>{{user.id}}</td>
                <td>{{user.name}}</td>
                <td>{{user.email}}</td>
            </tr>
        </tbody>
    </table>
    <userform ref="myForm" @saved='renderList'/>
</div>
</template>
<script>

import userform from './UsersForm.vue';

export default {
    components: {userform},
    data(){
        return {
            users: []
        }
    },
    mounted(){
        this.renderList()
    },
    methods: {
        handleCreateClick()
        {
            this.$refs.myForm.createModal()
        },
        handleEditClick(id)
        {
            this.$refs.myForm.editModal(id)
        },
        renderList(){
            axios.get('/api/users')
            .then(r=>{
                this.users = r.data
            })
        }
    }
}
</script>