<template>
    <div id="box" class="rtl w-100 d-flex flex-row align-items-center justify-content-center pb-3">
        <div v-for="(item,index) in names" class="d-flex flex-column align-items-center justify-content-between item">
            <i :class="get_i_class(index)"></i>
            <span class="iranyekan text-center" :class="get_t_class(index)">{{ names[index] }}</span>
        </div>
    </div>
</template>

<script>
export default {
    name: "Progress",
    props:["step_count","current","names"],
    methods:{
        get_i_class(index){
            ++index;
            if (index === this.current)
                return "fa fa-circle-dot fa-2x yellow-color";
            else if (index < this.current)
                return "fa fa-check-circle fa-2x green-color";
            else
                return "fa fa-circle-question fa-2x mid-white-color";
        },
        get_t_class(index){
            ++index;
            if (index === this.current)
                return "white-color";
            else if (index < this.current)
                return "text-muted";
            else
                return "text-muted";
        }
    }
}
</script>

<style scoped>
.item{
    height: 70px;
}
.item:not(:last-child){
    margin-left: 2rem;
}
.item i{
    height: 50%;
}
.item span{
    height: 50%;
}
@media screen and (max-width: 800px){
    .item{
        height: 80px;
        margin-left: 0;
        flex: 1 1 0px;
    }
    .item:not(:last-child){
        margin-left: 0.75rem;
    }
    .item i{
        height: 32%;
    }
    .item span{
        height: 68%;
    }
    #box{
        justify-content: space-between!important;
    }
}
</style>
