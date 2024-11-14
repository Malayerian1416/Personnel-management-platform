<template>
    <div class="loading_window">
        <div class="bg-menu loading_box d-flex flex-column justify-content-between align-items-center p-3 rounded">
            <i class="fa-duotone fa-loader fa-4x fa-spin"></i>
            <span class="iransans mt-3" style="font-weight: bold;user-select: none">در حال پردازش اطلاعات...</span>
        </div>
    </div>
</template>

<script>
export default {
    name: "Loading",
    props: ["show"]
}
</script>

<style scoped>
.loading_window{
    position: absolute;
    width: 100%;
    height: 100%;
    overflow: hidden;
    display: flex;
    justify-content: center;
    align-items: center;
    flex-direction: column;
    background: rgba(0, 0, 0, 0.5);
    z-index: 10000;
    top:0;
    right:0
}
.loading_box{
    width: calc(100vw / 5);
}
@media screen and (max-width: 768px){
    .loading_box{
        width: 90vw;
    }
}
</style>
