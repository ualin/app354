<template>
   <modal ref="myModal">
        <template #body>
            <form method="post" onsubmit="return false;" @submit.prevent='save'>
                <div class="form-group row">
                    <div class="col">
                        <div class="form-material">
                            <label for="">Employee</label>
                            <select class="form-control" :class="{ 'is-invalid': form.errors.has('employee_id') }" v-model="form.employee_id">
                                <option v-for="employee in employees" :value="employee.id" :key="employee.id">{{employee.firstname}} {{employee.lastname}}</option>
                            </select>
                            <div v-if="form.errors.has('date')" v-html="form.errors.get('date')" />
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col">
                        <div class="form-material">
                            <label for="">Month</label>
                            <select class="form-control" :class="{ 'is-invalid': form.errors.has('month') }" v-model="form.month">
                                <option v-for="m in [1,2,3,4,5,6,7,8,9,10,11,12]" :value="m" :key="m">{{m}}</option>
                            </select>
                            <div v-if="form.errors.has('month')" v-html="form.errors.get('month')" />
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-material">
                            <label for="">Year</label>
                            <input type="number" min='0' class="form-control" :class="{ 'is-invalid': form.errors.has('year') }" v-model="form.year">
                            <div v-if="form.errors.has('year')" v-html="form.errors.get('year')" />
                        </div>
                    </div>
                </div>
               <div class="form-group row">
                    <div class="col-6">
                        <div class="form-material">
                            <label for="">gross</label>
                            <input type="number" step="0.01" min="0" class="form-control" :class="{ 'is-invalid': form.errors.has('gross') }" v-model="form.gross">
                            <div v-if="form.errors.has('gross')" v-html="form.errors.get('gross')" />
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-material">
                            <label for="">net</label>
                            <input type="number" step="0.01" min="0" class="form-control" :class="{ 'is-invalid': form.errors.has('net') }" v-model="form.net">
                            <div v-if="form.errors.has('net')" v-html="form.errors.get('net')" />
                        </div>
                    </div>
                </div>
            </form>
        </template>
        <template #footer>
            <div>
                <button type="button" class="btn btn-alt-success" @click='save'>Save</button>
                <button type='button' class="btn btn-alt-secondary" data-dismiss="modal">Cancel</button>
            </div>
        </template>
    </modal>
</template>
<script>
import modal from './ModalComponent.vue';
    export default {
        components:{modal},
        data() {
            return {
                employees: [],
                editMode: false,
                form: new Form({
                    id:'',
                    employee_id:'',
                    net:'',
                    gross:'',
                    month:'',
                    year:'',
                }),
                modal: null,
                canHide: false,
            }
        },
        mounted()
        {
            this.initListeners()
        },
        methods: {

            createModal()
            {
                this.editMode = false;
                this.initForm();
                this.form.reset();
                this.$refs.myModal.title = 'Create';

                this.$refs.myModal.show()
            },
            editModal(id)
            {
                this.editMode = true;
                
                this.form.reset();
                this.$refs.myModal.title = 'Edit';
                this.$refs.myModal.show()


                this.$refs.myModal.idle(true);

                axios.get(`api/salaries/${id}`)
                .then((res)=>{
                    
                    this.initForm(res.data)
                    // this.form.fill(res.data);
                    this.form.fill(this.form.data());

                    this.$refs.myModal.idle(false);
                    
                })
            },
            hiddenHandler(e)
            {
                this.canHide = false;
            },
            hideHandler(e)
                {
                    if(this.canHide)
                    {}
                    else
                    { 
                        e.preventDefault();

                        if(this.validateDirty() == true)
                        {
                            // but form is changed

                                if (confirm('Data unsaved. Dismiss?'))
                                {
                                    this.canHide = true;
                                    this.$refs.myModal.hide()
                                }
                                else {}
                        }
                        else
                        {
                            // form is unchanged. It's ok, modal can hide

                            this.canHide = true;

                            this.$refs.myModal.hide()
                        }
                    }
                    // modal wants to hide
                },
            initListeners()
            {
                $(this.$refs.myModal.$el).on('hide.bs.modal', e =>{ this.hideHandler(e) })
                $(this.$refs.myModal.$el).on('hidden.bs.modal', e =>{ this.hiddenHandler(e) })
            },
            init()
            {
            },
            initForm(d)
            {
                if(d)
                {
                    let data = {
                        id: d.id,
                        employee_id: d.employee_id,
                        net: d.net,
                        gross: d.gross,
                        month: d.month,
                        year: d.year,
                    };

                    this.form = new Form(data)
                }
                else
                {
                    this.form = new Form({
                        id: '',
                        employee_id:'',
                        net:'',
                        gross:'',
                        month:'',
                        year:'',
                    })
                }
            },
            save()
            { 
                if(this.editMode)
                {
                    this.update();
                }
                else
                {
                    this.store();
                }
            },
            store()
            {
                this.$refs.myModal.idle(true);

                this.form.post('api/salaries')
                .then( data =>{
                    this.canHide = true;
                    this.$emit('saved');
                    this.$refs.myModal.hide();
                })
                .catch((err)=>{if(err.response.status ===500) alert('Possible duplicate values!')})
                .then(()=>{this.$refs.myModal.idle(false)});
            },
            update()
            {
                this.$refs.myModal.idle(true);

                this.form.put('api/salaries/'+this.form.id)
                .then(() => {
                    this.canHide = true;
                    this.$emit('saved');
                    this.$refs.myModal.hide();
                })
                .catch(() => {})
                .then(()=>{this.$refs.myModal.idle(false)});
            },
            validateDirty()
            {
                let oData = this.form.originalData;
                let dirty = false;
                // this.canHide = true;

                for( const prop in oData)
                {
                    if(this.form.hasOwnProperty(prop))
                    {
                        if(typeof this.form[prop] === 'object')
                        {
                            if(JSON.stringify(this.form[prop]) != JSON.stringify(oData[prop]))
                            {
                                // alert(`${prop} changed`)
                                dirty = true;
                                break;
                            }
                        }
                        else if(this.form[prop] != oData[prop])
                        {
                            // alert(`${prop} changed`)
                            dirty = true;
                            break;
                        }
                    }
                }

                return dirty;
            },
        },
    }
</script>