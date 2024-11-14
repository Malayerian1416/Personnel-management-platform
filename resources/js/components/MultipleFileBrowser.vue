<template>
    <div>
        <input type="file" hidden :class="`form-control text-center ${typeof error_class !== 'undefined' ? error_class : ''}`" v-on:change="SelectFiles" multiple :id="id" :name="name" :accept="file_extensions">
        <input type="text" :disabled="disabled" :class="`form-control b-form iransans text-center file_selector_box font-size-lg d-inline-block ${typeof input_class !== 'undefined' ? input_class : ''} ${typeof error_class !== 'undefined' ? error_class : ''}`" v-on:click="PopUpFileBrowser" :id="filename_id" readonly :value="filename">
        <div class="form-text iransans green-color d-block" v-text="information"></div>
        <span v-if="error_message" class="invalid-feedback iransans small_font" role="alert">{{ error_message }}</span>
    </div>
</template>

<script>
export default {
    name: "MultipleFileBrowser",
    mounted() {
        if (this.$props.already)
            this.filename = "فایل(ها) بارگذاری شده است"
    },
    data() {
        return {
            filename: "انتخاب کنید",
            information: typeof this.error_class === "undefined" ? "* فرمت های قابل قبول " + `(${this.$props.accept.toString()})` + " / حداکثر سایز قابل قبول (" + this.formatBytes(this.$props.size) +")" : '',
        }
    },
    computed : {
        file_extensions : function (){
            return this.$props.accept.map(extension => '.' + extension).join(',');
        },
        name: function (){
            return this.$props.file_box_name ? this.$props.file_box_name : "upload_files[]";
        },
        id: function (){
            return this.$props.file_box_id ? this.$props.file_box_id : "upload_files";
        },
        filename_id (){
            return this.$props.filename_box_id ? this.$props.filename_box_id : "file_browser_box";
        }
    },
    props:["accept","size","already","file_box_name","file_box_id","filename_box_id","input_class","error_class","error_message","disabled"],
    methods:{
        PopUpFileBrowser(e){
            $(e.target).closest('div').find('input[type="file"]').click();
        },
        SelectFiles(e){
            let valid_ext = this.$props.accept;
            let error_ext = [];
            let error_size = [];
            let file_names = [];
            let ext_str = '';
            let size_str = '';
            for (let i = 0; i < e.target.files.length; i++) {
                let file_ext = e.target.files[i].name.split('.').pop();
                let file_size = parseInt(e.target.files[i].size);
                if (valid_ext.indexOf(file_ext.toLowerCase()) === -1)
                    error_ext.push(e.target.files[i].name)
                if (file_size > this.$props.size)
                    error_size.push(`${e.target.files[i].name}(${this.formatBytes(file_size)})`);
                file_names.push(e.target.files[i].name);
            }
            if (error_ext.length > 0)
                ext_str = "<h6 style='color: red'>فرمت فایل(های) ذیل مورد قبول نمی باشد:</h6>" + error_ext.toString();
            if (error_size.length > 0)
                size_str = "<h6 style='color: red'>حجم فایل(های) ذیل مورد قبول نمی باشد:</h6>" + error_size.toString();
            if (error_size.length > 0 || error_ext.length > 0) {
                bootbox.alert({
                    "message": ext_str + size_str,
                    buttons: {
                        ok: {
                            label: 'قبول'
                        }
                    },
                });
                this.filename = 'انتخاب کنید';
            } else
                this.filename = file_names.toString();
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
