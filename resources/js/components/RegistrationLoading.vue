<template>
    <div class="loading_window">
        <div class="bg-transparent d-flex flex-column justify-content-between align-items-center">
            <i class="fa-sharp fa-solid fa-spinner-third white-color fa-4x fa-spin"></i>
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
