<template>
    <div>
        <div class="p-3">

            <button type="button" class="btn btn-outline-primary" @click='handleCreateClick'>Adauga</button>
        </div>
        <table class="table">
            <thead>
                <tr>
                    <th></th>
                    <th scope="col">#</th>
                    <th scope="col">Name</th>
                    <th scope="col">Rooms</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="department in departments" v-bind:key="department.id">
                    <td>
                        <button class="btn btn-outline-secondary" @click='handleEditClick(department.id)'>Edit</button>
                    </td>
                    <td>{{department.id}}</td>
                    <td>{{department.name}}</td>
                    <td>{{department.rooms}}</td>
                </tr>
            </tbody>
        </table>
        <departmentform ref="myForm" @saved='renderList'/>
    </div>
</template>
<script>

import departmentform from './DepartmentsForm.vue';

export default {
    components: {departmentform},
    data(){
        return {
            departments: []
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
            axios.get('/api/departments')
            .then(r=>{
                this.departments = r.data
            })
        }
    }
}
</script>