<template>
    <div class="loading_window">
        <div class="bg-menu loading_box d-flex flex-column justify-content-center align-items-center p-3 rounded">
            <img src="/images/upload.gif" style="width: 130px;height: auto;margin-bottom: 1rem">
            <div class="d-flex align-items-center justify-content-center w-100">
                <i class="fas fa-sync fa-spin fa-1-4x ml-2"></i>
                <span class="iransans font-weight-bold">در حال بارگذاری مدارک...</span>
            </div>
            <span class="iransans text-muted mt-1">لطفا تا پایان عملیات، صفحه مرورگر را نبندید</span>
        </div>
    </div>
</template>

<script>
export default {
    name: "UploadLoading",
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
