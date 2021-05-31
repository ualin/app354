<template>
   <modal ref="myModal">
        <template #body>
            <form method="post" onsubmit="return false;" @submit.prevent='save'>
                <div class="form-group row">
                    <div class="col-6">
                        <div class="form-material">
                            <label for="">Name</label>
                            <input type="text" class="form-control" :class="{ 'is-invalid': form.errors.has('name') }" v-model="form.name">
                            <div v-if="form.errors.has('name')" v-html="form.errors.get('name')" />
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-material">
                            <label for="">Rooms</label>
                            <select multiple size="10" class="form-control" :class="{ 'is-invalid': form.errors.has('rooms') }" v-model="form.rooms">
                                <option v-for="room in rooms" :key="room.id" :value='room.id'>{{ room.name }}</option>
                            </select>
                            <div v-if="form.errors.has('rooms')" v-html="form.errors.get('rooms')" />
                        </div>
                    </div>
                </div>
            </form>
        </template>
        <template #footer>
            <div>
                <button type="button" class="btn btn-alt-success" @click="save">Save</button>
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
                rooms: [],
                editMode: false,
                form: new Form({
                    id:'',
                    name : '',
                    rooms: []
                }),
                modal: null,
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

                axios.get(`api/departments/${id}`)
                .then((r1)=>{
                    
                    let data = r1.data;

                    axios.get(`api/departments/${id}/rooms`)
                    .then( r2=> data.rooms = r2.data)
                    
                    this.initForm(data)
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
                axios.get('/api/rooms')
                .then(r=>{this.rooms = r.data})
            },
            initForm(d)
            {
                if(d)
                {
                    let data = {
                        id: d.id,
                        name : d.name,
                        rooms: d.rooms
                    };

                    this.form = new Form(data)
                }
                else
                {
                    this.form = new Form({
                        id:'',
                        name : '',
                        rooms: []
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


                this.form.post('api/departments')
                .then( data =>{
                    this.canHide = true;
                    this.$emit('saved');
                    this.$refs.myModal.hide()
                })
                .catch((err)=>{})
                .then(()=>{this.$refs.myModal.idle(false)});
            },
            update()
            {
                this.$refs.myModal.idle(true);

                this.form.put('api/departments/'+this.form.id)
                .then(() => {
                    // success
                    this.canHide = true;
                    this.$emit('saved');
                    this.$refs.myModal.hide()
                })
                .catch(() => {})
                .then(()=>{this.$refs.myModal.idle(false)});
            },
            validateDirty()
            {
                let oData = this.form.originalData;
                let dirty = false;

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