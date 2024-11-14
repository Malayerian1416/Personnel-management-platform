<template>
    <div>
        <div v-show="image_loading === 1" class="image-loading" v-cloak>
            <i class="fas fa-spinner fa-pulse fa-2x"></i>
            <div class="iransans form-text text-center">بارگذاری تصویر...</div>
        </div>
        <img v-show="image_loaded === 1" :id="id" alt="همیاران شمال شرق" src="" :class="css_class" v-cloak>
        <div v-show="image_error === 1" class="image-error" v-cloak>
            <i class="fa fa-link-slash fa-2x text-muted red-color"></i>
        </div>
        <slot v-if="image_loaded"></slot>
    </div>
</template>

<script>
export default {
    name: "VueImage",
    props: ["img_src","css_class","id","error_image"],
    data(){
        return{
            image_error: 0,
            image_loading: 1,
            image_loaded: 0
        }
    },
    mounted() {
        const self = this;
        let image = document.getElementById(this.id);
        image.onload = function (){
            self.$data.image_error = 0;
            self.$data.image_loading = 0;
            self.$data.image_loaded = 1;
        }
        image.onerror = function (){
            self.$data.image_error = 1;
            self.$data.image_loading = 0;
            self.$data.image_loaded = 0;
        }
        image.src = this.img_src;
    }
}
</script>


<style>
.image-error,.image-loading{
    display: flex;
    align-items: center;
    justify-content: center;
    background: #eeeeee;
    padding: 1.5rem 2rem;
}
</style>
