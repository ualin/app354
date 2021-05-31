<template>
   <modal ref="myModal">
        <template #body>
            <form method="post" onsubmit="return false;" @submit.prevent='save'>
                <div class="form-group row">
                    <div class="col-6">
                        <div class="form-material">
                            <label for="">name</label>
                            <input type="text" class="form-control" :class="{ 'is-invalid': form.errors.has('name') }" v-model="form.name">
                            <div v-if="form.errors.has('name')" v-html="form.errors.get('name')" />
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-material">
                            <label for="">email</label>
                            <input type="text" class="form-control" :class="{ 'is-invalid': form.errors.has('email') }" v-model="form.email">
                            <div v-if="form.errors.has('email')" v-html="form.errors.get('email')" />
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-6">
                        <div class="form-material">
                            <label for="">pass</label>
                            <input type="password" class="form-control" :class="{ 'is-invalid': form.errors.has('password') }" v-model="form.password">
                            <div v-if="form.errors.has('password')" v-html="form.errors.get('password')" />
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-material">
                            <label for="">pass confirm</label>
                            <input type="password" class="form-control" :class="{ 'is-invalid': form.errors.has('password_confirmation') }" v-model="form.password_confirmation">
                            <div v-if="form.errors.has('password_confirmation')" v-html="form.errors.get('password_confirmation')" />
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
                editMode: false,
                users : {},
                locationAccessIds: [],
                customerAccessIds: [],
                form: new Form({
                    id:'',
                    name : '',
                    email: '',
                    password: '',
                    password_confirmation: '',
                }),
                canHide: false,
            }
        },
        mounted:function()
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
            editModal(userId)
            {
                this.editMode = true;
                
                this.form.reset();
                this.$refs.myModal.title = 'Edit';
                this.$refs.myModal.show()


                this.idle(true);

                axios.get(`api/users/${userId}`)
                .then((res)=>{
                    
                    res.data.password = res.data.password ?? '';
                    res.data.password_confirmation = res.data.password;

                    this.initForm(res.data)
                    // this.form.fill(res.data);
                    this.form.fill(this.form.data());

                    this.idle(false);
                    
                })
            },
            initListeners()
            {},
            initForm(user)
            {
                if(user)
                {
                    let data = {
                        id:user.id,
                        name : user.name,
                        email: user.email,
                        password: user.password ?? '',
                        password_confirmation: user.password_confirmation ?? '',
                    };

                    this.form = new Form(data)
                }
                else
                {
                    this.accessIds = [];
                    this.form = new Form({
                        id:'',
                        name : '',
                        email: '',
                        password: '',
                        password_confirmation: '',
                    })
                }
            },
            idle(bool)
            {},
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
                this.idle(true);
                let data = this.form.data();


                this.form.post('api/users')
                .then( data =>{
                    this.canHide = true;
                    this.$refs.myModal.hide()
                    this.$emit('saved')
                })
                .catch((err)=>{})
                .then(()=>{this.idle(false)});
            },
            update()
            {
                this.idle(true);

                switch (this.form.access_type) {
                    case 'customers':
                        this.form.access_id = this.form.customer_access_id;    
                    break;
                    case 'locations':
                        this.form.access_id = this.form.location_access_id;    
                    break;
                }

                this.form.put('api/users/'+this.form.id)
                .then(() => {
                    // success
                    this.canHide = true;
                    this.$refs.myModal.hide()
                    this.$emit('saved')
                })
                .catch(() => {})
                .then(()=>{this.idle(false)});
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