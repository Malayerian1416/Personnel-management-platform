<template>
    <div>
        <input type="file" hidden :class="`form-control text-center ${typeof error_class !== 'undefined' ? error_class : ''}`" v-on:change="SelectFile" :id="id" :name="name" :accept="file_extensions">
        <input :disabled="disabled" type="text" :class="`form-control b-form iransans text-center file_selector_box font-size-lg d-inline-block ${typeof input_class !== 'undefined' ? input_class : ''} ${typeof error_class !== 'undefined' ? error_class : ''}`" v-on:click="PopUpFileBrowser" :id="filename_id" readonly :value="filename">
        <div class="form-text iransans green-color d-block mt-1" v-text="information"></div>
        <span v-if="error_message" class="invalid-feedback iransans small_font" role="alert">{{ error_message }}</span>
    </div>
</template>

<script>
export default {
    name: "SingleFileBrowser",
    mounted() {
        if (this.$props.already)
            this.filename = "فایل بارگذاری شده است";
    },
    data() {
        return {
            filename: "انتخاب کنید",
            information: typeof this.error_class === "undefined" ? "فرمت های قابل قبول " + `(${this.$props.accept.toString()})` + " / حداکثر سایز قابل قبول (" + this.formatBytes(this.$props.size) +")" : '',
        }
    },
    computed : {
        file_extensions : function (){
            return this.$props.accept.map(extension => '.' + extension).join(',');
        },
        name: function (){
            return this.$props.file_box_name ? this.$props.file_box_name : "upload_file";
        },
        id: function (){
            return this.$props.file_box_id ? this.$props.file_box_id : "upload_file";
        },
        filename_id (){
            return this.$props.filename_box_id ? this.$props.filename_box_id : "file_browser_box";
        },
    },
    props:["accept","size","already","file_box_name","file_box_id","filename_box_id","input_class","error_class","error_message","disabled"],
    methods:{
        PopUpFileBrowser(e){
            $(e.target).closest('div').find('input[type="file"]').click();
        },
        SelectFile(e){
            const self = this;
            let valid_ext = this.$props.accept;
            let file_ext = e.target.files[0].name.split('.').pop();
            let file_size = parseInt(e.target.files[0].size);
            if (valid_ext.indexOf(file_ext.toLowerCase()) === -1){
                bootbox.alert({
                    "message": "فرمت فایل مورد قبول نمی باشد",
                    buttons: {
                        ok: {
                            label: 'قبول'
                        }
                    },
                });
                this.$nextTick(() => {
                    self.filename = "انتخاب کنید";
                });
                this.$emit("file_deselected",e.target.files[0]);
            }
            else if (file_size > this.$props.size){
                bootbox.alert({
                    "message": "حجم فایل انتخاب شده بیشتر از " + this.formatBytes(self.$props.size).toString() + " می باشد",
                    buttons: {
                        ok: {
                            label: 'قبول'
                        }
                    },
                });
                this.$nextTick(() => {
                    self.filename = "انتخاب کنید";
                });
                this.$emit("file_deselected",e.target.files[0]);
            }
            else {
                this.$nextTick(() => {
                    self.filename = e.target.files[0].name;
                });
                this.$emit("file_selected",e.target.files[0]);
            }
        },
        formatBytes(bytes, decimals = 2) {
            if (!+bytes) return '0'
            const k = 1024
            const dm = decimals < 0 ? 0 : decimals
            const sizes = ['بایت', 'کیلوبایت', 'مگابایت']
            const i = Math.floor(Math.log(bytes) / Math.log(k))
            return `${parseFloat((bytes / Math.pow(k, i)).toFixed(dm))} ${sizes[i]}`
        }
    }
}
</script>

<style scoped>
.file_selector_box{
    cursor: pointer;
}
.login-input-text{
    font-size: 11px!important;
}
</style>
