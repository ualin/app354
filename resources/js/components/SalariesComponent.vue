<template>
<div>
    <div class="row p-3">
        <div class="col">
            <button type='button' class="btn btn-outline-secondary" @click='handleCreateClick'>Adauga</button>
        </div>
        <div class="col">
            <label for="">Employees</label>
            <select class="form-control" @change="handleEmployeeChange" v-model="currentEmployee">
                <option v-for="employee in employees" :key="employee.id" :value="employee.id">{{employee.firstname}} {{employee.lastname}}</option>
            </select>
        </div>
    </div>
    <table class="table">
        <thead>
            <tr>
                <th></th>
                <th scope="col">#</th>
                <th scope="col">Gross</th>
                <th scope="col">Net</th>
                <th scope="col">Month</th>
                <th scope="col">Year</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="salary in salaries" v-bind:key="salary.id">
                <td>
                    <button type='button' class="btn btn-outline-secondary" @click='handleEditClick(salary.id)'>Edit</button>
                </td>
                <td>{{salary.id}}</td>
                <td>{{salary.gross}}</td>
                <td>{{salary.net}}</td>
                <td>{{salary.month}}</td>
                <td>{{salary.year}}</td>
            </tr>   
        </tbody>
    </table>
    <salaryform ref="myForm" @saved='renderList'/>
</div>
</template>
<script>

import salaryform from './SalariesForm.vue';

export default {
    components: {salaryform},
    data(){
        return {
            salaries: [],
            employees: [],
            currentEmployee: null
        }
    },
    mounted(){
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
        handleEmployeeChange(e)
        {
            this.renderList()
        },
        init()
        {
            axios.get(`/api/employees`)
            .then(r=>{
                this.employees = r.data
                this.$refs.myForm.employees = r.data
            })
        },
        renderList(){
            if(this.currentEmployee)
            axios.get(`/api/employees/${this.currentEmployee}/salary`)
            .then(r=>{
                this.salaries = r.data
            })
        }
    }
}
</script>