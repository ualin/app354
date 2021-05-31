<template>
<div>
    <button type='button' class="btn btn-outline-primary" @click='handleCreateClick'>Adauga</button>
    <table class="table">
        <thead>
            <tr>
                <th></th>
                <th scope="col">#</th>
                <th scope="col">Name</th>
                <th scope="col">Birthday</th>
                <th scope="col">Is manager</th>
                <th scope="col">Department</th>
                <th scope="col">Room</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="employee in employees" v-bind:key="employee.id">
                <td>
                    <button type='button' class="btn btn-outline-secondary" @click='handleEditClick(employee.id)'>Edit</button>
                    <button type='button' class="btn btn-outline-secondary" @click='handleDeleteClick(employee.id)'>Delete</button>
                </td>
                <td>{{employee.id}}</td>
                <td>{{employee.firstname}} {{employee.lastname}}</td>
                <td>{{employee.birthday}}</td>
                <td>{{employee.is_manager}}</td>
                <td>{{employee.department_name}}</td>
                <td>{{employee.room_name}}</td>
            </tr>
        </tbody>
    </table>
    <employeesform ref="myForm" @saved='renderList'/>
</div>
</template>
<script>

import employeesform from './EmployeesForm.vue';

export default {
    components: {employeesform},
    data(){
        return {
            employees: [],
        }
    },
    mounted(){
        this.renderList()
        this.init()
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
            axios.delete(`/api/employees/${id}`)
            .then(_=> this.renderList())
        },
        init(){
            axios.get('/api/departments')
            .then( r=>{ 
            
                this.$refs.myForm.departments = r.data
            })
        },
        renderList(){
            axios.get('/api/employees')
            .then(r=>{
                this.employees = r.data
            })
        }
    }
}
</script>