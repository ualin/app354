<template>
   <modal ref="myModal">
        <template #body>
            <form method="post" onsubmit="return false;" @submit.prevent='save'>
               <div class="form-group row">
                    <div class="col-6">
                        <div class="form-material">
                            <label for="">firstname</label>
                            <input type="text" class="form-control" :class="{ 'is-invalid': form.errors.has('firstname') }" v-model="form.firstname">
                            <div v-if="form.errors.has('firstname')" v-html="form.errors.get('firstname')" />
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-material">
                            <label for="">lastname</label>
                            <input type="text" class="form-control" :class="{ 'is-invalid': form.errors.has('lastname') }" v-model="form.lastname">
                            <div v-if="form.errors.has('lastname')" v-html="form.errors.get('lastname')" />
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-6">
                        <div class="form-material">
                            <label for="">email</label>
                            <input type="text" class="form-control" :class="{ 'is-invalid': form.errors.has('email') }" v-model="form.email">
                            <div v-if="form.errors.has('email')" v-html="form.errors.get('email')" />
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-material">
                            <label for="">Birthday</label>
                            <input type="date" class="form-control" :class="{ 'is-invalid': form.errors.has('birthday') }" v-model="form.birthday">
                            <div v-if="form.errors.has('birthday')" v-html="form.errors.get('birthday')" />
                        </div>
                    </div>
                </div>
                 <div class="form-group row">
                     <div class="col-6">
                        <div class="form-material">
                            <label for="">Department</label>
                             <select class="form-control" :class="{ 'is-invalid': form.errors.has('department_id') }" v-model="form.department_id" @change='handleDepartmentChange'>
                                <option value=""></option>
                                <option v-for="department in departments" :key="department.id" :value='department.id'>{{ department.name }}</option>
                            </select>
                            <div v-if="form.errors.has('department_id')" v-html="form.errors.get('department_id')" />
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-material">
                            <label for="">Room</label>
                             <select class="form-control" :class="{ 'is-invalid': form.errors.has('department_id') }" v-model="form.room_id">
                                <option value=""></option>
                                <option v-for="room in rooms" :key="room.id" :value='room.id'>{{ room.name }}</option>
                            </select>
                            <div v-if="form.errors.has('room_id')" v-html="form.errors.get('room_id')" />
                        </div>
                    </div>
                </div>
                 <div class="form-group row">
                   <div class="col-6">
                        <div class="form-material">
                            <label for="">Is manager</label>
                            <input type="checkbox" class="form-control" :class="{ 'is-invalid': form.errors.has('is_manager') }" v-model="form.is_manager">
                            <div v-if="form.errors.has('is_manager')" v-html="form.errors.get('is_manager')" />
                        </div>
                    </div>
                </div>
            </form>
        </template>
        <template #footer>
            <div>
                <button type="submit" class="btn btn-alt-success" @click='save'>Save</button>
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
            departments: [],
            rooms: [],
            editMode: false,
            form: new Form({
                id:'',
                firstname : '',
                lastname : '',
                email : '',
                birthday : '',
                is_manager : false,
                department_id: null,
                room_id: null
            }),
            canHide: true,
        }
    },
    mounted:function()
    {
        this.init();
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

            axios.get(`api/employees/${id}`)
            .then((r)=>{
                
                this.initForm(r.data)
                this.form.fill(this.form.data());

                this.handleDepartmentChange()

                this.$refs.myModal.idle(false);
                
            })
        },
        handleDepartmentChange(e)
        {
            this.form.room_id = null;

            if(!this.form.department_id)
            {
                this.rooms = [];
                return;
            }

            axios.get(`/api/departments/${this.form.department_id}/rooms`)
            .then( r => this.rooms = r.data)
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
                    firstname : d.firstname,
                    lastname : d.lastname,
                    email : d.email,
                    birthday : d.birthday,
                    is_manager : d.is_manager,
                    department_id: d.department_id,
                    room_id: d.room_id,
                };

                this.form = new Form(data)
            }
            else
            {
                this.form = new Form({
                    id: '',
                    firstname : '',
                    lastname : '',
                    email : '',
                    birthday : '',
                    is_manager : false,
                    department_id: null,
                    room_id: null,
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
            let data = this.form.data();


            this.form.post('api/employees')
            .then( data =>{
                this.canHide = true;
                this.$emit('saved')
                this.$refs.myModal.hide();
            })
            .catch((err)=>{})
            .then(()=>{this.$refs.myModal.idle(false)});
        },
        update()
        {
            this.$refs.myModal.idle(true);

            this.form.put('api/employees/'+this.form.id)
            .then(() => {
                this.canHide=true;
                this.$emit('saved')
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