<template>
<div>
    <button type='button' class="btn btn-outline-secondary" @click='handleCreateClick'>Adauga</button>
    <table class="table">
        <thead>
            <tr>
                <th></th>
                <th scope="col">#</th>
                <th scope="col">Name</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="room in rooms" v-bind:key="room.id">
                <td>
                    <button type='button' class="btn btn-outline-secondary" @click='handleEditClick(room.id)'>Edit</button>
                    <button type='button' class="btn btn-outline-secondary" @click='handleDeleteClick(room.id)'>Delete</button>
                </td>
                <td>{{room.id}}</td>
                <td>{{room.name}}</td>
            </tr>
        </tbody>
    </table>
    <roomform ref="myForm" @saved='renderList'/>
</div>
</template>
<script>

import roomform from './RoomsForm.vue';

export default {
    components: {roomform},
    data(){
        return {
            rooms: []
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
        handleDeleteClick(id)
        {
            if(confirm('Are you sure?')){
                this.deleteEntity(id)
            }
        },
        deleteEntity(id){
            axios.delete(`/api/rooms/${id}`)
            .then(_=> this.renderList())
        },
        renderList(){
            axios.get('/api/rooms')
            .then(r=>{
                this.rooms = r.data
            })
        }
    }
}
</script>