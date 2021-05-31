<template>
   <modal ref="myModal">
        <template #body>
            <form method="post" onsubmit="return false;" @submit.prevent='save'>
                <div class="form-group row">
                    <div class="col">
                        <div class="form-material">
                            <label for="">name</label>
                            <input type="text" class="form-control" :class="{ 'is-invalid': form.errors.has('name') }" v-model="form.name">
                            <div v-if="form.errors.has('name')" v-html="form.errors.get('name')" />
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
            editMode: false,
            form: new Form({
                id:'',
                name : '',
            }),
            modal: null,
            canHide: false,
            forceHide: false
        }
    },
    mounted:function()
    {
        // this.initModal();
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
        delete(id)
        {},
        editModal(id)
        {
            this.editMode = true;
            
            this.form.reset();
            this.$refs.myModal.title = 'Edit';
            this.$refs.myModal.show()


            this.$refs.myModal.idle(true);

            axios.get(`api/rooms/${id}`)
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

                        if (confirm(this.trans('generic.form_leave_warning')))
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

                    $(this.modal).modal('hide')
                }
            }
            // modal wants to hide

            
        },
        initListeners()
        {
            // Observer.$on('users.create', data => {
            //     this.createModal()
            // })
            // Observer.$on('users.edit', data => {
            //     this.editModal(data)
            // })

            // $(this.modal).on('hide.bs.modal', e =>{ this.hideHandler(e) })
            // $(this.modal).on('hidden.bs.modal', e =>{ this.hiddenHandler(e) })
        },
        initModal()
        {
            this.modal = $(this.$parent.$el).modal({show:false})
            this.modalComp = this.$parent;
        },
        initForm(d)
        {
            if(d)
            {
                let data = {
                    id: d.id,
                    name: d.name,
                };

                this.form = new Form(data)
            }
            else
            {
                this.form = new Form({
                    id:'',
                    name : '',
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


            this.form.post('api/rooms')
            .then( data =>{
                this.canHide = true;
                this.$refs.myModal.hide()
                this.$emit('saved')
            })
            .catch((err)=>{})
            .then(()=>{this.$refs.myModal.idle(false)});
        },
        update()
        {
            this.$refs.myModal.idle(true);

            this.form.put('api/rooms/'+this.form.id)
            .then(() => {
                // success
                this.canHide = true;
                this.$refs.myModal.hide()
                this.$emit('saved')
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
                            alert(`${prop} changed`)
                            // this.canHide = false
                            dirty = true;
                            break;
                        }
                    }
                    else if(this.form[prop] != oData[prop])
                    {
                        alert(`${prop} changed`)
                        // this.canHide = false
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